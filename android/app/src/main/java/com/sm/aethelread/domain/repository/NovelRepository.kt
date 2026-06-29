package com.sm.aethelread.domain.repository

import com.sm.aethelread.domain.model.Novel
import com.sm.aethelread.util.Resource
import kotlinx.coroutines.flow.Flow

interface NovelRepository {

    /**
     * Get all active novels from Smart Cache.
     * Priority: Memory → Room → API
     */
    fun getNovels(): Flow<Resource<List<Novel>>>

    /**
     * Force refresh novels from API.
     */
    suspend fun refreshNovels(): Resource<List<Novel>>

    /**
     * Get novel by slug from local cache.
     */
    suspend fun getNovelBySlug(slug: String): Novel?
}