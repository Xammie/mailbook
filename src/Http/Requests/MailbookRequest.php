<?php

namespace Xammie\Mailbook\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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

    public function email(): string
    {
        $email = $this->get('email');

        if (! is_string($email)) {
            abort(404);
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            abort(400);
        }

        return $email;
    }
}
