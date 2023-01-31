<?php

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Http\Request;
use Xammie\Mailbook\Facades\Mailbook;

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

        $current = Mailbook::retrieve(
            class: strval($request->get('class')) ?: null,
            variant: strval($request->get('variant')) ?: null,
            locale: strval($request->get('locale')) ?: null,
            fallback: false,
        );

        if (! $current) {
            abort(404);
        }

        $current->send($email);

        return redirect()
            ->back()
            ->withSuccess('Successfully sent!');
    }
}
