<?php

namespace App\Repositories\Eloquent;

use App\Models\Entity;
use App\Repositories\Contracts\EntityRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EntityRepository extends BaseRepository implements EntityRepositoryInterface
{
    public function __construct(Entity $model)
    {
        parent::__construct($model);
    }

    public function findBySlug(string $slug): ?Entity
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function findBySlugOrFail(string $slug): Entity
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }

    public function getAllByNovel(string $novelId): Collection
    {
        return $this->model
            ->forNovel($novelId)
            ->orderBy('name')
            ->get();
    }

    public function getPaginatedByNovel(string $novelId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->forNovel($novelId)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getAllActiveByNovel(string $novelId): Collection
    {
        return $this->model
            ->forNovel($novelId)
            ->active()
            ->orderBy('name')
            ->get();
    }

    public function findByHash(string $hash): ?Entity
    {
        return $this->model->where('hash', $hash)->first();
    }

    public function updateHash(string $id, string $hash): Entity
    {
        return $this->update($id, ['hash' => $hash]);
    }

    public function getWithRelations(string $slug, array $relations = []): ?Entity
    {
        return $this->model
            ->where('slug', $slug)
            ->with($relations)
            ->first();
    }
}