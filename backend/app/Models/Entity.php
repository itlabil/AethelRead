<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Entity extends BaseModel
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'novel_id',
        'type',
        'name',
        'slug',
        'hash',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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

    public function novel(): BelongsTo
    {
        return $this->belongsTo(Novel::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(Alias::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class);
    }

    public function descriptions(): HasMany
    {
        return $this->hasMany(Description::class);
    }

    public function image(): HasOne
    {
        return $this->hasOne(Image::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function description(string $locale = 'en'): ?Description
    {
        return $this->descriptions->firstWhere('locale', $locale);
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

    public function scopeForNovel($query, string $novelId)
    {
        return $query->where('novel_id', $novelId);
    }
}