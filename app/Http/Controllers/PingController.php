<?php

namespace App\Http\Controllers;

use App\Http\Resources\PingResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PingController
{
    public function show(): JsonResource
    {
        return new PingResource('pong');
    }
}
