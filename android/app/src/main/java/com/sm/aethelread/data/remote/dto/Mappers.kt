package com.sm.aethelread.data.remote.dto

import com.sm.aethelread.domain.model.Alias
import com.sm.aethelread.domain.model.Description
import com.sm.aethelread.domain.model.Entity
import com.sm.aethelread.domain.model.Image
import com.sm.aethelread.domain.model.Keyword
import com.sm.aethelread.domain.model.Novel
import com.sm.aethelread.domain.model.SyncResult

// Novel DTO → Domain Model
fun NovelDto.toDomain(): Novel = Novel(
    slug      = slug,
    name      = name,
    type      = type,
    typeLabel = typeLabel,
    hash      = hash,
    isActive  = isActive,
    updatedAt = updatedAt,
)

// Entity DTO → Domain Model
fun EntityDto.toDomain(): Entity = Entity(
    slug         = slug,
    novelSlug    = novel?.slug ?: "",
    name         = name,
    type         = type,
    typeLabel    = typeLabel,
    hash         = hash,
    isActive     = isActive,
    aliases      = aliases?.map { it.toDomain() } ?: emptyList(),
    keywords     = keywords?.map { it.toDomain() } ?: emptyList(),
    descriptions = descriptions?.map { it.toDomain() } ?: emptyList(),
    image        = image?.toDomain(),
    updatedAt    = updatedAt,
)

fun AliasDto.toDomain(): Alias = Alias(id = id, name = name)

fun KeywordDto.toDomain(): Keyword = Keyword(id = id, keyword = keyword)

fun DescriptionDto.toDomain(): Description = Description(locale = locale, content = content)

fun ImageDto.toDomain(): Image = Image(
    thumbnailUrl = thumbnailUrl,
    originalUrl  = originalUrl,
    hash         = hash,
    width        = width,
    height       = height,
    size         = size,
)

// Sync Response → Domain Model
fun SyncResponse.toDomain(): SyncResult = SyncResult(
    newCount      = sync.new,
    updatedCount  = sync.updated,
    deletedSlugs  = sync.deleted,
    entities      = entities.map { it.toDomain() },
)