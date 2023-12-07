<?php

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Http\Requests\MailbookRequest;
use Xammie\Mailbook\MailableItem;
use Xammie\Mailbook\MailbookConfig;

class MailSendController
{
    public function __construct(private MailbookConfig $config)
    {
    }

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

        $current->send($this->config->getSendToStrict());

        return redirect()->back();
    }
}
