<?php

namespace App\Repositories\Contracts;

use App\Models\Entity;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface EntityRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?Entity;

    public function findBySlugOrFail(string $slug): Entity;

    public function getAllByNovel(string $novelId): Collection;

    public function getPaginatedByNovel(string $novelId, int $perPage = 15): LengthAwarePaginator;

    public function getAllActiveByNovel(string $novelId): Collection;

    public function findByHash(string $hash): ?Entity;

    public function updateHash(string $id, string $hash): Entity;

    public function getWithRelations(string $slug, array $relations = []): ?Entity;
}