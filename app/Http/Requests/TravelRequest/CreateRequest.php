<?php

namespace App\Http\Requests\TravelRequest;

use App\Enums\TravelRequest\Uf;
use App\Rules\VerifyDestiny;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'state' => ['required',  'string', Rule::in(Uf::values())],
            'city' => ['required',  'string', new VerifyDestiny()],
            'departed_at' => ['required', 'integer', 'lt:returned_at'],
            'returned_at' => ['required', 'integer', 'gte:'.now()->addDay()->timestamp],
        ];
    }
}
