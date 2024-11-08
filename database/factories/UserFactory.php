<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->name(),
            'password' => 'Pass1234',
        ];
    }

    public function internal()
    {
        return $this->state(fn (array $attributes) => [
            'email' => Str::slug($attributes['name']).'-'.random_int(1, 999).'@onfly.com',
        ]);
    }
}
