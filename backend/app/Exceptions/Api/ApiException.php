<?php

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;

class ApiException extends Exception
{
    public function __construct(
        string $message = 'An error occurred',
        protected int $statusCode = 400,
        protected mixed $errors = null,
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): mixed
    {
        return $this->errors;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'errors'  => $this->errors,
        ], $this->statusCode);
    }
}