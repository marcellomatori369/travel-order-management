<?php

namespace App\Http\Controllers;

use App\Actions\Login;
use App\Exceptions\Http\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Login\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginController extends Controller
{
    public function __construct(private readonly User $user)
    {
    }

    public function login(LoginRequest $request): JsonResource|JsonResponse
    {
        $input = $request->validated();

        $credentials = [
            'email' => $input['email'],
            'password' => $input['password'],
        ];

        $user = $this->user->where('email', $credentials['email'])->first();

        if (! is_null($user) && auth()->validate($credentials)) {
            return new JsonResource(Login::run($user));
        }

        throw new NotFoundException();
    }
}
