<?php

namespace Database\Factories;

use App\Models\Description;
use App\Models\Entity;
use Illuminate\Database\Eloquent\Factories\Factory;

class DescriptionFactory extends Factory
{
    protected $model = Description::class;

    public function definition(): array
    {
        return [
            'entity_id' => Entity::factory(),
            'locale'    => 'en',
            'content'   => fake()->paragraphs(2, true),
        ];
    }
}