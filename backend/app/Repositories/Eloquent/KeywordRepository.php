<?php

namespace App\Repositories\Eloquent;

use App\Models\Keyword;
use App\Repositories\Contracts\KeywordRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class KeywordRepository extends BaseRepository implements KeywordRepositoryInterface
{
    public function __construct(Keyword $model)
    {
        parent::__construct($model);
    }

    public function getAllByEntity(string $entityId): Collection
    {
        return $this->model
            ->where('entity_id', $entityId)
            ->orderBy('keyword')
            ->get();
    }

    public function deleteAllByEntity(string $entityId): bool
    {
        return $this->model
            ->where('entity_id', $entityId)
            ->delete() > 0;
    }

    public function createMany(string $entityId, array $keywords): Collection
    {
        $records = collect($keywords)->map(fn($keyword) => [
            'id'         => (string) Str::orderedUuid(),
            'entity_id'  => $entityId,
            'keyword'    => $keyword,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        $this->model->insert($records);

        return $this->getAllByEntity($entityId);
    }
}