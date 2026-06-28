<?php

namespace App\Exceptions\Api;

class InvalidEntityTypeException extends ApiException
{
    public function __construct(string $type)
    {
        parent::__construct("Invalid entity type: '{$type}'. Supported: character, place, item.", 422);
    }
}