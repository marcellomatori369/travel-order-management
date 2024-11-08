<?php

namespace Database\Factories;

use App\Enums\TravelRequest\Status;
use App\Enums\TravelRequest\Uf;
use Illuminate\Database\Eloquent\Factories\Factory;

class TravelRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => $this->faker->randomElement(Status::values()),
            'destiny' => "{$this->faker->city}, {$this->faker->randomElement(Uf::values())}",
            'departed_at' => now(),
            'returned_at' => now()->addWeek()
        ];
    }

    public function withStatus(Status $status)
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }
}
