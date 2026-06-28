<?php

namespace Database\Factories;

use App\Models\Alias;
use App\Models\Entity;
use Illuminate\Database\Eloquent\Factories\Factory;

class AliasFactory extends Factory
{
    protected $model = Alias::class;

    public function definition(): array
    {
        return [
            'entity_id' => Entity::factory(),
            'name'      => fake()->words(2, true),
        ];
    }
}