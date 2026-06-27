<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Auth Helpers
    |--------------------------------------------------------------------------
    */

    protected function actingAsAdmin(): User
    {
        $user = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);

        return $user;
    }

    protected function actingAsSuperAdmin(): User
    {
        $user = User::factory()->superAdmin()->create([
            'is_active' => true,
        ]);

        return $user;
    }

    protected function getJwtToken(User $user): string
    {
        return JWTAuth::fromUser($user);
    }

    protected function authHeaders(User $user): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->getJwtToken($user),
            'Accept'        => 'application/json',
        ];
    }

    protected function jsonHeaders(): array
    {
        return [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}