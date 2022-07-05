<?php

namespace Xammie\Mailbook\Http\Controllers;

use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Mailable;

class RenderMailableController
{
    public function __invoke(string $class): string
    {
        $mailables = Mailbook::mailables();

        if ($mailables->isEmpty()) {
            abort(500);
        }

        $current = $mailables->first(fn (Mailable $mailable) => $mailable->class === $class);

        if (! $current) {
            abort(400);
        }

        return $current->render();
    }
}
