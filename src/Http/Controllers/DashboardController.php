<?php

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\MailbookItem;

class DashboardController
{
    public function __invoke(Request $request): View
    {
        $mailables = Mailbook::mailables();
        $style = new HtmlString(File::get(__DIR__ . '/../../../resources/dist/mailbook.css'));

        if ($mailables->isEmpty()) {
            return view('mailbook::error', [
                'error' => 'You have not registered any mailables.',
                'style' => $style,
            ]);
        }

        $current = $mailables->first();

        if ($request->has('selected')) {
            $selected = $mailables->first(fn (MailbookItem $mailable) => $mailable->class === $request->get('selected'));
            $current = $selected ?: $current;
        }

        return view('mailbook::dashboard', [
            'current' => $current,
            'subject' => $current?->subject(),
            'mailables' => $mailables,
            'style' => $style,
        ]);
    }
}
