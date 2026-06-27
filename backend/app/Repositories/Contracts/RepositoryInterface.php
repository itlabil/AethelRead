<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(string $id): ?Model;

    public function create(array $data): Model;

    public function update(string $id, array $data): Model;

    public function delete(string $id): bool;
}