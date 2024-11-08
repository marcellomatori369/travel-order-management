<?php

namespace App\Exceptions\Http;

use App\Enums\HttpResponse;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use Throwable;

class HttpException extends RuntimeException implements HttpExceptionInterface
{
    public function __construct(
        protected int $statusCode,
        protected HttpResponse $messageCode,
        Throwable $previous = null,
        protected array $headers = [],
        int $code = 0,
    ) {
        parent::__construct($messageCode->value, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function getResponse(): JsonResponse
    {
        $responseBody = [
            'data' => [],
            'code' => $this->messageCode,
        ];

        return response()->json($responseBody, $this->statusCode, $this->headers);
    }
}
