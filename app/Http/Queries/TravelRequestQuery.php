<?php

namespace App\Http\Queries;

use App\Models\TravelRequest;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TravelRequestQuery extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct(TravelRequest::query());

        $this->allowedFilters([AllowedFilter::exact('status')]);

        $this->allowedSorts([
            'id',
            'created_at',
        ]);

        $this->defaultSorts('-id');
    }
}
