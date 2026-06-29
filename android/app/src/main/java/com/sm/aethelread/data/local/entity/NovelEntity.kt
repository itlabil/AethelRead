package com.sm.aethelread.data.local.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "novels")
data class NovelEntity(
    @PrimaryKey val slug: String,
    val name: String,
    val type: String,
    val typeLabel: String,
    val hash: String?,
    val isActive: Boolean,
    val updatedAt: String?,
)