<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class AuthenticationService
{
    public function __construct(private readonly User $user)
    {
    }

    public function token(): string
    {
        $expiresAtInMinutes = now()->diffInMinutes(Carbon::createFromTimestamp(now()->addHour()->timestamp));

        $token = auth()
            ->setTTL($expiresAtInMinutes)
            ->claims(['user_id' => $this->user->id]);

        return $token->login($this->user);
    }
}
