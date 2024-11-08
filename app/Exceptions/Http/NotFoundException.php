<?php

namespace App\Exceptions\Http;

use App\Enums\HttpResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class NotFoundException extends HttpException
{
    public function __construct(Throwable $previous = null, int $code = 0)
    {
        parent::__construct(Response::HTTP_NOT_FOUND, HttpResponse::NOT_FOUND, $previous, [], $code);
    }
}
