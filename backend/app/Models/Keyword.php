<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Keyword extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'entity_id',
        'keyword',
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
}