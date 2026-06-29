package com.sm.aethelread.domain.model

data class Novel(
    val slug: String,
    val name: String,
    val type: String,
    val typeLabel: String,
    val hash: String?,
    val isActive: Boolean,
    val updatedAt: String?,
)