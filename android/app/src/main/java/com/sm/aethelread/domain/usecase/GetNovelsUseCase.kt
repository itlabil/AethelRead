package com.sm.aethelread.domain.usecase

import com.sm.aethelread.domain.model.Novel
import com.sm.aethelread.domain.repository.NovelRepository
import com.sm.aethelread.util.Resource
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetNovelsUseCase @Inject constructor(
    private val repository: NovelRepository,
) {
    operator fun invoke(): Flow<Resource<List<Novel>>> {
        return repository.getNovels()
    }
}