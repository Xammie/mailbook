<?php

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Contracts\View\View;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Http\Requests\MailbookRequest;
use Xammie\Mailbook\MailableItem;

class MailContentController
{
    public function __invoke(MailbookRequest $request): View
    {
        $current = Mailbook::retrieve(
            class: $request->class(),
            variant: $request->variant(),
            locale: $request->locale(),
        );

        if (! $current instanceof MailableItem) {
            abort(404);
        }

        if (function_exists('fake') && $request->has('s')) {
            // restore faker seed
            fake()->seed($request->get('s'));
        }

        return view('mailbook::content', [
            'content' => $current->content(),
        ]);
    }
}
