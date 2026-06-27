<?php

namespace App\Repositories\Contracts;

use App\Models\Novel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface NovelRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?Novel;

    public function findBySlugOrFail(string $slug): Novel;

    public function getAllActive(): Collection;

    public function getPaginatedActive(int $perPage = 15): LengthAwarePaginator;

    public function findByHash(string $hash): ?Novel;

    public function updateHash(string $id, string $hash): Novel;
}