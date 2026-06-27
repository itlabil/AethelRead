<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name'      => fake()->name(),
            'email'     => fake()->unique()->safeEmail(),
            'password'  => 'password123',
            'role'      => 'admin',
            'is_active' => true,
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn() => [
            'role' => 'superadmin',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn() => [
            'is_active' => false,
            'password'  => 'password123',
        ]);
    }
}