package com.sm.aethelread.data.local.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.sm.aethelread.data.local.entity.KeywordEntity

@Dao
interface KeywordDao {
    @Query("SELECT * FROM keywords WHERE entitySlug = :entitySlug")
    suspend fun getByEntity(entitySlug: String): List<KeywordEntity>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertAll(keywords: List<KeywordEntity>)

    @Query("DELETE FROM keywords WHERE entitySlug = :entitySlug")
    suspend fun deleteByEntity(entitySlug: String)

    @Query("SELECT * FROM keywords")
    suspend fun getAll(): List<KeywordEntity>
}