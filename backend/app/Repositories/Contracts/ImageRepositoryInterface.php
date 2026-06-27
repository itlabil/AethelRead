<?php

namespace App\Repositories\Contracts;

use App\Models\Image;

interface ImageRepositoryInterface extends RepositoryInterface
{
    public function findByEntity(string $entityId): ?Image;

    public function upsert(string $entityId, array $data): Image;

    public function deleteByEntity(string $entityId): bool;

    public function findByHash(string $hash): ?Image;
}