<?php

namespace App\Exceptions\Api;

class UnauthorizedException extends ApiException
{
    public function __construct(string $message = 'Unauthorized.')
    {
        parent::__construct($message, 401);
    }
}