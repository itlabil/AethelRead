<?php

namespace App\Services;

use App\Models\Novel;
use App\Repositories\Contracts\NovelRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class NovelService extends BaseService
{
    public function __construct(
        private readonly NovelRepositoryInterface $novelRepository,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Read Operations
    |--------------------------------------------------------------------------
    */

    public function getAll(): Collection
    {
        return $this->novelRepository->all();
    }

    public function getAllActive(): Collection
    {
        return $this->novelRepository->getAllActive();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->novelRepository->paginate($perPage);
    }

    public function getPaginatedActive(int $perPage = 15): LengthAwarePaginator
    {
        return $this->novelRepository->getPaginatedActive($perPage);
    }

    public function findById(string $id): ?Novel
    {
        return $this->novelRepository->findById($id);
    }

    public function findBySlug(string $slug): ?Novel
    {
        return $this->novelRepository->findBySlug($slug);
    }

    public function findBySlugOrFail(string $slug): Novel
    {
        return $this->novelRepository->findBySlugOrFail($slug);
    }

    /*
    |--------------------------------------------------------------------------
    | Write Operations
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Novel
    {
        $data['hash'] = $this->generateHash($data);

        return $this->novelRepository->create($data);
    }

    public function update(string $id, array $data): Novel
    {
        $novel = $this->novelRepository->findByIdOrFail($id);

        // Regenerate hash on update
        $data['hash'] = $this->generateHash(array_merge($novel->toArray(), $data));

        return $this->novelRepository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        return $this->novelRepository->delete($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Operations
    |--------------------------------------------------------------------------
    */

    public function toggleActive(string $id): Novel
    {
        $novel = $this->novelRepository->findByIdOrFail($id);

        return $this->novelRepository->update($id, [
            'is_active' => ! $novel->is_active,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Hash Operations
    |--------------------------------------------------------------------------
    */

    public function refreshHash(string $id): Novel
    {
        $novel = $this->novelRepository->findByIdOrFail($id);
        $hash  = $this->generateHash($novel->toArray());

        return $this->novelRepository->updateHash($id, $hash);
    }

    public function verifyHash(string $slug, string $clientHash): bool
    {
        $novel = $this->novelRepository->findBySlug($slug);

        if (! $novel) {
            return false;
        }

        return $novel->hash === $clientHash;
    }
}