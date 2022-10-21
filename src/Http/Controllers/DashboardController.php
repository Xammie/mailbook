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

        /** @var array $locales */
        $locales = config('mailbook.locales', []);
        $locale = Mailbook::setLocale($request->get('locale')) ?? config('app.locale');

        $localeLabel = $locales[$locale] ?? $locale;

        /** @var MailableItem $item */
        $item = $mailables->first();

        if ($request->has('selected')) {
            $selected = $mailables->first(fn (MailableItem $mailable) => $mailable->class() === $request->get('selected'));
            $item = $selected ?: $item;
        }

        if ($request->has('variant')) {
            $item->selectVariant(strval($request->get('variant')));
        }

        $display = config('mailbook.display_preview') ? $request->get('display') : null;

        // @phpstan-ignore-next-line
        return view('mailbook::dashboard', [
            'current' => $item,
            'subject' => $item->subject(),
            'from' => $item->from(),
            'to' => $item->to(),
            'replyTo' => $item->replyTo(),
            'cc' => $item->cc(),
            'bcc' => $item->bcc(),
            'attachments' => $item->attachments(),
            'size' => $item->size(),
            'mailables' => $mailables,
            'display' => $display,
            'locales' => $locales,
            'localeLabel' => $localeLabel,
            'currentLocale' => $locale,
            'preview' => route('mailbook.content', [
                'class' => $item->class(),
                'variant' => $item->currentVariant()?->slug,
                'locale' => $locale,
            ]),
            'style' => new HtmlString(File::get(__DIR__.'/../../../resources/dist/mailbook.css')),
        ]);
    }
}
