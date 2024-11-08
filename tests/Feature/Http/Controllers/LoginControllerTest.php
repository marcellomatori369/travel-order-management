<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\ControllerTestCase;

class LoginControllerTest extends ControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        User::factory()->create([
            'name' => 'Marcello Matori',
            'email' => 'marcello.matori@onfly.com',
        ]);
    }

    public function test_login_endpoint_structure(): void
    {
        $response = $this->post(route('v1.login'), [
            'email' => 'marcello.matori@onfly.com',
            'password' => 'Pass1234',
        ]);

        $this->assertShowStructure($response, [
            'id',
            'expires_at',
            'token',
        ]);
    }

    public function test_login_endpoint_structure_but_user_not_found(): void
    {
        $this->post(route('v1.login'), [
            'email' => 'marcello.matori@teste.com',
            'password' => 'Pass1234',
        ])->assertNotFound();
    }

    #[DataProvider('loginValidationProvider')]
    public function test_login_endpoint_validation(array $messages, array $data): void
    {
        $response = $this->post(route('v1.login', $data));

        foreach($messages as $key => $message) {
            $this->assertContains($message, $response['errors'][$key] ?? []);
        }
    }

    public static function loginValidationProvider(): array
    {
        return [
            [
                [
                    'email' => 'required',
                    'password' => 'required',
                ],
                [
                    'email' => null,
                    'password' => null,
                ],
            ],
            [
                [
                    'email' => 'invalid',
                ],
                [
                    'email' => 'test',
                    'password' => 'Pass1234',
                ],
            ],
        ];
    }
}
