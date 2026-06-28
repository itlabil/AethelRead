<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseRepository implements RepositoryInterface
{
    public function __construct(
        protected Model $model
    ) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function findById(string $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findByIdOrFail(string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): Model
    {
        $model = $this->findByIdOrFail($id);
        $model->update($data);

        return $model->fresh();
    }

    public function delete(string $id): bool
    {
        $model = $this->findByIdOrFail($id);

        return $model->delete();
    }

    public function query(): Builder
    {
        return $this->model->newQuery();
    }
}