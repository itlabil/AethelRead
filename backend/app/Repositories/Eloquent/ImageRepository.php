<?php

namespace App\Repositories\Eloquent;

use App\Models\Image;
use App\Repositories\Contracts\ImageRepositoryInterface;

class ImageRepository extends BaseRepository implements ImageRepositoryInterface
{
    public function __construct(Image $model)
    {
        parent::__construct($model);
    }

    public function findByEntity(string $entityId): ?Image
    {
        return $this->model
            ->where('entity_id', $entityId)
            ->first();
    }

    public function upsert(string $entityId, array $data): Image
    {
        return $this->model->updateOrCreate(
            ['entity_id' => $entityId],
            $data
        );
    }

    public function deleteByEntity(string $entityId): bool
    {
        return $this->model
            ->where('entity_id', $entityId)
            ->delete() > 0;
    }

    public function findByHash(string $hash): ?Image
    {
        return $this->model->where('hash', $hash)->first();
    }
}