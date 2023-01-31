<?php

namespace Xammie\Mailbook\Http\Controllers;

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
     * @throws MailbookException
     */
    public function __invoke(Request $request): View
    {
        $mailables = Mailbook::mailables();

        /** @var MailableItem $current */
        $current = Mailbook::retrieve(
            class: strval($request->get('selected')) ?: null,
            variant: strval($request->get('variant')) ?: null,
            locale: strval($request->get('locale')) ?: null,
        );

        /** @var array $locales */
        $locales = config('mailbook.locales', []);
        $locale = Mailbook::getLocale() ?? config('app.locale');
        $localeLabel = $locales[$locale] ?? $locale;

        $display = config('mailbook.display_preview') ? $request->get('display') : null;

        return view('mailbook::dashboard', [
            'current' => $current,
            'subject' => $current->subject(),
            'attachments' => $current->attachments(),
            'size' => $current->size(),
            'mailables' => $mailables,
            'display' => $display,
            'locales' => $locales,
            'localeLabel' => $localeLabel,
            'currentLocale' => $locale,
            'meta' => $current->meta(),
            'preview' => route('mailbook.content', [
                'class' => $current->class(),
                'variant' => $current->currentVariant()?->slug,
                'locale' => $locale,
            ]),
            'send' => config('mailbook.send'),
            'style' => new HtmlString(File::get(__DIR__.'/../../../resources/dist/mailbook.css')),
        ]);
    }
}
