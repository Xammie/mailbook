<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use Xammie\Mailbook\Data\MailableItem;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Http\Requests\MailbookRequest;
use Xammie\Mailbook\MailbookConfig;
use Xammie\Mailbook\Support\FakeSeedGenerator;

class DashboardController
{
    public function __construct(
        private FakeSeedGenerator $fakeSeedGenerator,
        private MailbookConfig $config,
    ) {
    }

    public function __invoke(MailbookRequest $request): View
    {
        $mailables = Mailbook::groupedMailables();

        /** @var MailableItem $current */
        $current = Mailbook::retrieve(
            class: $request->class(),
            variant: $request->variant(),
            locale: $request->locale(),
            fallback: true
        );

        /** @var array $locales */
        $locales = config('mailbook.locales', []);
        $locale = Mailbook::getLocale() ?? config('app.locale');

        $display = config('mailbook.display_preview') ? $request->get('display') : null;

        return view('mailbook::dashboard', [
            'current' => $current,
            'subject' => $current->subject(),
            'attachments' => $current->attachments(),
            'size' => $current->size(),
            'items' => $mailables,
            'display' => $display,
            'locales' => $locales,
            'currentLocale' => $locale,
            'meta' => $current->meta(),
            'preview' => route('mailbook.content', [
                'class' => $current->class(),
                'variant' => $current->currentVariant()?->slug,
                'locale' => $locale,
                's' => $this->fakeSeedGenerator->getCurrentSeed(),
            ]),
            'send' => config('mailbook.send'),
            'send_to' => $this->config->getSendTo(),
            'style' => new HtmlString(File::get(__DIR__.'/../../../resources/dist/mailbook.css')),
        ]);
    }
}
