<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TravelRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'created_at' => $this->created_at,
            'departed_at' => $this->departed_at,
            'destiny' => $this->destiny,
            'id' => $this->id,
            'returned_at' => $this->returned_at,
            'status' => $this->status,
            'updated_at' => $this->updated_at,
            'user_id' => $this->user_id,
        ];
    }
}
