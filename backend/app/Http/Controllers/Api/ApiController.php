<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

abstract class ApiController extends Controller
{
    protected function successResponse(mixed $data = null, string $message = 'Success', int $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    protected function errorResponse(string $message = 'Error', int $statusCode = 400, mixed $errors = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $statusCode);
    }
}