<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug'         => $this->slug,
            'name'         => $this->name,
            'type'         => $this->type,
            'type_label'   => $this->typeLabel(),
            'hash'         => $this->hash,
            'is_active'    => $this->is_active,
            'novel'        => $this->whenLoaded('novel', fn() => [
                'slug' => $this->novel->slug,
                'name' => $this->novel->name,
            ]),
            'aliases'      => AliasResource::collection($this->whenLoaded('aliases')),
            'keywords'     => KeywordResource::collection($this->whenLoaded('keywords')),
            'descriptions' => DescriptionResource::collection($this->whenLoaded('descriptions')),
            'image'        => new ImageResource($this->whenLoaded('image')),
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }

    private function typeLabel(): string
    {
        return match ($this->type) {
            'character' => 'Character',
            'place'     => 'Place',
            'item'      => 'Item',
            default     => 'Unknown',
        };
    }
}