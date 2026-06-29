package com.sm.aethelread.data.repository

import com.sm.aethelread.data.local.dao.AliasDao
import com.sm.aethelread.data.local.dao.DescriptionDao
import com.sm.aethelread.data.local.dao.EntityDao
import com.sm.aethelread.data.local.dao.ImageDao
import com.sm.aethelread.data.local.dao.KeywordDao
import com.sm.aethelread.data.local.entity.toDomain
import com.sm.aethelread.data.local.entity.toEntity
import com.sm.aethelread.data.remote.api.EntityApiService
import com.sm.aethelread.data.remote.dto.SyncRequest
import com.sm.aethelread.data.remote.dto.toDomain
import com.sm.aethelread.domain.model.Entity
import com.sm.aethelread.domain.model.SyncResult
import com.sm.aethelread.domain.repository.EntityRepository
import com.sm.aethelread.util.NetworkUtils
import com.sm.aethelread.util.Resource
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.flow
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class EntityRepositoryImpl @Inject constructor(
    private val entityDao: EntityDao,
    private val aliasDao: AliasDao,
    private val keywordDao: KeywordDao,
    private val descriptionDao: DescriptionDao,
    private val imageDao: ImageDao,
    private val entityApiService: EntityApiService,
    private val networkUtils: NetworkUtils,
) : EntityRepository {

    // Memory cache per novel
    private val memoryCache = mutableMapOf<String, List<Entity>>()

    override fun getEntities(novelSlug: String): Flow<Resource<List<Entity>>> = flow {
        emit(Resource.Loading)

        // Layer 1: Memory Cache
        memoryCache[novelSlug]?.let {
            emit(Resource.Success(it))
            return@flow
        }

        // Layer 2: Room Database
        val localEntities = entityDao.getAllByNovelSync(novelSlug)
        if (localEntities.isNotEmpty()) {
            val entities = localEntities.map { buildFullEntity(it.slug) }.filterNotNull()
            memoryCache[novelSlug] = entities
            emit(Resource.Success(entities))
        } else {
            // Layer 3: REST API
            if (networkUtils.isConnected()) {
                emit(downloadEntities(novelSlug))
            } else {
                emit(Resource.Error("No internet connection and no cached data available."))
            }
        }
    }

    override suspend fun getEntityBySlug(novelSlug: String, entitySlug: String): Entity? {
        return buildFullEntity(entitySlug)
    }

    override suspend fun syncEntities(novelSlug: String): Resource<SyncResult> {
        if (!networkUtils.isConnected()) {
            return Resource.Error("No internet connection.")
        }

        return try {
            // Get local hashes
            val localHashes = entityDao.getSlugHashMap(novelSlug)
                .associate { it.slug to (it.hash ?: "") }

            // Send to server
            val response = entityApiService.syncEntities(
                novelSlug = novelSlug,
                request   = SyncRequest(hashes = localHashes),
            )

            if (response.success && response.data != null) {
                val syncResult = response.data.toDomain()

                // Save new & updated entities
                if (syncResult.entities.isNotEmpty()) {
                    saveEntities(syncResult.entities)
                }

                // Delete removed entities
                if (syncResult.deletedSlugs.isNotEmpty()) {
                    deleteEntities(syncResult.deletedSlugs)
                }

                // Invalidate memory cache
                memoryCache.remove(novelSlug)

                Resource.Success(syncResult)
            } else {
                Resource.Error(response.message)
            }
        } catch (e: Exception) {
            Resource.Error(e.message ?: "Sync failed", e)
        }
    }

    override suspend fun getEntitiesForRecognition(novelSlug: String): List<Entity> {
        // Memory cache first
        memoryCache[novelSlug]?.let { return it }

        // Room fallback
        val localEntities = entityDao.getAllByNovelSync(novelSlug)
        return localEntities.map { buildFullEntity(it.slug) }.filterNotNull()
    }

    override suspend fun saveEntities(entities: List<Entity>) {
        entities.forEach { entity ->
            // Save entity
            entityDao.insertAll(listOf(entity.toEntity()))

            // Save aliases
            aliasDao.deleteByEntity(entity.slug)
            aliasDao.insertAll(entity.aliases.map { it.toEntity(entity.slug) })

            // Save keywords
            keywordDao.deleteByEntity(entity.slug)
            keywordDao.insertAll(entity.keywords.map { it.toEntity(entity.slug) })

            // Save descriptions
            descriptionDao.deleteByEntity(entity.slug)
            descriptionDao.insertAll(entity.descriptions.map { it.toEntity(entity.slug) })

            // Save image
            entity.image?.let {
                imageDao.insert(it.toEntity(entity.slug))
            }
        }
    }

    override suspend fun deleteEntities(slugs: List<String>) {
        slugs.forEach { slug ->
            entityDao.deleteBySlug(slug)
            aliasDao.deleteByEntity(slug)
            keywordDao.deleteByEntity(slug)
            descriptionDao.deleteByEntity(slug)
            imageDao.deleteByEntity(slug)
        }
    }

    private suspend fun buildFullEntity(slug: String): Entity? {
        val entityEntity = entityDao.getBySlug(slug) ?: return null
        val aliases      = aliasDao.getByEntity(slug)
        val keywords     = keywordDao.getByEntity(slug)
        val descriptions = descriptionDao.run {
            listOfNotNull(
                getByEntityAndLocale(slug, "en"),
                getByEntityAndLocale(slug, "id"),
            )
        }
        val image = imageDao.getByEntity(slug)

        return entityEntity.toDomain(
            aliases      = aliases,
            keywords     = keywords,
            descriptions = descriptions,
            image        = image,
        )
    }

    private suspend fun downloadEntities(novelSlug: String): Resource<List<Entity>> {
        return try {
            val response = entityApiService.getEntities(novelSlug)
            if (response.success && response.data != null) {
                val entities = response.data.map { it.toDomain() }
                saveEntities(entities)
                memoryCache[novelSlug] = entities
                Resource.Success(entities)
            } else {
                Resource.Error(response.message)
            }
        } catch (e: Exception) {
            Resource.Error(e.message ?: "Failed to fetch entities", e)
        }
    }
}