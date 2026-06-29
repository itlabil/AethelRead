package com.sm.aethelread.data.local.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.sm.aethelread.data.local.entity.AliasEntity

@Dao
interface AliasDao {
    @Query("SELECT * FROM aliases WHERE entitySlug = :entitySlug")
    suspend fun getByEntity(entitySlug: String): List<AliasEntity>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertAll(aliases: List<AliasEntity>)

    @Query("DELETE FROM aliases WHERE entitySlug = :entitySlug")
    suspend fun deleteByEntity(entitySlug: String)

    @Query("SELECT * FROM aliases")
    suspend fun getAll(): List<AliasEntity>
}