package com.sm.aethelread.data.repository

import com.sm.aethelread.data.local.dao.NovelDao
import com.sm.aethelread.data.local.entity.toDomain
import com.sm.aethelread.data.local.entity.toEntity
import com.sm.aethelread.data.remote.api.NovelApiService
import com.sm.aethelread.data.remote.dto.toDomain
import com.sm.aethelread.domain.model.Novel
import com.sm.aethelread.domain.repository.NovelRepository
import com.sm.aethelread.util.NetworkUtils
import com.sm.aethelread.util.Resource
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.flow
import kotlinx.coroutines.flow.map
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class NovelRepositoryImpl @Inject constructor(
    private val novelDao: NovelDao,
    private val novelApiService: NovelApiService,
    private val networkUtils: NetworkUtils,
) : NovelRepository {

    // Memory cache
    private var memoryCache: List<Novel>? = null

    override fun getNovels(): Flow<Resource<List<Novel>>> = flow {
        emit(Resource.Loading)

        // Layer 1: Memory Cache
        memoryCache?.let {
            emit(Resource.Success(it))
            return@flow
        }

        // Layer 2: Room Database
        val localNovels = novelDao.getAllActive()
        localNovels.collect { entities ->
            if (entities.isNotEmpty()) {
                val novels = entities.map { it.toDomain() }
                memoryCache = novels
                emit(Resource.Success(novels))
            } else {
                // Layer 3: REST API
                if (networkUtils.isConnected()) {
                    val result = refreshNovels()
                    emit(result)
                } else {
                    emit(Resource.Error("No internet connection and no cached data available."))
                }
            }
        }
    }

    override suspend fun refreshNovels(): Resource<List<Novel>> {
        return try {
            val response = novelApiService.getNovels()
            if (response.success && response.data != null) {
                val novels = response.data.map { it.toDomain() }

                // Save to Room
                novelDao.deleteAll()
                novelDao.insertAll(novels.map { it.toEntity() })

                // Update memory cache
                memoryCache = novels

                Resource.Success(novels)
            } else {
                Resource.Error(response.message)
            }
        } catch (e: Exception) {
            Resource.Error(e.message ?: "Failed to fetch novels", e)
        }
    }

    override suspend fun getNovelBySlug(slug: String): Novel? {
        return novelDao.getBySlug(slug)?.toDomain()
    }
}