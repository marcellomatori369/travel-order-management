<?php

namespace App\Exceptions\Http;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface as BaseHttpExceptionInterface;

interface HttpExceptionInterface extends BaseHttpExceptionInterface
{
    public function getResponse(): JsonResponse;
}
