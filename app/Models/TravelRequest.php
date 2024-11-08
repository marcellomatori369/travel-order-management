<?php

namespace App\Models;

use App\Enums\TravelRequest\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'travel_requests';

    protected $fillable = [
        'created_at',
        'departed_at',
        'destiny',
        'returned_at',
        'status',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'timestamp',
            'deleted_at' => 'timestamp',
            'departed_at' => 'timestamp',
            'status' => Status::class,
            'returned_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function departedAt(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Carbon::createFromTimestamp($value)->format('Y-m-d H:i:s'),
        );
    }

    protected function returnedAt(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Carbon::createFromTimestamp($value)->format('Y-m-d H:i:s'),
        );
    }
}
