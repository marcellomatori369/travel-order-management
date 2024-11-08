<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Internal',
            'email' => 'internal@onfly.com',
            'password' => 'Pass1234',
        ]);
    }
}