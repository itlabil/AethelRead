<?php

namespace App\Repositories\Eloquent;

use App\Models\Description;
use App\Repositories\Contracts\DescriptionRepositoryInterface;
use Illuminate\Support\Collection;

class DescriptionRepository extends BaseRepository implements DescriptionRepositoryInterface
{
    public function __construct(Description $model)
    {
        parent::__construct($model);
    }

    public function getAllByEntity(string $entityId): Collection
    {
        return $this->model
            ->where('entity_id', $entityId)
            ->get();
    }

    public function findByEntityAndLocale(string $entityId, string $locale): ?Description
    {
        return $this->model
            ->where('entity_id', $entityId)
            ->where('locale', $locale)
            ->first();
    }

    public function upsert(string $entityId, string $locale, string $content): Description
    {
        return $this->model->updateOrCreate(
            [
                'entity_id' => $entityId,
                'locale'    => $locale,
            ],
            [
                'content' => $content,
            ]
        );
    }

    public function deleteAllByEntity(string $entityId): bool
    {
        return $this->model
            ->where('entity_id', $entityId)
            ->delete() > 0;
    }
}