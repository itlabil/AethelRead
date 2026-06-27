<?php

namespace App\Repositories\Contracts;

use App\Models\Keyword;
use Illuminate\Support\Collection;

interface KeywordRepositoryInterface extends RepositoryInterface
{
    public function getAllByEntity(string $entityId): Collection;

    public function deleteAllByEntity(string $entityId): bool;

    public function createMany(string $entityId, array $keywords): Collection;
}