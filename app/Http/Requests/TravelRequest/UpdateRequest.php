<?php

namespace App\Http\Requests\TravelRequest;

use App\Enums\TravelRequest\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => [
                'bail',
                'required',
                Rule::in([Status::APPROVED->value, Status::CANCELED->value]),
                Rule::prohibitedIf(fn () => $this->travelRequest->status !== Status::REQUESTED),
            ],
        ];
    }
}
