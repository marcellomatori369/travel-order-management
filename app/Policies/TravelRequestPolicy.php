<?php

namespace App\Policies;

use App\Models\Channel;
use App\Models\PlatformProvider;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TravelRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TravelRequest $travelRequest): bool
    {
        return $travelRequest->user()->is($user) || $user->is_internal;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TravelRequest $travelRequest): bool
    {
        return $user->is_internal;
    }
}
