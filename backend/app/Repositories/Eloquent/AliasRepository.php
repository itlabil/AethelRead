<?php

namespace App\Repositories\Eloquent;

use App\Models\Alias;
use App\Repositories\Contracts\AliasRepositoryInterface;
use Illuminate\Support\Collection;

class AliasRepository extends BaseRepository implements AliasRepositoryInterface
{
    public function __construct(Alias $model)
    {
        parent::__construct($model);
    }

    public function getAllByEntity(string $entityId): Collection
    {
        return $this->model
            ->where('entity_id', $entityId)
            ->orderBy('name')
            ->get();
    }

    public function deleteAllByEntity(string $entityId): bool
    {
        return $this->model
            ->where('entity_id', $entityId)
            ->delete() > 0;
    }

    public function createMany(string $entityId, array $names): Collection
    {
        $aliases = collect($names)->map(fn($name) => [
            'entity_id'  => $entityId,
            'name'       => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        $this->model->insert($aliases);

        return $this->getAllByEntity($entityId);
    }
}