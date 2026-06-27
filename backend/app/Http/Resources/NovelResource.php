<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NovelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug'          => $this->slug,
            'name'          => $this->name,
            'type'          => $this->type,
            'type_label'    => $this->typeLabel(),
            'hash'          => $this->hash,
            'is_active'     => $this->is_active,
            'entities_count'=> $this->whenCounted('entities'),
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
        ];
    }

    private function typeLabel(): string
    {
        return match ($this->type) {
            'manga'   => 'Manga',
            'manhwa'  => 'Manhwa',
            'manhua'  => 'Manhua',
            default   => 'Other',
        };
    }
}