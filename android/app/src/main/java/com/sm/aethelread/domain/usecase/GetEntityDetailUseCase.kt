package com.sm.aethelread.domain.usecase

import com.sm.aethelread.domain.model.Entity
import com.sm.aethelread.domain.repository.EntityRepository
import javax.inject.Inject

class GetEntityDetailUseCase @Inject constructor(
    private val repository: EntityRepository,
) {
    suspend operator fun invoke(novelSlug: String, entitySlug: String): Entity? {
        return repository.getEntityBySlug(novelSlug, entitySlug)
    }
}