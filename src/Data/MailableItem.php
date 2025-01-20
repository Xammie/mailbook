<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Data;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Mail\Mailer;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Str;
use Xammie\Mailbook\Exceptions\MailbookException;
use Xammie\Mailbook\Facades\Mailbook as MailbookFacade;
use Xammie\Mailbook\MailableResolver;
use Xammie\Mailbook\Support\Format;
use Xammie\Mailbook\Traits\HasCategory;
use Xammie\Mailbook\Traits\HasLabel;

class MailableItem
{
    use HasCategory;
    use HasLabel;

    /**
     * @var Collection<int, MailableVariant>
     */
    private Collection $variants;

    private ?string $selectedVariant = null;

    private ?MailableResolver $resolver = null;

    public function __construct(public string|Closure|Mailable|Notification $closure, public mixed $notifiable = null)
    {
        $this->variants = collect();
    }

    public function variant(string $label, Closure $variant): self
    {
        $slug = Str::slug($label);

        if ($this->hasVariant($slug)) {
            throw new MailbookException(sprintf('Variant %s (%s) already exists', $label, $slug));
        }

        $this->variants->push(new MailableVariant($label, $slug, $variant, $this->notifiable));

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

    public function selectVariant(?string $variant): self
    {
        if ($variant && $this->hasVariant($variant)) {
            $this->selectedVariant = $variant;
        }

        return $this;
    }

    public function subject(): ?string
    {
        try {
            return $this->resolve()->subject();
        } catch (BindingResolutionException) {
            return null;
        }
    }

    public function from(): array
    {
        return $this->resolve()->from();
    }

    public function to(): array
    {
        return $this->resolve()->to();
    }

    public function replyTo(): array
    {
        return $this->resolve()->replyTo();
    }

    public function cc(): array
    {
        return $this->resolve()->cc();
    }

    public function bcc(): array
    {
        return $this->resolve()->bcc();
    }

    public function theme(): ?string
    {
        /** @var object $instance */
        $instance = $this->resolveInstance();

        return $instance->theme ?? null;
    }

    public function content(): string
    {
        return $this->resolve()->content();
    }

    public function size(): string
    {
        return Format::bytesToHuman(strlen($this->content()));
    }

    public function attachments(): array
    {
        return $this->resolve()->attachments();
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
        return $this->resolver ??= new MailableResolver($this->closure, $this->notifiable);
    }

    private function resolve(): ResolvedMail
    {
        return $this->variantResolver()->resolve();
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

    private function resolveInstance(): Mailable|Notification
    {
        return $this->variantResolver()->instance();
    }

    public function class(): string
    {
        return $this->resolver()->className();
    }

    public function send(string $email): void
    {
        if (method_exists(app(Mailer::class), 'alwaysTo')) {
            Mail::alwaysTo($email);
        }

        $instance = $this->variantResolver()->instance();
        $locale = MailbookFacade::getLocale();

        if (is_string($locale)) {
            $instance->locale($locale);
        }

        if ($instance instanceof Notification) {
            NotificationFacade::route('mail', $email)->notifyNow($instance);
        } else {
            Mail::to($email)->sendNow($instance);
        }
    }

    public function meta(): array
    {
        return array_filter([
            'Subject' => $this->subject(),
            'From' => $this->from(),
            'Reply To' => $this->replyTo(),
            'To' => $this->to(),
            'Cc' => $this->cc(),
            'Bcc' => $this->bcc(),
            'Theme' => $this->theme(),
        ]);
    }
}
