<?php

namespace App\Actions;

use App\Models\User;
use App\Services\AuthenticationService;
use Lorisleiva\Actions\Concerns\AsObject;
use Namshi\JOSE\SimpleJWS;

class Login
{
    use AsObject;

    public function handle(User $user): array
    {
        $service = new AuthenticationService($user);

        $token = $service->token();
        $payload = SimpleJWS::load($token)->getPayload();

        return [
            'id' => $payload['jti'],
            'expires_at' => $payload['exp'],
            'token' => $token,
        ];
    }
}
