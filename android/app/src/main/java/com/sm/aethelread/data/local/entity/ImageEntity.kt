package com.sm.aethelread.data.local.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "images")
data class ImageEntity(
    @PrimaryKey val entitySlug: String,
    val thumbnailUrl: String?,
    val originalUrl: String?,
    val hash: String?,
    val width: Int?,
    val height: Int?,
    val size: Long?,
)