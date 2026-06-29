package com.sm.aethelread.util

object Normalizer {

    /**
     * Normalize text for matching.
     * - Lowercase
     * - Trim whitespace
     * - Remove punctuation
     * - Normalize spaces
     */
    fun normalize(text: String): String {
        return text
            .lowercase()
            .trim()
            .replace(Regex("[^a-z0-9\\s]"), " ")
            .replace(Regex("\\s+"), " ")
            .trim()
    }

    /**
     * Normalize OCR result — additional OCR-specific corrections.
     */
    fun normalizeOcr(text: String): String {
        return normalize(text)
            .replace("0", "o")  // OCR often mistakes 0 for o
            .replace("1", "l")  // OCR often mistakes 1 for l
            .replace(Regex("\\s+"), " ")
            .trim()
    }

    /**
     * Split text into individual words for matching.
     */
    fun tokenize(text: String): List<String> {
        return normalize(text)
            .split(" ")
            .filter { it.length >= Constants.RECOGNITION_MIN_MATCH_LENGTH }
    }

    /**
     * Check if text contains the query (normalized).
     */
    fun contains(text: String, query: String): Boolean {
        val normalizedText  = normalize(text)
        val normalizedQuery = normalize(query)
        return normalizedText.contains(normalizedQuery)
    }
}