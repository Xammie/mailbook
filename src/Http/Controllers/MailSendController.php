<?php

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Http\Request;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\MailableItem;

class MailSendController
{
    public function __invoke(Request $request): string
    {
        if (! config('mailbook.send')) {
            abort(404);
        }

        $email = $request->get('email');

        if (! is_string($email)) {
            abort(404);
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            abort(400);
        }

        $class = $request->get('class');

        if (! is_string($class)) {
            abort(404);
        }

        /** @var ?string $variant */
        $variant = $request->get('variant') ?? null;

        Mailbook::setLocale($request->get('locale'));

        $mailables = Mailbook::mailables();

        $current = $mailables->first(fn (MailableItem $mailable) => $mailable->class() === $class);

        if (! $current instanceof MailableItem) {
            abort(400);
        }

        if (! is_null($variant)) {
            $current->selectVariant($variant);
        }

        $current->send($email);

        return redirect()
            ->back()
            ->withSuccess('Successfully sent!');
    }
}
