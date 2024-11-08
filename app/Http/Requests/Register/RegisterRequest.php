<?php

namespace App\Http\Requests\Register;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function prepareForValidation(): void
    {
        $input = $this->all();

        $input['email'] = Str::lower($input['email'] ?? null);

        $this->replace($input);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', Rule::unique(User::class)],
            'password' => ['required', 'confirmed', 'min:5'],
        ];
    }
}
