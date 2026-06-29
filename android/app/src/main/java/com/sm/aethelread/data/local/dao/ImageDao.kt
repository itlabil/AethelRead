package com.sm.aethelread.data.local.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.sm.aethelread.data.local.entity.ImageEntity

@Dao
interface ImageDao {
    @Query("SELECT * FROM images WHERE entitySlug = :entitySlug LIMIT 1")
    suspend fun getByEntity(entitySlug: String): ImageEntity?

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insert(image: ImageEntity)

    @Query("DELETE FROM images WHERE entitySlug = :entitySlug")
    suspend fun deleteByEntity(entitySlug: String)
}