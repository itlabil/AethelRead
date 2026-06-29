package com.sm.aethelread.data.remote.api

import com.sm.aethelread.data.remote.dto.ApiResponse
import com.sm.aethelread.data.remote.dto.EntityDto
import com.sm.aethelread.data.remote.dto.SyncRequest
import com.sm.aethelread.data.remote.dto.SyncResponse
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path
import retrofit2.http.Query

interface EntityApiService {

    @GET("novels/{novelSlug}/entities")
    suspend fun getEntities(
        @Path("novelSlug") novelSlug: String,
    ): ApiResponse<List<EntityDto>>

    @GET("novels/{novelSlug}/entities/{entitySlug}")
    suspend fun getEntity(
        @Path("novelSlug") novelSlug: String,
        @Path("entitySlug") entitySlug: String,
        @Query("locale") locale: String = "en",
    ): ApiResponse<EntityDto>

    @POST("novels/{novelSlug}/entities/sync")
    suspend fun syncEntities(
        @Path("novelSlug") novelSlug: String,
        @Body request: SyncRequest,
    ): ApiResponse<SyncResponse>
}