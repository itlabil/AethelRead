<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (! $this->resource) {
            return [];
        }

        return [
            'thumbnail_url' => $this->thumbnail_url,
            'original_url'  => $this->original_url,
            'hash'          => $this->hash,
            'width'         => $this->width,
            'height'        => $this->height,
            'size'          => $this->size,
        ];
    }
}