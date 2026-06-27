<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NovelResource;
use App\Services\NovelService;
use Illuminate\Http\JsonResponse;

class NovelController extends ApiController
{
    public function __construct(
        private readonly NovelService $novelService,
    ) {}

    /**
     * GET /api/v1/novels
     *
     * Returns all active novels.
     * Used by Android for Novel Selection screen.
     */
    public function index(): JsonResponse
    {
        $novels = $this->novelService->getAllActive();

        return $this->successResponse(
            NovelResource::collection($novels),
            'Novels retrieved successfully'
        );
    }

    /**
     * GET /api/v1/novels/{slug}
     *
     * Returns a single novel by slug.
     * Used by Android to verify novel hash before sync.
     */
    public function show(string $slug): JsonResponse
    {
        $novel = $this->novelService->findBySlugOrFail($slug);

        return $this->successResponse(
            new NovelResource($novel),
            'Novel retrieved successfully'
        );
    }
}