<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Login Tests
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                ],
            ])
            ->assertJson(['success' => true]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'admin@test.com']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'admin@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_login_fails_with_inactive_user(): void
    {
        User::factory()->inactive()->create([
            'email'    => 'inactive@test.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'inactive@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_validation_requires_email(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_validation_requires_password(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@test.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /*
    |--------------------------------------------------------------------------
    | Me Tests
    |--------------------------------------------------------------------------
    */

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = $this->actingAsAdmin();

        $response = $this->getJson('/api/v1/auth/me', $this->authHeaders($user));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    /*
    |--------------------------------------------------------------------------
    | Logout Tests
    |--------------------------------------------------------------------------
    */

    public function test_authenticated_user_can_logout(): void
    {
        $user = $this->actingAsAdmin();

        $response = $this->postJson('/api/v1/auth/logout', [], $this->authHeaders($user));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | Refresh Tests
    |--------------------------------------------------------------------------
    */

    public function test_authenticated_user_can_refresh_token(): void
    {
        $user = $this->actingAsAdmin();

        $response = $this->postJson('/api/v1/auth/refresh', [], $this->authHeaders($user));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['access_token'],
            ]);
    }
}