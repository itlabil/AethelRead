<?php

namespace App\Repositories\Contracts;

use App\Models\Description;
use Illuminate\Support\Collection;

interface DescriptionRepositoryInterface extends RepositoryInterface
{
    public function getAllByEntity(string $entityId): Collection;

    public function findByEntityAndLocale(string $entityId, string $locale): ?Description;

    public function upsert(string $entityId, string $locale, string $content): Description;

    public function deleteAllByEntity(string $entityId): bool;
}