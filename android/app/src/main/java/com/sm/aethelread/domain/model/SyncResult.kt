package com.sm.aethelread.domain.model

data class SyncResult(
    val newCount: Int,
    val updatedCount: Int,
    val deletedSlugs: List<String>,
    val entities: List<Entity>,
)