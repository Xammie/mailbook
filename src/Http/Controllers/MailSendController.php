<?php

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailer;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\MailableItem;

class MailSendController
{
    public function __invoke(Mailer $mailer, Request $request): string
    {
        $request->validate([
            'email' => 'email|required',
            'item' => 'required',
        ]);
        /** @var string $item */
        $item = $request->get('item');

        if (! is_subclass_of($item, Mailable::class)) {
            abort(422);
        }
        /** @var MailableItem $mailableItem */
        $mailableItem = Mailbook::mailables()->first(fn (MailableItem $mailableItem) => $mailableItem->class() === $item);
        /** @var Mailable $mailable */
        $mailable = $mailableItem->resolver()->instance();

        $mailer->to($request->email)->send($mailable);

        return redirect()->back()->withSuccess('Successfully sent!');
    }
}
