<?php

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Http\Request;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\MailableItem;

class MailContentController
{
    public function __invoke(Request $request): string
    {
        $class = $request->get('class');

        if (! is_string($class)) {
            abort(404);
        }

        /** @var ?string $variant */
        $variant = $request->get('variant') ?? null;

        Mailbook::setLocale($request->get('locale'));

        $mailables = Mailbook::mailables();

        if ($mailables->isEmpty()) {
            abort(500);
        }

        $current = $mailables->first(fn (MailableItem $mailable) => $mailable->class() === $class);

        if (! $current instanceof MailableItem) {
            abort(400);
        }

        if (! is_null($variant)) {
            $current->selectVariant($variant);
        }

        return $current->content();
    }
}
