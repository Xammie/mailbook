<?php

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use RuntimeException;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Http\Requests\MailbookRequest;
use Xammie\Mailbook\MailableItem;

class MailSendController
{
    public function __invoke(MailbookRequest $request): RedirectResponse
    {
        if (! config('mailbook.send')) {
            abort(404);
        }

        $current = Mailbook::retrieve(
            class: $request->class(),
            variant: $request->variant(),
            locale: $request->locale(),
        );

        if (! $current instanceof MailableItem) {
            abort(404);
        }

        $to = config('mailbook.send_to');

        if (is_array($to)) {
            $to = $to[0];
        }

        if (! $to) {
            throw new RuntimeException('invalid config mailbook.send_to should be string');
        }

        $current->send($to);

        return redirect()->back();
    }
}
