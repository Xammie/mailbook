<?php

namespace Xammie\Mailbook\Http\Controllers;

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Http\Requests\MailbookRequest;
use Xammie\Mailbook\MailableItem;

class MailSendController
{
    public function __invoke(MailbookRequest $request): string
    {
        if (! config('mailbook.send')) {
            abort(404);
        }

        $email = $request->email();

        $current = Mailbook::retrieve(
            class: $request->class(),
            variant: $request->variant(),
            locale: $request->locale(),
        );

        if (! $current instanceof MailableItem) {
            abort(404);
        }

        $current->send($email);

        return redirect()
            ->back()
            ->withSuccess('Successfully sent!');
    }
}
