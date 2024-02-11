<?php

namespace Xammie\Mailbook\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @internal
 */
class MailbookRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }

    public function class(): ?string
    {
        $class = $this->get('class', $this->get('selected'));

        if (is_string($class)) {
            return $class;
        }

        return null;
    }

    public function variant(): ?string
    {
        $variant = $this->get('variant');

        if (is_string($variant)) {
            return $variant;
        }

        return null;
    }

    public function locale(): ?string
    {
        $locale = $this->get('locale');

        if (is_string($locale)) {
            return $locale;
        }

        return null;
    }

    public function seed(): ?string
    {
        $seed = $this->get('s');

        if (is_string($seed)) {
            return $seed;
        }

        return null;
    }
}
