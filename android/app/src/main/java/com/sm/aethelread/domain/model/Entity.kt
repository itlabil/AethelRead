package com.sm.aethelread.domain.model

data class Entity(
    val slug: String,
    val novelSlug: String,
    val name: String,
    val type: String,
    val typeLabel: String,
    val hash: String?,
    val isActive: Boolean,
    val aliases: List<Alias> = emptyList(),
    val keywords: List<Keyword> = emptyList(),
    val descriptions: List<Description> = emptyList(),
    val image: Image? = null,
    val updatedAt: String?,
)

data class Alias(
    val id: String,
    val name: String,
)

data class Keyword(
    val id: String,
    val keyword: String,
)

data class Description(
    val locale: String,
    val content: String,
)

data class Image(
    val thumbnailUrl: String?,
    val originalUrl: String?,
    val hash: String?,
    val width: Int?,
    val height: Int?,
    val size: Long?,
)