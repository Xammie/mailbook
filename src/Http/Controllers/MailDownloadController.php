<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Xammie\Mailbook\Data\MailableItem;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Http\Requests\MailbookRequest;
use Xammie\Mailbook\Support\FakeSeedGenerator;

class MailDownloadController
{
    public function __construct(
        private FakeSeedGenerator $fakeSeedGenerator,
    ) {}

    public function __invoke(MailbookRequest $request): Response
    {
        if (! config('mailbook.download')) {
            abort(404);
        }

        $current = Mailbook::retrieve(
            class: $request->class(),
            variant: $request->variant(),
            locale: $request->locale(),
        );

        if (! $current instanceof MailableItem) {
            abort(404);
        }

        $this->fakeSeedGenerator->restoreSeed($request->seed());

        return new Response(
            $current->raw(),
            200,
            [
                'Content-Type' => 'message/rfc822',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $this->filename($current)),
            ]
        );
    }

    private function filename(MailableItem $item): string
    {
        $slug = Str::slug((string) $item->subject());

        if ($slug === '') {
            $slug = Str::slug(class_basename($item->class()));
        }

        return $slug.'.eml';
    }
}
