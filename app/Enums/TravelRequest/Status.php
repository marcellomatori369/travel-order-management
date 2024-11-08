<?php

namespace App\Enums\TravelRequest;

use ArchTech\Enums\Values;

enum Status: string
{
    use Values;

    case APPROVED = 'approved';
    case CANCELED = 'canceled';
    case REQUESTED = 'requested';
}
