<?php

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Http\Request;
use Xammie\Mailbook\Facades\Mailbook;

class MailContentController
{
    public function __invoke(Request $request): string
    {
        $current = Mailbook::retrieve(
            class: strval($request->get('class')) ?: null,
            variant: strval($request->get('variant')) ?: null,
            locale: strval($request->get('locale')) ?: null,
            fallback: false,
        );

        if (! $current) {
            abort(404);
        }

        return $current->content();
    }
}
