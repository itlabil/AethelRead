package com.sm.aethelread.data.remote.dto

import com.google.gson.annotations.SerializedName

data class NovelDto(
    @SerializedName("slug")       val slug: String,
    @SerializedName("name")       val name: String,
    @SerializedName("type")       val type: String,
    @SerializedName("type_label") val typeLabel: String,
    @SerializedName("hash")       val hash: String?,
    @SerializedName("is_active")  val isActive: Boolean,
    @SerializedName("created_at") val createdAt: String?,
    @SerializedName("updated_at") val updatedAt: String?,
)