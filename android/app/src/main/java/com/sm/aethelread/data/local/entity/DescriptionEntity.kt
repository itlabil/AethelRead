package com.sm.aethelread.data.local.entity

import androidx.room.Entity

@Entity(
    tableName = "descriptions",
    primaryKeys = ["entitySlug", "locale"],
)
data class DescriptionEntity(
    val entitySlug: String,
    val locale: String,
    val content: String,
)