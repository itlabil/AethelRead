<?php

namespace App\Services;

use App\Models\Entity;
use App\Repositories\Contracts\AliasRepositoryInterface;
use App\Repositories\Contracts\DescriptionRepositoryInterface;
use App\Repositories\Contracts\EntityRepositoryInterface;
use App\Repositories\Contracts\KeywordRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EntityService extends BaseService
{
    public function __construct(
        private readonly EntityRepositoryInterface      $entityRepository,
        private readonly AliasRepositoryInterface       $aliasRepository,
        private readonly KeywordRepositoryInterface     $keywordRepository,
        private readonly DescriptionRepositoryInterface $descriptionRepository,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Read Operations
    |--------------------------------------------------------------------------
    */

    public function getAllByNovel(string $novelId): Collection
    {
        return $this->entityRepository->getAllByNovel($novelId);
    }

    public function getAllActiveByNovel(string $novelId): Collection
    {
        return $this->entityRepository->getAllActiveByNovel($novelId);
    }

    public function getPaginatedByNovel(string $novelId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->entityRepository->getPaginatedByNovel($novelId, $perPage);
    }

    public function findById(string $id): ?Entity
    {
        return $this->entityRepository->findById($id);
    }

    public function findBySlug(string $slug): ?Entity
    {
        return $this->entityRepository->findBySlug($slug);
    }

    public function findBySlugOrFail(string $slug): Entity
    {
        return $this->entityRepository->findBySlugOrFail($slug);
    }

    public function findWithRelations(string $slug, string $locale = 'en'): ?Entity
    {
        return $this->entityRepository->getWithRelations($slug, [
            'novel',
            'aliases',
            'keywords',
            'image',
            'descriptions' => fn($query) => $query->where('locale', $locale),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Write Operations
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Entity
    {
        // Pisahkan data relasi dari data model
        $aliases      = $data['aliases'] ?? [];
        $keywords     = $data['keywords'] ?? [];
        $descriptions = $data['descriptions'] ?? [];

        // Hanya field yang ada di tabel entities
        $entityData = [
            'novel_id'  => $data['novel_id'],
            'type'      => $data['type'],
            'name'      => $data['name'],
            'is_active' => $data['is_active'] ?? true,
        ];

        $entityData['hash'] = $this->generateHash($entityData);

        $entity = $this->entityRepository->create($entityData);

        // Sync relasi
        if (! empty($aliases)) {
            $this->aliasRepository->createMany($entity->id, $aliases);
        }

        if (! empty($keywords)) {
            $this->keywordRepository->createMany($entity->id, $keywords);
        }

        foreach ($descriptions as $locale => $content) {
            if (! empty($content)) {
                $this->descriptionRepository->upsert($entity->id, $locale, $content);
            }
        }

        return $entity->fresh(['aliases', 'keywords', 'descriptions', 'image']);
    }

    public function update(string $id, array $data): Entity
    {
        // Pisahkan data relasi
        $aliases      = $data['aliases'] ?? null;
        $keywords     = $data['keywords'] ?? null;
        $descriptions = $data['descriptions'] ?? [];

        // Hanya field yang ada di tabel entities
        $entityData = array_filter([
            'novel_id'  => $data['novel_id'] ?? null,
            'type'      => $data['type'] ?? null,
            'name'      => $data['name'] ?? null,
            'is_active' => $data['is_active'] ?? null,
        ], fn($value) => ! is_null($value));

        $entity = $this->entityRepository->findByIdOrFail($id);
        $entityData['hash'] = $this->generateHash(array_merge($entity->toArray(), $entityData));

        $entity = $this->entityRepository->update($id, $entityData);

        // Sync aliases
        if ($aliases !== null) {
            $this->aliasRepository->deleteAllByEntity($id);
            if (! empty($aliases)) {
                $this->aliasRepository->createMany($id, $aliases);
            }
        }

        // Sync keywords
        if ($keywords !== null) {
            $this->keywordRepository->deleteAllByEntity($id);
            if (! empty($keywords)) {
                $this->keywordRepository->createMany($id, $keywords);
            }
        }

        // Sync descriptions
        foreach ($descriptions as $locale => $content) {
            if (! empty($content)) {
                $this->descriptionRepository->upsert($id, $locale, $content);
            }
        }

        return $entity->fresh(['aliases', 'keywords', 'descriptions', 'image']);
    }

    public function delete(string $id): bool
    {
        return $this->entityRepository->delete($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Operations
    |--------------------------------------------------------------------------
    */

    public function toggleActive(string $id): Entity
    {
        $entity = $this->entityRepository->findByIdOrFail($id);

        return $this->entityRepository->update($id, [
            'is_active' => ! $entity->is_active,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Hash Operations
    |--------------------------------------------------------------------------
    */

    public function refreshHash(string $id): Entity
    {
        $entity = $this->entityRepository->findByIdOrFail($id);
        $hash   = $this->generateHash($entity->toArray());

        return $this->entityRepository->updateHash($id, $hash);
    }

    public function verifyHash(string $slug, string $clientHash): bool
    {
        $entity = $this->entityRepository->findBySlug($slug);

        if (! $entity) {
            return false;
        }

        return $entity->hash === $clientHash;
    }

    /*
    |--------------------------------------------------------------------------
    | Type Helpers
    |--------------------------------------------------------------------------
    */

    public function getValidTypes(): array
    {
        return ['character', 'place', 'item'];
    }

    public function isValidType(string $type): bool
    {
        return in_array($type, $this->getValidTypes());
    }

    /*
    |--------------------------------------------------------------------------
    | findByIdOrFail
    |--------------------------------------------------------------------------
    */

    public function findByIdOrFail(string $id): Entity
    {
        return $this->entityRepository->findByIdOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | getFilteredPaginated
    |--------------------------------------------------------------------------
    */

    public function getFilteredPaginated(array $filters): LengthAwarePaginator
    {
        $query = $this->entityRepository->query();

        // Search
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'ilike', "%{$filters['search']}%")
                ->orWhere('slug', 'ilike', "%{$filters['search']}%");
            });
        }

        // Filter by novel
        if (! empty($filters['novel_id'])) {
            $query->where('novel_id', $filters['novel_id']);
        }

        // Filter by type
        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Filter by status
        if ($filters['status'] === 'active') {
            $query->where('is_active', true);
        } elseif ($filters['status'] === 'inactive') {
            $query->where('is_active', false);
        }

        // Sort
        $query->orderBy($filters['sort'], $filters['direction']);

        return $query->with('novel')->paginate($filters['per_page'])->withQueryString();
    }
}