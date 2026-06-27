<?php

namespace App\Services;

use App\Models\Entity;
use App\Models\Novel;
use App\Repositories\Contracts\EntityRepositoryInterface;
use App\Repositories\Contracts\NovelRepositoryInterface;

class HashService extends BaseService
{
    public function __construct(
        private readonly NovelRepositoryInterface  $novelRepository,
        private readonly EntityRepositoryInterface $entityRepository,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Novel Hash Operations
    |--------------------------------------------------------------------------
    */

    public function generateNovelHash(Novel $novel): string
    {
        return $this->generateHash([
            'id'   => $novel->id,
            'name' => $novel->name,
            'slug' => $novel->slug,
            'type' => $novel->type,
        ]);
    }

    public function refreshNovelHash(string $novelId): Novel
    {
        $novel = $this->novelRepository->findByIdOrFail($novelId);
        $hash  = $this->generateNovelHash($novel);

        return $this->novelRepository->updateHash($novelId, $hash);
    }

    public function verifyNovelHash(string $novelId, string $clientHash): bool
    {
        $novel = $this->novelRepository->findById($novelId);

        if (! $novel) {
            return false;
        }

        return hash_equals($novel->hash ?? '', $clientHash);
    }

    /*
    |--------------------------------------------------------------------------
    | Entity Hash Operations
    |--------------------------------------------------------------------------
    */

    public function generateEntityHash(Entity $entity): string
    {
        // Load relations needed for hash
        $entity->loadMissing(['aliases', 'keywords', 'descriptions', 'image']);

        return $this->generateHash([
            'id'           => $entity->id,
            'name'         => $entity->name,
            'slug'         => $entity->slug,
            'type'         => $entity->type,
            'aliases'      => $entity->aliases->pluck('name')->sort()->values()->toArray(),
            'keywords'     => $entity->keywords->pluck('keyword')->sort()->values()->toArray(),
            'descriptions' => $entity->descriptions->pluck('content', 'locale')->toArray(),
            'image_hash'   => $entity->image?->hash,
        ]);
    }

    public function refreshEntityHash(string $entityId): Entity
    {
        $entity = $this->entityRepository->findByIdOrFail($entityId);
        $hash   = $this->generateEntityHash($entity);

        return $this->entityRepository->updateHash($entityId, $hash);
    }

    public function verifyEntityHash(string $entityId, string $clientHash): bool
    {
        $entity = $this->entityRepository->findById($entityId);

        if (! $entity) {
            return false;
        }

        return hash_equals($entity->hash ?? '', $clientHash);
    }

    /*
    |--------------------------------------------------------------------------
    | Batch Hash Operations
    |--------------------------------------------------------------------------
    | Used by Android sync to check multiple entities at once.
    */

    public function diffEntityHashes(string $novelId, array $clientHashes): array
    {
        // clientHashes format: ['entity-slug' => 'hash-value']
        $entities = $this->entityRepository->getAllActiveByNovel($novelId);

        $needsUpdate = [];
        $newEntities = [];

        foreach ($entities as $entity) {
            if (! isset($clientHashes[$entity->slug])) {
                // Entity not in client — needs to be downloaded
                $newEntities[] = $entity->slug;
            } elseif ($clientHashes[$entity->slug] !== $entity->hash) {
                // Hash mismatch — entity has been updated
                $needsUpdate[] = $entity->slug;
            }
        }

        // Slugs that exist on client but not on server (deleted)
        $serverSlugs = $entities->pluck('slug')->toArray();
        $deletedSlugs = array_diff(array_keys($clientHashes), $serverSlugs);

        return [
            'new'     => $newEntities,
            'updated' => $needsUpdate,
            'deleted' => array_values($deletedSlugs),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Raw Hash Helpers
    |--------------------------------------------------------------------------
    */

    public function hashString(string $value): string
    {
        return $this->generateHash($value);
    }

    public function hashFile(string $filePath): string
    {
        return hash_file('sha256', $filePath);
    }

    public function compare(string $hashA, string $hashB): bool
    {
        return hash_equals($hashA, $hashB);
    }
}