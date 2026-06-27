<?php

namespace Tests\Feature\Api\Novel;

use App\Models\Novel;
use Tests\TestCase;

class NovelTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Index Tests
    |--------------------------------------------------------------------------
    */

    public function test_can_get_all_active_novels(): void
    {
        Novel::factory()->count(3)->create(['is_active' => true]);
        Novel::factory()->count(2)->create(['is_active' => false]);

        $response = $this->getJson('/api/v1/novels');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'data');
    }

    public function test_inactive_novels_are_excluded(): void
    {
        Novel::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/v1/novels');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /*
    |--------------------------------------------------------------------------
    | Show Tests
    |--------------------------------------------------------------------------
    */

    public function test_can_get_novel_by_slug(): void
    {
        $novel = Novel::factory()->create(['is_active' => true]);

        $response = $this->getJson("/api/v1/novels/{$novel->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'slug' => $novel->slug,
                    'name' => $novel->name,
                ],
            ]);
    }

    public function test_returns_404_for_missing_novel(): void
    {
        $response = $this->getJson('/api/v1/novels/tidak-ada');

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    public function test_novel_response_has_correct_structure(): void
    {
        $novel = Novel::factory()->create();

        $response = $this->getJson("/api/v1/novels/{$novel->slug}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'slug',
                    'name',
                    'type',
                    'type_label',
                    'hash',
                    'is_active',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }
}