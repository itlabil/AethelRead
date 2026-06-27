<?php

namespace App\Services;

use App\Models\Keyword;
use App\Repositories\Contracts\KeywordRepositoryInterface;
use Illuminate\Support\Collection;

class KeywordService extends BaseService
{
    public function __construct(
        private readonly KeywordRepositoryInterface $keywordRepository,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Read Operations
    |--------------------------------------------------------------------------
    */

    public function getAllByEntity(string $entityId): Collection
    {
        return $this->keywordRepository->getAllByEntity($entityId);
    }

    public function findById(string $id): ?Keyword
    {
        return $this->keywordRepository->findById($id);
    }

    public function findByIdOrFail(string $id): Keyword
    {
        return $this->keywordRepository->findByIdOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Write Operations
    |--------------------------------------------------------------------------
    */

    public function create(string $entityId, string $keyword): Keyword
    {
        return $this->keywordRepository->create([
            'entity_id' => $entityId,
            'keyword'   => $keyword,
        ]);
    }

    public function createMany(string $entityId, array $keywords): Collection
    {
        return $this->keywordRepository->createMany($entityId, $keywords);
    }

    public function update(string $id, string $keyword): Keyword
    {
        return $this->keywordRepository->update($id, ['keyword' => $keyword]);
    }

    public function delete(string $id): bool
    {
        return $this->keywordRepository->delete($id);
    }

    public function deleteAllByEntity(string $entityId): bool
    {
        return $this->keywordRepository->deleteAllByEntity($entityId);
    }

    /*
    |--------------------------------------------------------------------------
    | Sync Operations
    |--------------------------------------------------------------------------
    */

    public function sync(string $entityId, array $keywords): Collection
    {
        $this->keywordRepository->deleteAllByEntity($entityId);

        if (empty($keywords)) {
            return collect();
        }

        return $this->keywordRepository->createMany($entityId, $keywords);
    }

    /*
    |--------------------------------------------------------------------------
    | Normalization
    |--------------------------------------------------------------------------
    | Keywords are normalized before saving to ensure consistent matching.
    */

    public function normalize(string $keyword): string
    {
        return strtolower(trim($keyword));
    }

    public function normalizeMany(array $keywords): array
    {
        return collect($keywords)
            ->map(fn($keyword) => $this->normalize($keyword))
            ->unique()
            ->values()
            ->toArray();
    }
}