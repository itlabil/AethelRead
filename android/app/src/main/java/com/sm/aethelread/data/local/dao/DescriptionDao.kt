package com.sm.aethelread.data.local.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.sm.aethelread.data.local.entity.DescriptionEntity

@Dao
interface DescriptionDao {
    @Query("SELECT * FROM descriptions WHERE entitySlug = :entitySlug AND locale = :locale LIMIT 1")
    suspend fun getByEntityAndLocale(entitySlug: String, locale: String): DescriptionEntity?

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertAll(descriptions: List<DescriptionEntity>)

    @Query("DELETE FROM descriptions WHERE entitySlug = :entitySlug")
    suspend fun deleteByEntity(entitySlug: String)
}