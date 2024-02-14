<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Contracts\View\View;
use Xammie\Mailbook\Data\MailableItem;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Http\Requests\MailbookRequest;
use Xammie\Mailbook\Support\FakeSeedGenerator;

class MailContentController
{
    public function __construct(
        private FakeSeedGenerator $fakeSeedGenerator,
    ) {
    }

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

        $this->fakeSeedGenerator->restoreSeed($request->seed());

        return view('mailbook::content', [
            'content' => $current->content(),
        ]);
    }
}
