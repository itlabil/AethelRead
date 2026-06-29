package com.sm.aethelread.data.remote.api

import com.sm.aethelread.data.remote.dto.ApiResponse
import com.sm.aethelread.data.remote.dto.NovelDto
import retrofit2.http.GET

interface NovelApiService {

    @GET("novels")
    suspend fun getNovels(): ApiResponse<List<NovelDto>>
}