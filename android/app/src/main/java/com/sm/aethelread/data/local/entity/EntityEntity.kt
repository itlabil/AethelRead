package com.sm.aethelread.data.local.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "entities")
data class EntityEntity(
    @PrimaryKey val slug: String,
    val novelSlug: String,
    val name: String,
    val type: String,
    val typeLabel: String,
    val hash: String?,
    val isActive: Boolean,
    val updatedAt: String?,
)