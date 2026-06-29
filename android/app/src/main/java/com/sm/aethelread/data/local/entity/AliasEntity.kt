package com.sm.aethelread.data.local.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "aliases")
data class AliasEntity(
    @PrimaryKey val id: String,
    val entitySlug: String,
    val name: String,
)