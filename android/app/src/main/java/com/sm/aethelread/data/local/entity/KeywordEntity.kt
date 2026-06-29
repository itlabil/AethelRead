package com.sm.aethelread.data.local.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "keywords")
data class KeywordEntity(
    @PrimaryKey val id: String,
    val entitySlug: String,
    val keyword: String,
)