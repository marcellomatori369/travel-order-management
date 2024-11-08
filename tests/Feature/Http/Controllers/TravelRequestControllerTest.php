<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\TravelRequest\Status;
use App\Enums\TravelRequest\Uf;
use App\Models\TravelRequest;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\ControllerTestCase;

class TravelRequestControllerTest extends ControllerTestCase
{
    private User $internalUser;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->internalUser = User::factory()->create([
            'name' => 'Marcello Matori',
            'email' => 'marcello.matori@onfly.com',
        ]);
    }

    public function test_index_travel_request_endpoint_structure(): void
    {
        TravelRequest::factory()->for($this->internalUser)->createMany([
            ['status' =>Status::REQUESTED],
            ['status' => Status::APPROVED],
        ]);

        $response = $this->actingAs($this->internalUser)->get(route('v1.travel-requests.index'));

        $response->assertJsonCount(2, 'data');
        $this->assertIndexStructure($response, $this->travelRequestItemStructure());
    }

    public function test_index_travel_request_endpoint_structure_users_permission(): void
    {
        $requestedUser = User::factory()->create();
        $randomUser = User::factory()->create();

        TravelRequest::factory()->for($requestedUser)->createMany([
            ['status' =>Status::REQUESTED],
            ['status' => Status::APPROVED],
        ]);

        $this->actingAs($requestedUser)->get(route('v1.travel-requests.index'))->assertJsonCount(2, 'data');
        $this->actingAs($this->internalUser)->get(route('v1.travel-requests.index'))->assertJsonCount(2, 'data');
        $this->actingAs($randomUser)->get(route('v1.travel-requests.index'))->assertJsonCount(0, 'data');
    }

    public function test_index_travel_request_endpoint_structure_filter_by_status(): void
    {
        TravelRequest::factory()->for($this->internalUser)->createMany([
            ['status' =>Status::REQUESTED],
            ['status' => Status::APPROVED],
            ['status' => Status::APPROVED],
        ]);

        $response = $this->actingAs($this->internalUser)->get(route('v1.travel-requests.index', [
            'filter[status]' => Status::REQUESTED->value,
        ]));

        $response->assertJsonCount(1, 'data');
        $this->assertIndexStructure($response, $this->travelRequestItemStructure());
    }

    public function test_index_travel_request_endpoint_structure_sort_by_created_at(): void
    {
        $firstTravelRequest = TravelRequest::factory()->for($this->internalUser)->create([
            'status' =>Status::REQUESTED, 'created_at' => now(),
        ]);

        $secondTravelRequest = TravelRequest::factory()->for($this->internalUser)->create([
            'status' => Status::APPROVED, 'created_at' => now()->subMonth(),
        ]);

        $thirdTravelRequest = TravelRequest::factory()->for($this->internalUser)->create([
            'status' => Status::APPROVED, 'created_at' => now()->subMonths(2),
        ]);

        $this->actingAs($this->internalUser)->get(route('v1.travel-requests.index', [
            'sort' => 'created_at'
        ]))
            ->assertJsonPath('data.0.id', $thirdTravelRequest->id)
            ->assertJsonPath('data.1.id', $secondTravelRequest->id)
            ->assertJsonPath('data.2.id', $firstTravelRequest->id);

        $this->actingAs($this->internalUser)->get(route('v1.travel-requests.index', [
            'sort' => '-created_at'
        ]))
            ->assertJsonPath('data.0.id', $firstTravelRequest->id)
            ->assertJsonPath('data.1.id', $secondTravelRequest->id)
            ->assertJsonPath('data.2.id', $thirdTravelRequest->id);
    }

    public function test_show_travel_request_endpoint_structure(): void
    {
        $travelRequest = TravelRequest::factory()->for($this->internalUser)->create();
        TravelRequest::factory()->for($this->internalUser)->create();

        $response = $this->actingAs($this->internalUser)->get(route('v1.travel-requests.show', [
            'travelRequest' => $travelRequest
        ]));

        $this->assertEquals($response->json('data.id'), $travelRequest->id);
        $this->assertShowStructure($response, $this->travelRequestItemStructure());
    }

    public function test_store_travel_request_endpoint_structure(): void
    {
        $mock = $this->partialMock(Client::class);
        $mock->shouldReceive('get')->once()->andReturn(new Response(body: json_encode([['nome' => 'BETIM']])));

        $params = [
            'state' => 'MG',
            'city' => 'Betim',
            'departed_at' => now()->addDay()->getTimestamp(),
            'returned_at' => now()->addWeek()->getTimestamp(),
        ];

        $response = $this->actingAs($this->internalUser)->post(route('v1.travel-requests.store'), $params);

        $params['destiny'] = "{$params['city']}, {$params['state']}";

        unset($params['city'], $params['state']);

        $params['departed_at'] = Carbon::parse($params['departed_at'])->toDateTimeString();
        $params['returned_at'] = Carbon::parse($params['returned_at'])->toDateTimeString();

        $this->assertStoreStructure($response, $this->travelRequestItemStructure());
        $this->assertDatabaseHas('travel_requests', $params);
    }

    #[DataProvider('travelRequestStoreValidationProvider')]
    public function test_travel_request_endpoint_validation(array $messages, array $data): void
    {
        $mock = $this->partialMock(Client::class);
        $mock->shouldReceive('get')->andReturn(new Response(body: json_encode([])));

        $response = $this->actingAs($this->internalUser)->post(route('v1.travel-requests.store'), $data);

        foreach($messages as $key => $message) {
            $this->assertContains($message, $response['errors'][$key] ?? []);
        }
    }

    public static function travelRequestStoreValidationProvider(): array
    {
        return [
            [
                [
                    'state' => 'required',
                    'city' => 'required',
                    'departed_at' => 'required',
                    'returned_at' => 'required',
                ],
                [
                    'state' => null,
                    'city' => null,
                    'departed_at' => null,
                    'returned_at' => null,
                ],
            ],
            [
                [
                    'state' => 'invalid',
                    'city' => 'invalid',
                    'departed_at' => 'integer-expected',
                    'returned_at' => 'integer-expected',
                ],
                [
                    'state' => 'teste',
                    'city' => 'teste',
                    'departed_at' => 'teste',
                    'returned_at' => 'teste',
                ],
            ],
            [
                [
                    'departed_at' => 'invalid',
                    'returned_at' => 'invalid',
                ],
                [
                    'state' => Uf::MG->value,
                    'city' => 'Betim',
                    'departed_at' => now()->addDay()->getTimestamp(),
                    'returned_at' => now()->getTimestamp(),
                ],
            ],
        ];
    }

    public function test_update_travel_request_endpoint_structure(): void
    {
        $travelRequest = TravelRequest::factory()->for($this->internalUser)->create(['status' => Status::REQUESTED]);

        $response = $this->actingAs($this->internalUser)->put(route('v1.travel-requests.update', [
            'travelRequest' => $travelRequest,
        ]), ['status' => Status::APPROVED->value]);

        $this->assertUpdateStructure($response, $this->travelRequestItemStructure());
        $this->assertDatabaseHas('travel_requests', [
            'id' => $travelRequest->id,
            'status' => Status::APPROVED->value,
        ]);
    }

    public function test_update_travel_request_endpoint_structure_with_invalid_status(): void
    {
        $travelRequest = TravelRequest::factory()->for($this->internalUser)->create(['status' => Status::REQUESTED]);

        $this->actingAs($this->internalUser)
            ->put(route('v1.travel-requests.update', ['travelRequest' => $travelRequest]), ['status' => Status::REQUESTED->value])
            ->assertInvalid(['status' => 'invalid']);

        $this->actingAs($this->internalUser)
            ->put(route('v1.travel-requests.update', ['travelRequest' => $travelRequest]), ['status' => 'teste'])
            ->assertInvalid(['status' => 'invalid']);

        $travelRequest->update(['status' => Status::CANCELED]);

        $this->actingAs($this->internalUser)
            ->put(route('v1.travel-requests.update', ['travelRequest' => $travelRequest]), ['status' => Status::APPROVED->value])
            ->assertInvalid(['status' => 'not-allowed']);
    }

    public function travelRequestItemStructure(): array
    {
        return [
            'created_at',
			'departed_at',
			'destiny',
			'id',
			'returned_at',
			'status',
			'updated_at',
        ];
    }
}
