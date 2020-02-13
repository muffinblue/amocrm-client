<?php

namespace AmoCrm\Exception;

class RequestException extends AmoException
{
    public static $httpCodes = [
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    ];

    public function __construct($code) {
        parent::__construct(
            isset(self::$httpCodes[$code])
                ? 'Request error ' . $code . ': ' . self::$httpCodes[$code]
                : 'Undefined request error'
        );
    }
}