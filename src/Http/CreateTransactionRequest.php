<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Http;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data.myparcelcom_payment_id' => 'required|string|uuid',
            'data.price.amount'           => 'required|integer',
            'data.price.currency'         => 'required|string|size:3',
            'data.description'            => 'string',
            'data.locale'                 => 'required|string|regex:/^[a-z]{2}_[A-Z]{2}$/',
            'data.redirect_url'           => 'required|url',
            'data.cancel_url'             => 'required|url',
            'meta'                        => 'array',
        ];
    }

    public function myparcelcomPaymentId(): string
    {
        return $this->input('data.myparcelcom_payment_id');
    }

    public function priceAmount(): int
    {
        return (int) $this->input('data.price.amount');
    }

    public function priceCurrency(): string
    {
        return $this->input('data.price.currency');
    }

    public function description(): ?string
    {
        return $this->input('data.description');
    }

    public function locale(): string
    {
        return $this->input('data.locale');
    }

    public function redirectUrl(): string
    {
        return $this->input('data.redirect_url');
    }

    public function cancelUrl(): string
    {
        return $this->input('data.cancel_url');
    }

    public function meta(): array
    {
        return $this->input('meta', []);
    }
}
