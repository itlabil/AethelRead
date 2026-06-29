package com.sm.aethelread.domain.usecase

import com.sm.aethelread.domain.repository.EntityRepository
import com.sm.aethelread.util.RecognitionEngine
import com.sm.aethelread.util.RecognitionMatch
import javax.inject.Inject

class RecognizeEntitiesUseCase @Inject constructor(
    private val entityRepository: EntityRepository,
    private val recognitionEngine: RecognitionEngine,
) {
    suspend operator fun invoke(
        ocrText: String,
        novelSlug: String,
    ): List<RecognitionMatch> {
        val entities = entityRepository.getEntitiesForRecognition(novelSlug)
        return recognitionEngine.recognize(ocrText, entities)
    }
}