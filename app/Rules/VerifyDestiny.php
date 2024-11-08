<?php

namespace App\Rules;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

class VerifyDestiny implements DataAwareRule, ValidationRule
{
    private array $data = [];
    private const BRASIL_API_URL = 'https://brasilapi.com.br/api/ibge/municipios/v1/';

    public function setData($data): DataAwareRule
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_null($state = $this->data['state'] ?? null)) {
            return;
        }

        try {
            $response = app(Client::class)->get(self::BRASIL_API_URL.$state.'?providers=dados-abertos-br,gov,wikipedia');

            $body = new Collection(json_decode($response->getBody(), true));

            $body = $body->filter(fn ($item) => $item['nome'] == Str::upper($value))->first();

            if (isset($body)) {
                return;
            }
        } catch (Throwable) {
        }

        $fail(__('validation.invalid'));
    }
}
