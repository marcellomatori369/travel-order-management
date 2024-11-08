<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\ControllerTestCase;

class RegisterControllerTest extends ControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function test_checkout_register_endpoint_structure(): void
    {
        $params = [
            'name' => 'Marcello Matori',
            'email' => 'marcello.matori@onfly.com',
            'password' => 'Pass1234',
            'password_confirmation' => 'Pass1234',
        ];

        $response = $this->post(route('v1.register'), $params);

        unset($params['password_confirmation'], $params['password']);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', $params);

        $this->assertShowStructure($response, [
            'id',
            'expires_at',
            'token',
        ]);
    }

    #[DataProvider('registerValidationProvider')]
    public function test_checkout_register_endpoint_validation(array $messages, array $data): void
    {
        User::factory()->create(['email' => 'marcello.matori@onfly.com']);

        $response = $this->post(route('v1.register', $data));

        foreach($messages as $key => $message) {
            $this->assertContains($message, $response['errors'][$key] ?? []);
        }
    }

    public static function registerValidationProvider(): array
    {
        return [
            [
                [
                    'name' => 'required',
                    'email' => 'required',
                    'password' => 'required',
                ],
                [
                    'name' => null,
                    'email' => null,
                    'password' => null,
                ],
            ],
            [
                [
                    'name' => 'too-long',
                    'email' => 'invalid',
                ],
                [
                    'name' => Str::random(256),
                    'email' => 'test',
                ],
            ],
            [
                [
                    'name' => 'too-short',
                    'password' => 'too-short',
                ],
                [
                    'name' => '12',
                    'password' => '1234',
                ],
            ],
            [
                [
                    'email' => 'already-in-use',
                    'password' => 'confirmation-mismatch',
                ],
                [
                    'name' => 'Marcello Matori',
                    'email' => 'marcello.matori@onfly.com',
                    'password' => 'Pass1234',
                ],
            ],
        ];
    }
}
