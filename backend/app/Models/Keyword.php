<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keyword extends BaseModel
{
    use HasFactory;

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