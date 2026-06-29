package com.sm.aethelread.data.local.entity

import com.sm.aethelread.domain.model.Alias
import com.sm.aethelread.domain.model.Description
import com.sm.aethelread.domain.model.Entity
import com.sm.aethelread.domain.model.Image
import com.sm.aethelread.domain.model.Keyword
import com.sm.aethelread.domain.model.Novel

// Domain → Local Entity
fun Novel.toEntity(): NovelEntity = NovelEntity(
    slug      = slug,
    name      = name,
    type      = type,
    typeLabel = typeLabel,
    hash      = hash,
    isActive  = isActive,
    updatedAt = updatedAt,
)

fun Entity.toEntity(): EntityEntity = EntityEntity(
    slug      = slug,
    novelSlug = novelSlug,
    name      = name,
    type      = type,
    typeLabel = typeLabel,
    hash      = hash,
    isActive  = isActive,
    updatedAt = updatedAt,
)

fun Alias.toEntity(entitySlug: String): AliasEntity = AliasEntity(
    id         = id,
    entitySlug = entitySlug,
    name       = name,
)

fun Keyword.toEntity(entitySlug: String): KeywordEntity = KeywordEntity(
    id         = id,
    entitySlug = entitySlug,
    keyword    = keyword,
)

fun Description.toEntity(entitySlug: String): DescriptionEntity = DescriptionEntity(
    entitySlug = entitySlug,
    locale     = locale,
    content    = content,
)

fun Image.toEntity(entitySlug: String): ImageEntity = ImageEntity(
    entitySlug   = entitySlug,
    thumbnailUrl = thumbnailUrl,
    originalUrl  = originalUrl,
    hash         = hash,
    width        = width,
    height       = height,
    size         = size,
)

// Local Entity → Domain
fun NovelEntity.toDomain(): Novel = Novel(
    slug      = slug,
    name      = name,
    type      = type,
    typeLabel = typeLabel,
    hash      = hash,
    isActive  = isActive,
    updatedAt = updatedAt,
)

fun EntityEntity.toDomain(
    aliases: List<AliasEntity> = emptyList(),
    keywords: List<KeywordEntity> = emptyList(),
    descriptions: List<DescriptionEntity> = emptyList(),
    image: ImageEntity? = null,
): Entity = Entity(
    slug         = slug,
    novelSlug    = novelSlug,
    name         = name,
    type         = type,
    typeLabel    = typeLabel,
    hash         = hash,
    isActive     = isActive,
    aliases      = aliases.map { Alias(it.id, it.name) },
    keywords     = keywords.map { Keyword(it.id, it.keyword) },
    descriptions = descriptions.map { Description(it.locale, it.content) },
    image        = image?.let {
        Image(it.thumbnailUrl, it.originalUrl, it.hash, it.width, it.height, it.size)
    },
    updatedAt    = updatedAt,
)