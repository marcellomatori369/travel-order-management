<?php

namespace App\Http\Controllers;

use App\Actions\Login;
use App\Http\Requests\Register\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class RegisterController
{
    public function __construct(private readonly User $user)
    {
    }

    public function register(RegisterRequest $request): JsonResource
    {
        $user = $this->user->create($request->validated());
        $token = Login::run($user);

        return new JsonResource($token);
    }
}
