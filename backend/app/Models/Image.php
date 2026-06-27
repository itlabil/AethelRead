<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Image extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'original_path',
        'thumbnail_path',
        'hash',
        'width',
        'height',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'width'  => 'integer',
            'height' => 'integer',
            'size'   => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getThumbnailUrlAttribute(): string
    {
        return Storage::url($this->thumbnail_path);
    }

    public function getOriginalUrlAttribute(): string
    {
        return Storage::url($this->original_path);
    }
}