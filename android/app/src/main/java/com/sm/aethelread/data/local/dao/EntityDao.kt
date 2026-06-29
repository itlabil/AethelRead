package com.sm.aethelread.data.local.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.sm.aethelread.data.local.entity.EntityEntity
import kotlinx.coroutines.flow.Flow

@Dao
interface EntityDao {
    @Query("SELECT * FROM entities WHERE novelSlug = :novelSlug AND isActive = 1 ORDER BY name ASC")
    fun getAllByNovel(novelSlug: String): Flow<List<EntityEntity>>

    @Query("SELECT * FROM entities WHERE slug = :slug LIMIT 1")
    suspend fun getBySlug(slug: String): EntityEntity?

    @Query("SELECT * FROM entities WHERE novelSlug = :novelSlug AND isActive = 1")
    suspend fun getAllByNovelSync(novelSlug: String): List<EntityEntity>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertAll(entities: List<EntityEntity>)

    @Query("DELETE FROM entities WHERE slug = :slug")
    suspend fun deleteBySlug(slug: String)

    @Query("DELETE FROM entities WHERE novelSlug = :novelSlug")
    suspend fun deleteAllByNovel(novelSlug: String)

    @Query("SELECT slug, hash FROM entities WHERE novelSlug = :novelSlug AND isActive = 1")
    suspend fun getSlugHashMap(novelSlug: String): List<SlugHash>
}

data class SlugHash(val slug: String, val hash: String?)