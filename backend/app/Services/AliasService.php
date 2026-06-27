<?php

namespace App\Services;

use App\Models\Alias;
use App\Repositories\Contracts\AliasRepositoryInterface;
use Illuminate\Support\Collection;

class AliasService extends BaseService
{
    public function __construct(
        private readonly AliasRepositoryInterface $aliasRepository,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Read Operations
    |--------------------------------------------------------------------------
    */

    public function getAllByEntity(string $entityId): Collection
    {
        return $this->aliasRepository->getAllByEntity($entityId);
    }

    public function findById(string $id): ?Alias
    {
        return $this->aliasRepository->findById($id);
    }

    public function findByIdOrFail(string $id): Alias
    {
        return $this->aliasRepository->findByIdOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Write Operations
    |--------------------------------------------------------------------------
    */

    public function create(string $entityId, string $name): Alias
    {
        return $this->aliasRepository->create([
            'entity_id' => $entityId,
            'name'      => $name,
        ]);
    }

    public function createMany(string $entityId, array $names): Collection
    {
        return $this->aliasRepository->createMany($entityId, $names);
    }

    public function update(string $id, string $name): Alias
    {
        return $this->aliasRepository->update($id, ['name' => $name]);
    }

    public function delete(string $id): bool
    {
        return $this->aliasRepository->delete($id);
    }

    public function deleteAllByEntity(string $entityId): bool
    {
        return $this->aliasRepository->deleteAllByEntity($entityId);
    }

    /*
    |--------------------------------------------------------------------------
    | Sync Operations
    |--------------------------------------------------------------------------
    */

    public function sync(string $entityId, array $names): Collection
    {
        $this->aliasRepository->deleteAllByEntity($entityId);

        if (empty($names)) {
            return collect();
        }

        return $this->aliasRepository->createMany($entityId, $names);
    }
}