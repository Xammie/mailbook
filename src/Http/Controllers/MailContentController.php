<?php

namespace Xammie\Mailbook\Http\Controllers;

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Http\Requests\MailbookRequest;
use Xammie\Mailbook\MailableItem;

class MailContentController
{
    public function __invoke(MailbookRequest $request): string
    {
        $current = Mailbook::retrieve(
            class: $request->class(),
            variant: $request->variant(),
            locale: $request->locale(),
        );

        if (! $current instanceof MailableItem) {
            abort(404);
        }

        return $current->content();
    }
}
