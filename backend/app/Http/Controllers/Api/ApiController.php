<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class ApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Success Responses
    |--------------------------------------------------------------------------
    */

    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    protected function createdResponse(
        mixed $data = null,
        string $message = 'Created successfully'
    ): JsonResponse {
        return $this->successResponse($data, $message, 201);
    }

    protected function noContentResponse(
        string $message = 'Deleted successfully'
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => null,
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Error Responses
    |--------------------------------------------------------------------------
    */

    protected function errorResponse(
        string $message = 'Error',
        int $statusCode = 400,
        mixed $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $statusCode);
    }

    protected function notFoundResponse(
        string $message = 'Resource not found.'
    ): JsonResponse {
        return $this->errorResponse($message, 404);
    }

    protected function unauthorizedResponse(
        string $message = 'Unauthorized.'
    ): JsonResponse {
        return $this->errorResponse($message, 401);
    }

    protected function forbiddenResponse(
        string $message = 'Forbidden.'
    ): JsonResponse {
        return $this->errorResponse($message, 403);
    }

    protected function validationErrorResponse(
        mixed $errors,
        string $message = 'Validation failed.'
    ): JsonResponse {
        return $this->errorResponse($message, 422, $errors);
    }

    /*
    |--------------------------------------------------------------------------
    | Paginated Response
    |--------------------------------------------------------------------------
    */

    protected function paginatedResponse(
        ResourceCollection $resource,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        $paginator = $resource->resource;

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $resource,
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last'  => $paginator->url($paginator->lastPage()),
                'prev'  => $paginator->previousPageUrl(),
                'next'  => $paginator->nextPageUrl(),
            ],
        ], 200);
    }
}