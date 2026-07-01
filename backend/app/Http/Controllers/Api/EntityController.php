<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Entity\SyncEntityRequest;
use App\Http\Resources\EntityResource;
use App\Services\EntityService;
use App\Services\HashService;
use App\Services\NovelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EntityController extends ApiController
{
    public function __construct(
        private readonly EntityService $entityService,
        private readonly NovelService  $novelService,
        private readonly HashService   $hashService,
    ) {}

    /**
     * GET /api/v1/novels/{novelSlug}/entities
     *
     * Returns all active entities for a novel (without relations).
     * Used for initial download or full refresh.
     */
    public function index(string $novelSlug): JsonResponse
    {
        $novel    = $this->novelService->findBySlugOrFail($novelSlug);
        $entities = $this->entityService->getAllActiveByNovel($novel->id);

        // Load relations agar image, aliases, dll ikut ter-include
        $entities->load(['novel', 'aliases', 'keywords', 'descriptions', 'image']);

        return $this->successResponse(
            EntityResource::collection($entities),
            'Entities retrieved successfully'
        );
    }

    /**
     * GET /api/v1/novels/{novelSlug}/entities/{entitySlug}
     *
     * Returns full entity detail with all relations.
     * Used by Android to display Entity Detail screen.
     */
    public function show(Request $request, string $novelSlug, string $entitySlug): JsonResponse
    {
        // Verify novel exists
        $novel = $this->novelService->findBySlugOrFail($novelSlug);

        // Get locale from request header or query param
        $locale = $request->query('locale', $request->header('X-Locale', 'en'));

        // Validate locale
        if (! in_array($locale, ['en', 'id'])) {
            $locale = 'en';
        }

        // Load entity with all relations
        $entity = $this->entityService->findWithRelations($entitySlug, $locale);

        if (! $entity) {
            return $this->errorResponse('Entity not found.', 404);
        }

        // Verify entity belongs to the requested novel
        if ($entity->novel_id !== $novel->id) {
            return $this->errorResponse('Entity not found in this novel.', 404);
        }

        return $this->successResponse(
            new EntityResource($entity),
            'Entity retrieved successfully'
        );
    }

    /**
     * POST /api/v1/novels/{novelSlug}/entities/sync
     *
     * Android sends its local entity hashes.
     * Server responds with what needs to be added, updated, or deleted.
     */
    public function sync(SyncEntityRequest $request, string $novelSlug): JsonResponse
    {
        $novel = $this->novelService->findBySlugOrFail($novelSlug);

        $diff = $this->hashService->diffEntityHashes(
            $novel->id,
            $request->input('hashes', [])
        );

        $slugsToDownload = array_merge($diff['new'], $diff['updated']);

        $entities = collect($slugsToDownload)->map(function (string $slug) {
            return $this->entityService->findWithRelations($slug);
        })->filter()->values();

        return $this->successResponse([
            'sync' => [
                'new'     => count($diff['new']),
                'updated' => count($diff['updated']),
                'deleted' => $diff['deleted'],
            ],
            'entities' => EntityResource::collection($entities),
        ], 'Sync completed successfully');
    }
}