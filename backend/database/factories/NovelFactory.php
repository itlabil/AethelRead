<?php

namespace Database\Factories;

use App\Models\Novel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NovelFactory extends Factory
{
    protected $model = Novel::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name'      => ucwords($name),
            'slug'      => Str::slug($name),
            'type'      => fake()->randomElement(['manga', 'manhwa', 'manhua', 'other']),
            'hash'      => hash('sha256', $name),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['is_active' => false]);
    }
}