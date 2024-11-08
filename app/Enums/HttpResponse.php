<?php

namespace App\Enums;

enum HttpResponse: string
{
    case FORBIDDEN = 'forbidden';
    case INTERNAL_SERVER_ERROR = 'internal-server-error';
    case INVALID_FILTER = 'invalid-filter';
    case INVALID_SORT = 'invalid-sort';
    case METHOD_NOT_ALLOWED = 'method-not-allowed';
    case NOT_FOUND = 'not-found';
    case UNAUTHENTICATED = 'unauthenticated';
    case UNAUTHORIZED = 'unauthorized';
    case VALIDATION_FAILED = 'validation-failed';
}
