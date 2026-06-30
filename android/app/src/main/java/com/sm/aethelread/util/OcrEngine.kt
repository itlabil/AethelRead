package com.sm.aethelread.util

import android.content.Context
import android.graphics.Bitmap
import com.google.mlkit.vision.common.InputImage
import com.google.mlkit.vision.text.TextRecognition
import com.google.mlkit.vision.text.latin.TextRecognizerOptions
import dagger.hilt.android.qualifiers.ApplicationContext
import kotlinx.coroutines.suspendCancellableCoroutine
import javax.inject.Inject
import javax.inject.Singleton
import kotlin.coroutines.resume
import kotlin.coroutines.resumeWithException

@Singleton
class OcrEngine @Inject constructor(
    @ApplicationContext private val context: Context,
) {
    private val recognizer = TextRecognition.getClient(TextRecognizerOptions.DEFAULT_OPTIONS)

    /**
     * Process bitmap and extract text using ML Kit.
     * Returns raw OCR text result.
     */
    suspend fun processImage(bitmap: Bitmap): OcrResult {
        return suspendCancellableCoroutine { continuation ->
            val image = InputImage.fromBitmap(bitmap, 0)

            recognizer.process(image)
                .addOnSuccessListener { visionText ->
                    val blocks = visionText.textBlocks.map { block ->
                        OcrBlock(
                            text       = block.text,
                            lines      = block.lines.map { line ->
                                OcrLine(
                                    text     = line.text,
                                    elements = line.elements.map { element ->
                                        OcrElement(
                                            text       = element.text,
                                            confidence = element.confidence ?: 0f,
                                        )
                                    }
                                )
                            }
                        )
                    }

                    continuation.resume(
                        OcrResult(
                            fullText   = visionText.text,
                            blocks     = blocks,
                            isSuccess  = true,
                        )
                    )
                }
                .addOnFailureListener { exception ->
                    continuation.resume(
                        OcrResult(
                            fullText  = "",
                            blocks    = emptyList(),
                            isSuccess = false,
                            error     = exception.message,
                        )
                    )
                }

            continuation.invokeOnCancellation {
                recognizer.close()
            }
        }
    }

    /**
     * Process bitmap and return only the full text string.
     */
    suspend fun extractText(bitmap: Bitmap): String {
        val result = processImage(bitmap)
        return if (result.isSuccess) result.fullText else ""
    }
}

data class OcrResult(
    val fullText: String,
    val blocks: List<OcrBlock>,
    val isSuccess: Boolean,
    val error: String? = null,
)

data class OcrBlock(
    val text: String,
    val lines: List<OcrLine>,
)

data class OcrLine(
    val text: String,
    val elements: List<OcrElement>,
)

data class OcrElement(
    val text: String,
    val confidence: Float,
)