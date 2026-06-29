package com.sm.aethelread.domain.repository

import com.sm.aethelread.domain.model.Entity
import com.sm.aethelread.domain.model.SyncResult
import com.sm.aethelread.util.Resource
import kotlinx.coroutines.flow.Flow

interface EntityRepository {

    /**
     * Get all active entities for a novel from Smart Cache.
     * Priority: Memory → Room → API
     */
    fun getEntities(novelSlug: String): Flow<Resource<List<Entity>>>

    /**
     * Get entity detail by slug.
     */
    suspend fun getEntityBySlug(novelSlug: String, entitySlug: String): Entity?

    /**
     * Sync entities with server using hash comparison.
     */
    suspend fun syncEntities(novelSlug: String): Resource<SyncResult>

    /**
     * Get all entities for recognition engine (from local cache only).
     */
    suspend fun getEntitiesForRecognition(novelSlug: String): List<Entity>

    /**
     * Save entities to local cache.
     */
    suspend fun saveEntities(entities: List<Entity>)

    /**
     * Delete entities by slugs.
     */
    suspend fun deleteEntities(slugs: List<String>)
}