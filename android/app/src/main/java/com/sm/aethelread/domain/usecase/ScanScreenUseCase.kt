package com.sm.aethelread.domain.usecase

import android.graphics.Bitmap
import com.sm.aethelread.util.OcrEngine
import com.sm.aethelread.util.OcrResult
import javax.inject.Inject

class ScanScreenUseCase @Inject constructor(
    private val ocrEngine: OcrEngine,
) {
    suspend operator fun invoke(bitmap: Bitmap): OcrResult {
        return ocrEngine.processImage(bitmap)
    }

    suspend fun extractText(bitmap: Bitmap): String {
        return ocrEngine.extractText(bitmap)
    }
}