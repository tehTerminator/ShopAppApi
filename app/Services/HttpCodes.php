<?php

namespace App\Constants;

class HttpCodes
{
    const OK = [
        'code' => 200,
        'message' => 'OK',
    ];

    const CREATED = [
        'code' => 201,
        'message' => 'Created',
    ];

    const BAD_REQUEST = [
        'code' => 400,
        'message' => 'Bad Request',
    ];

    const UNAUTHORIZED = [
        'code' => 401,
        'message' => 'Unauthorized',
    ];

    const FORBIDDEN = [
        'code' => 403,
        'message' => 'Forbidden',
    ];

    const NOT_FOUND = [
        'code' => 404,
        'message' => 'Not Found',
    ];

    const METHOD_NOT_ALLOWED = [
        'code' => 405,
        'message' => 'Method Not Allowed',
    ];

    const INTERNAL_SERVER_ERROR = [
        'code' => 500,
        'message' => 'Internal Server Error',
    ];

    const SERVICE_UNAVAILABLE = [
        'code' => 503,
        'message' => 'Service Unavailable',
    ];

    const UNPROCESSABLE_ENTITY = [
        'code' => 422,
        'message' => 'Unprocessable Entity',
    ];
}
