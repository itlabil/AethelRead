<?php

namespace App\Repositories\Contracts;

use App\Models\Alias;
use Illuminate\Support\Collection;

interface AliasRepositoryInterface extends RepositoryInterface
{
    public function getAllByEntity(string $entityId): Collection;

    public function deleteAllByEntity(string $entityId): bool;

    public function createMany(string $entityId, array $names): Collection;
}