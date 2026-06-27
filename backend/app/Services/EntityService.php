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
        $data['hash'] = $this->generateHash($data);

        $entity = $this->entityRepository->create($data);

        // Sync aliases if provided
        if (! empty($data['aliases'])) {
            $this->aliasRepository->createMany($entity->id, $data['aliases']);
        }

        // Sync keywords if provided
        if (! empty($data['keywords'])) {
            $this->keywordRepository->createMany($entity->id, $data['keywords']);
        }

        // Sync descriptions if provided
        if (! empty($data['descriptions'])) {
            foreach ($data['descriptions'] as $locale => $content) {
                $this->descriptionRepository->upsert($entity->id, $locale, $content);
            }
        }

        return $entity->fresh(['aliases', 'keywords', 'descriptions', 'image']);
    }

    public function update(string $id, array $data): Entity
    {
        $entity = $this->entityRepository->findByIdOrFail($id);

        // Regenerate hash on update
        $data['hash'] = $this->generateHash(array_merge($entity->toArray(), $data));

        $entity = $this->entityRepository->update($id, $data);

        // Sync aliases if provided
        if (isset($data['aliases'])) {
            $this->aliasRepository->deleteAllByEntity($id);
            if (! empty($data['aliases'])) {
                $this->aliasRepository->createMany($id, $data['aliases']);
            }
        }

        // Sync keywords if provided
        if (isset($data['keywords'])) {
            $this->keywordRepository->deleteAllByEntity($id);
            if (! empty($data['keywords'])) {
                $this->keywordRepository->createMany($id, $data['keywords']);
            }
        }

        // Sync descriptions if provided
        if (! empty($data['descriptions'])) {
            foreach ($data['descriptions'] as $locale => $content) {
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
}