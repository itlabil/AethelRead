<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Description extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'locale',
        'content',
    ];

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
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }
}