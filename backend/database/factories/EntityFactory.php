<?php

namespace Database\Factories;

use App\Models\Entity;
use App\Models\Novel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EntityFactory extends Factory
{
    protected $model = Entity::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'novel_id'  => Novel::factory(),
            'type'      => fake()->randomElement(['character', 'place', 'item']),
            'name'      => ucwords($name),
            'slug'      => Str::slug($name),
            'hash'      => hash('sha256', $name),
            'is_active' => true,
        ];
    }

    public function character(): static
    {
        return $this->state(fn() => ['type' => 'character']);
    }

    public function place(): static
    {
        return $this->state(fn() => ['type' => 'place']);
    }

    public function item(): static
    {
        return $this->state(fn() => ['type' => 'item']);
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['is_active' => false]);
    }
}