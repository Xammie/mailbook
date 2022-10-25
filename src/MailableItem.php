<?php

namespace Xammie\Mailbook;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Xammie\Mailbook\Exceptions\MailbookException;
use Xammie\Mailbook\Support\Format;

class MailableItem
{
    use HasMeta;

    private ?string $label = null;

    /**
     * @var Collection<int, MailableVariant>
     */
    private Collection $variants;

    private ?string $selectedVariant = null;

    private ?MailableResolver $resolver = null;

    public function __construct(public string|Closure|Mailable $closure)
    {
        $this->variants = collect(); // @phpstan-ignore-line
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        if (! is_null($this->label)) {
            return $this->label;
        }

        return Str::title(Str::snake(class_basename($this->resolver()->class()), ' '));
    }

    public function variant(string $label, Closure $variant): self
    {
        $slug = Str::slug($label);

        if ($this->hasVariant($slug)) {
            throw new MailbookException(sprintf('Variant %s (%s) already exists', $label, $slug));
        }

        $this->variants->push(new MailableVariant($label, $slug, $variant));

        return $this;
    }

    /**
     * @return Collection<int, MailableVariant>
     */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    private function getVariant(string $slug): ?MailableVariant
    {
        return $this
            ->getVariants()
            ->first(fn (MailableVariant $variant) => $variant->slug === $slug);
    }

    private function hasVariant(string $slug): bool
    {
        return $this->getVariants()
            ->filter(fn (MailableVariant $variant) => $variant->slug === $slug)
            ->isNotEmpty();
    }

    public function hasVariants(): bool
    {
        return $this->variants->isNotEmpty();
    }

    public function selectVariant(string $variant): self
    {
        if ($this->hasVariant($variant)) {
            $this->selectedVariant = $variant;
        }

        return $this;
    }

    public function subject(): string
    {
        try {
            return $this->resolveInstance()->subject ?? 'NULL';
        } catch (BindingResolutionException) {
            return '';
        }
    }

    public function from(): array
    {
        $items = $this->resolveInstance()->from ?? [];

        if (empty($items)) {
            $from = config('mail.from');

            if (is_array($from)) {
                $items = [$from];
            }
        }

        return $this->listOfEmailAddresses($items);
    }

    public function to(): array
    {
        return $this->listOfEmailAddresses($this->resolveInstance()->to ?? []);
    }

    public function replyTo(): array
    {
        return $this->listOfEmailAddresses($this->resolveInstance()->replyTo ?? []);
    }

    public function cc(): array
    {
        return $this->listOfEmailAddresses($this->resolveInstance()->cc ?? []);
    }

    public function bcc(): array
    {
        return $this->listOfEmailAddresses($this->resolveInstance()->bcc ?? []);
    }

    private function listOfEmailAddresses(array $items): array
    {
        return collect($items)
            ->map(fn (array $from) => sprintf('%s <%s>', $from['name'], $from['address']))
            ->filter()
            ->toArray();
    }

    public function theme(): ?string
    {
        return $this->resolveInstance()->theme ?? null;
    }

    public function mailer(): ?string
    {
        return $this->resolveInstance()->mailer ?? null;
    }

    public function content(): string
    {
        return $this->variantResolver()->content();
    }

    public function size(): string
    {
        return Format::bytesToHuman(strlen($this->content()));
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function attachments(): Collection
    {
        /** @var \Illuminate\Mail\Mailable $mailable */
        $mailable = $this->resolveInstance();

        // @phpstan-ignore-next-line
        return collect()
            ->concat($mailable->attachments)
            ->concat($mailable->rawAttachments)
            ->concat($mailable->diskAttachments)
            ->map(fn (array $attachment) => new Attachment($attachment['name']));
    }

    public function is(MailableItem $target): bool
    {
        return $this->class() === $target->class();
    }

    public function currentVariant(): ?MailableVariant
    {
        if (! $this->hasVariants()) {
            return null;
        }

        if (is_null($this->selectedVariant)) {
            return null;
        }

        return $this->getVariant($this->selectedVariant);
    }

    public function resolver(): MailableResolver
    {
        return $this->resolver = $this->resolver ?? new MailableResolver($this->closure);
    }

    public function variantResolver(): MailableResolver
    {
        $current = $this->currentVariant();

        if ($current instanceof MailableVariant) {
            return $current->resolver();
        }

        $firstVariant = $this->variants->first();

        if ($firstVariant instanceof MailableVariant) {
            return $firstVariant->resolver();
        }

        return $this->resolver();
    }

    private function resolveInstance(): Mailable
    {
        return $this->variantResolver()->instance();
    }

    public function class(): string
    {
        return $this->resolver()->class();
    }
}
