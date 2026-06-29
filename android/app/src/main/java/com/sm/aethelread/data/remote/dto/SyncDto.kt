package com.sm.aethelread.data.remote.dto

import com.google.gson.annotations.SerializedName

data class SyncRequest(
    @SerializedName("hashes") val hashes: Map<String, String>,
)

data class SyncResponse(
    @SerializedName("sync")     val sync: SyncMeta,
    @SerializedName("entities") val entities: List<EntityDto>,
)

data class SyncMeta(
    @SerializedName("new")     val new: Int,
    @SerializedName("updated") val updated: Int,
    @SerializedName("deleted") val deleted: List<String>,
)