<?php

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Mailable;

class DashboardController
{
    public function __invoke(Request $request): View
    {
        $mailables = Mailbook::mailables();

        if ($mailables->isEmpty()) {
            return view('mailbook::error', [
                'error' => 'You have not registered any mailables.',
            ]);
        }

        $current = $mailables->first();

        if ($request->has('selected')) {
            $selected = $mailables->first(fn (Mailable $mailable) => $mailable->class === $request->get('selected'));
            $current = $selected ?: $current;
        }

        return view('mailbook::dashboard', [
            'current' => $current,
            'subject' => $current->subject(),
            'mailables' => $mailables,
        ]);
    }
}
