package com.sm.aethelread.domain.usecase

import com.sm.aethelread.domain.model.Entity
import com.sm.aethelread.domain.repository.EntityRepository
import com.sm.aethelread.util.Resource
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetEntitiesUseCase @Inject constructor(
    private val repository: EntityRepository,
) {
    operator fun invoke(novelSlug: String): Flow<Resource<List<Entity>>> {
        return repository.getEntities(novelSlug)
    }
}