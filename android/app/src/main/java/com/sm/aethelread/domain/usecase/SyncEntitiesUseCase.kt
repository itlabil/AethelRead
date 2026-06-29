package com.sm.aethelread.domain.usecase

import com.sm.aethelread.domain.model.SyncResult
import com.sm.aethelread.domain.repository.EntityRepository
import com.sm.aethelread.util.Resource
import javax.inject.Inject

class SyncEntitiesUseCase @Inject constructor(
    private val repository: EntityRepository,
) {
    suspend operator fun invoke(novelSlug: String): Resource<SyncResult> {
        return repository.syncEntities(novelSlug)
    }
}