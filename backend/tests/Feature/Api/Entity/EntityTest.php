<?php

namespace Tests\Feature\Api\Entity;

use App\Models\Alias;
use App\Models\Description;
use App\Models\Entity;
use App\Models\Keyword;
use App\Models\Novel;
use Tests\TestCase;

class EntityTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Index Tests
    |--------------------------------------------------------------------------
    */

    public function test_can_get_all_entities_by_novel(): void
    {
        $novel = Novel::factory()->create();
        Entity::factory()->count(5)->create([
            'novel_id'  => $novel->id,
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/v1/novels/{$novel->slug}/entities");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(5, 'data');
    }

    public function test_inactive_entities_are_excluded(): void
    {
        $novel = Novel::factory()->create();
        Entity::factory()->count(2)->create([
            'novel_id'  => $novel->id,
            'is_active' => false,
        ]);

        $response = $this->getJson("/api/v1/novels/{$novel->slug}/entities");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /*
    |--------------------------------------------------------------------------
    | Show Tests
    |--------------------------------------------------------------------------
    */

    public function test_can_get_entity_detail(): void
    {
        $novel  = Novel::factory()->create();
        $entity = Entity::factory()->create(['novel_id' => $novel->id]);

        Description::create([
            'entity_id' => $entity->id,
            'locale'    => 'en',
            'content'   => 'Test description.',
        ]);

        $response = $this->getJson("/api/v1/novels/{$novel->slug}/entities/{$entity->slug}");

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
                    'aliases',
                    'keywords',
                    'descriptions',
                    'image',
                ],
            ]);
    }

    public function test_returns_404_for_missing_entity(): void
    {
        $novel = Novel::factory()->create();

        $response = $this->getJson("/api/v1/novels/{$novel->slug}/entities/tidak-ada");

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    /*
    |--------------------------------------------------------------------------
    | Sync Tests
    |--------------------------------------------------------------------------
    */

    public function test_sync_returns_all_entities_when_hashes_empty(): void
    {
        $novel = Novel::factory()->create();
        Entity::factory()->count(3)->create([
            'novel_id'  => $novel->id,
            'is_active' => true,
        ]);

        $response = $this->postJson("/api/v1/novels/{$novel->slug}/entities/sync", [
            'hashes' => [],
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.sync.new', 3);
    }

    public function test_sync_detects_deleted_entities(): void
    {
        $novel = Novel::factory()->create();

        $response = $this->postJson("/api/v1/novels/{$novel->slug}/entities/sync", [
            'hashes' => [
                'entity-yang-tidak-ada' => hash('sha256', 'test'),
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.sync.deleted.0', 'entity-yang-tidak-ada');
    }

    public function test_sync_detects_updated_entities(): void
    {
        $novel  = Novel::factory()->create();
        $entity = Entity::factory()->create([
            'novel_id'  => $novel->id,
            'is_active' => true,
            'hash'      => hash('sha256', 'server-hash'),
        ]);

        $response = $this->postJson("/api/v1/novels/{$novel->slug}/entities/sync", [
            'hashes' => [
                $entity->slug => hash('sha256', 'old-client-hash'),
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.sync.updated', 1);
    }
}