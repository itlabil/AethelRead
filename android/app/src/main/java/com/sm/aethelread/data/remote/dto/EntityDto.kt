package com.sm.aethelread.data.remote.dto

import com.google.gson.annotations.SerializedName

data class EntityDto(
    @SerializedName("slug")         val slug: String,
    @SerializedName("name")         val name: String,
    @SerializedName("type")         val type: String,
    @SerializedName("type_label")   val typeLabel: String,
    @SerializedName("hash")         val hash: String?,
    @SerializedName("is_active")    val isActive: Boolean,
    @SerializedName("novel")        val novel: NovelRefDto?,
    @SerializedName("aliases")      val aliases: List<AliasDto>?,
    @SerializedName("keywords")     val keywords: List<KeywordDto>?,
    @SerializedName("descriptions") val descriptions: List<DescriptionDto>?,
    @SerializedName("image")        val image: ImageDto?,
    @SerializedName("created_at")   val createdAt: String?,
    @SerializedName("updated_at")   val updatedAt: String?,
)

data class NovelRefDto(
    @SerializedName("slug") val slug: String,
    @SerializedName("name") val name: String,
)

data class AliasDto(
    @SerializedName("id")   val id: String,
    @SerializedName("name") val name: String,
)

data class KeywordDto(
    @SerializedName("id")      val id: String,
    @SerializedName("keyword") val keyword: String,
)

data class DescriptionDto(
    @SerializedName("locale")  val locale: String,
    @SerializedName("content") val content: String,
)

data class ImageDto(
    @SerializedName("thumbnail_url") val thumbnailUrl: String?,
    @SerializedName("original_url")  val originalUrl: String?,
    @SerializedName("hash")          val hash: String?,
    @SerializedName("width")         val width: Int?,
    @SerializedName("height")        val height: Int?,
    @SerializedName("size")          val size: Long?,
)