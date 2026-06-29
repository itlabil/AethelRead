package com.sm.aethelread.util

import com.sm.aethelread.domain.model.Entity
import javax.inject.Inject
import javax.inject.Singleton

data class RecognitionMatch(
    val entity: Entity,
    val matchedText: String,
    val matchType: MatchType,
    val confidence: Float,
)

enum class MatchType {
    MAIN_NAME,
    ALIAS,
    KEYWORD,
}

@Singleton
class RecognitionEngine @Inject constructor() {

    /**
     * Recognize entities from OCR text.
     * Searches by: Main Name → Alias → Keyword
     */
    fun recognize(
        ocrText: String,
        entities: List<Entity>,
    ): List<RecognitionMatch> {
        if (ocrText.isBlank() || entities.isEmpty()) return emptyList()

        val normalizedOcr = Normalizer.normalize(ocrText)
        val matches       = mutableListOf<RecognitionMatch>()
        val foundSlugs    = mutableSetOf<String>()

        entities.forEach { entity ->
            if (entity.slug in foundSlugs) return@forEach

            // 1. Match by Main Name
            val normalizedName = Normalizer.normalize(entity.name)
            if (normalizedName.length >= Constants.RECOGNITION_MIN_MATCH_LENGTH
                && normalizedOcr.contains(normalizedName)
            ) {
                matches.add(
                    RecognitionMatch(
                        entity      = entity,
                        matchedText = entity.name,
                        matchType   = MatchType.MAIN_NAME,
                        confidence  = 1.0f,
                    )
                )
                foundSlugs.add(entity.slug)
                return@forEach
            }

            // 2. Match by Aliases
            for (alias in entity.aliases) {
                val normalizedAlias = Normalizer.normalize(alias.name)
                if (normalizedAlias.length >= Constants.RECOGNITION_MIN_MATCH_LENGTH
                    && normalizedOcr.contains(normalizedAlias)
                ) {
                    matches.add(
                        RecognitionMatch(
                            entity      = entity,
                            matchedText = alias.name,
                            matchType   = MatchType.ALIAS,
                            confidence  = 0.9f,
                        )
                    )
                    foundSlugs.add(entity.slug)
                    return@forEach
                }
            }

            // 3. Match by Keywords
            for (keyword in entity.keywords) {
                val normalizedKeyword = Normalizer.normalize(keyword.keyword)
                if (normalizedKeyword.length >= Constants.RECOGNITION_MIN_MATCH_LENGTH
                    && normalizedOcr.contains(normalizedKeyword)
                ) {
                    matches.add(
                        RecognitionMatch(
                            entity      = entity,
                            matchedText = keyword.keyword,
                            matchType   = MatchType.KEYWORD,
                            confidence  = 0.7f,
                        )
                    )
                    foundSlugs.add(entity.slug)
                    return@forEach
                }
            }
        }

        // Sort by confidence descending
        return matches.sortedByDescending { it.confidence }
    }
}