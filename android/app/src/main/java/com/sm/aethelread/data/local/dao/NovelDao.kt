package com.sm.aethelread.data.local.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.sm.aethelread.data.local.entity.NovelEntity
import kotlinx.coroutines.flow.Flow

@Dao
interface NovelDao {
    @Query("SELECT * FROM novels WHERE isActive = 1 ORDER BY name ASC")
    fun getAllActive(): Flow<List<NovelEntity>>

    @Query("SELECT * FROM novels WHERE slug = :slug LIMIT 1")
    suspend fun getBySlug(slug: String): NovelEntity?

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertAll(novels: List<NovelEntity>)

    @Query("DELETE FROM novels")
    suspend fun deleteAll()
}