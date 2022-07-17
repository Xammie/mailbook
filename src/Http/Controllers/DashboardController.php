<?php

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use Xammie\Mailbook\Exceptions\MailbookException;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\MailableItem;

class DashboardController
{
    /**
     * @throws FileNotFoundException
     * @throws MailbookException
     */
    public function __invoke(Request $request): View
    {
        $mailables = Mailbook::mailables();

        if ($mailables->isEmpty()) {
            throw new MailbookException('No mailbook mailables registered');
        }

        /** @var MailableItem|null $current */
        $current = $mailables->first();

        if ($request->has('selected')) {
            $selected = $mailables->first(fn (MailableItem $mailable) => $mailable->class() === $request->get('selected'));
            $current = $selected ?: $current;
        }

        if ($request->has('variant') && $current->hasVariant($request->get('variant'))) {
            $current->selectVariant($request->get('variant'));
        }

        return view('mailbook::dashboard', [
            'current' => $current,
            'subject' => $current?->subject(),
            'mailables' => $mailables,
            'style' => new HtmlString(File::get(__DIR__.'/../../../resources/dist/mailbook.css')),
        ]);
    }
}
