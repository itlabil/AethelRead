<?php

namespace App\Repositories\Eloquent;

use App\Models\Novel;
use App\Repositories\Contracts\NovelRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NovelRepository extends BaseRepository implements NovelRepositoryInterface
{
    public function __construct(Novel $model)
    {
        parent::__construct($model);
    }

    public function findBySlug(string $slug): ?Novel
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function findBySlugOrFail(string $slug): Novel
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }

    public function getAllActive(): Collection
    {
        return $this->model->active()->orderBy('name')->get();
    }

    public function getPaginatedActive(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->active()->orderBy('name')->paginate($perPage);
    }

    public function findByHash(string $hash): ?Novel
    {
        return $this->model->where('hash', $hash)->first();
    }

    public function updateHash(string $id, string $hash): Novel
    {
        return $this->update($id, ['hash' => $hash]);
    }
}