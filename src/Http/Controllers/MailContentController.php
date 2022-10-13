<?php

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Http\Request;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\MailableItem;

class MailContentController
{
    public function __invoke(Request $request, string $class, ?string $variant = null): string
    {
        $locale = $request->get('locale');

        if (in_array($locale, array_keys(config('mailbook.localization.locales', [])), true)) {
            Mailbook::setLocale($locale);
        }

        $mailables = Mailbook::mailables();

        if ($mailables->isEmpty()) {
            abort(500);
        }

        /** @var MailableItem|null $current */
        $current = $mailables->first(fn (MailableItem $mailable) => $mailable->class() === $class);

        if (! $current) {
            abort(400);
        }

        if (! is_null($variant)) {
            $current->selectVariant($variant);
        }

        return $current->content();
    }
}
