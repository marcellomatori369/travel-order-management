<?php

namespace App\Http\Controllers;

use App\Enums\TravelRequest\Status;
use App\Http\Queries\TravelRequestQuery;
use App\Http\Requests\TravelRequest\CreateRequest;
use App\Http\Requests\TravelRequest\UpdateRequest;
use App\Http\Resources\TravelRequestResource;
use App\Models\TravelRequest;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelRequestController extends Controller
{
    public function __construct(private readonly Factory $auth)
    {
    }

    public function index(TravelRequestQuery $query, Request $request): JsonResource
    {
        $this->authorize('viewAny', TravelRequest::class);

        $travelRequests = $query
            ->when(! $this->auth->user()->is_internal, fn ($query) => $query->where('user_id', $this->auth->user()->id))
            ->simplePaginate($request->get('limit', config('app.pagination_limit')))
            ->appends($request->query());

        return TravelRequestResource::collection($travelRequests);
    }

    public function show(TravelRequestQuery $query, TravelRequest $travelRequest): JsonResource
    {
        $this->authorize('view', [TravelRequest::class, $travelRequest]);

        $travelRequest = $query->where('id', $travelRequest->id)->firstOrFail();

        return new TravelRequestResource($travelRequest);
    }

    public function store(CreateRequest $request): JsonResource
    {
        $this->authorize('create', TravelRequest::class);

        $input = $request->validated();

        $travelRequest = $this->auth->user()->travelRequests()->create([
            'status' => Status::REQUESTED,
            'destiny' => "{$input['city']}, {$input['state']}",
            ...$input,
        ]);

        return new TravelRequestResource($travelRequest);
    }

    public function update(UpdateRequest $request, TravelRequestQuery $query, TravelRequest $travelRequest): JsonResource
    {
        $this->authorize('update', [TravelRequest::class, $travelRequest]);

        $query
            ->where('id', $travelRequest->id)
            ->firstOrFail()
            ->update($request->validated());

        return new TravelRequestResource($travelRequest->refresh());
    }
}
