<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Novel extends BaseModel
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'hash',
        'is_active',
        'cover_path',
        'cover_thumbnail_path',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'cover_path' => 'string',
            'cover_thumbnail_path' => 'string',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Slug Configuration
    |--------------------------------------------------------------------------
    */

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class);
    }

    public function activeEntities(): HasMany
    {
        return $this->hasMany(Entity::class)->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_thumbnail_path
            ? Storage::url($this->cover_thumbnail_path)
            : null;
    }
}