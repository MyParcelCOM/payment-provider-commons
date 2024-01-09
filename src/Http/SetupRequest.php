<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Http;

use Illuminate\Foundation\Http\FormRequest;

class SetupRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data.broker_id'    => 'required|string|uuid',
            'data.settings'     => 'array',
            'data.redirect_url' => 'url',
        ];
    }

    public function brokerId(): string
    {
        return $this->input('data.broker_id');
    }

    public function redirectUrl(): ?string
    {
        return $this->input('data.redirect_url');
    }
}
