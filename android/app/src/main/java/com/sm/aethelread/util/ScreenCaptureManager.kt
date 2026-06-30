package com.sm.aethelread.util

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.graphics.Bitmap
import android.graphics.PixelFormat
import android.hardware.display.DisplayManager
import android.hardware.display.VirtualDisplay
import android.media.ImageReader
import android.media.projection.MediaProjection
import android.media.projection.MediaProjectionManager
import android.os.Handler
import android.os.Looper
import android.util.DisplayMetrics
import android.view.WindowManager
import dagger.hilt.android.qualifiers.ApplicationContext
import kotlinx.coroutines.suspendCancellableCoroutine
import javax.inject.Inject
import javax.inject.Singleton
import kotlin.coroutines.resume

@Singleton
class ScreenCaptureManager @Inject constructor(
    @ApplicationContext private val context: Context,
) {
    private var mediaProjection: MediaProjection? = null
    private var virtualDisplay: VirtualDisplay? = null
    private var imageReader: ImageReader? = null

    private val windowManager = context.getSystemService(Context.WINDOW_SERVICE) as WindowManager
    private val projectionManager =
        context.getSystemService(Context.MEDIA_PROJECTION_SERVICE) as MediaProjectionManager

    fun getProjectionIntent(): Intent {
        return projectionManager.createScreenCaptureIntent()
    }

    fun setupProjection(resultCode: Int, data: Intent) {
        android.util.Log.d("ScreenCaptureManager", "setupProjection called, resultCode=$resultCode")
        mediaProjection = projectionManager.getMediaProjection(resultCode, data)

        // Wajib register callback sebelum createVirtualDisplay (Android 14+)
        mediaProjection?.registerCallback(object : MediaProjection.Callback() {
            override fun onStop() {
                android.util.Log.d("ScreenCaptureManager", "MediaProjection stopped")
                stopCapture()
                mediaProjection = null
            }
        }, Handler(Looper.getMainLooper()))

        android.util.Log.d("ScreenCaptureManager", "mediaProjection=$mediaProjection")
    }

    /**
     * Capture current screen as Bitmap.
     */
    suspend fun captureScreen(): Bitmap? {
        val projection = mediaProjection
        android.util.Log.d("ScreenCaptureManager", "captureScreen called, projection=$projection")
        if (projection == null) return null

        return suspendCancellableCoroutine { continuation ->
            val metrics = DisplayMetrics()

            @Suppress("DEPRECATION")
            windowManager.defaultDisplay.getMetrics(metrics)

            val width  = metrics.widthPixels
            val height = metrics.heightPixels
            val dpi    = metrics.densityDpi

            android.util.Log.d("ScreenCaptureManager", "width=$width, height=$height, dpi=$dpi")

            imageReader = ImageReader.newInstance(width, height, PixelFormat.RGBA_8888, 2)

            try {
                virtualDisplay = projection.createVirtualDisplay(
                    "AethelReadCapture",
                    width, height, dpi,
                    DisplayManager.VIRTUAL_DISPLAY_FLAG_AUTO_MIRROR,
                    imageReader!!.surface,
                    null, null,
                )
                android.util.Log.d("ScreenCaptureManager", "virtualDisplay created=$virtualDisplay")
            } catch (e: Exception) {
                android.util.Log.e("ScreenCaptureManager", "createVirtualDisplay failed", e)
                continuation.resume(null)
                return@suspendCancellableCoroutine
            }

            Handler(Looper.getMainLooper()).postDelayed({
                tryAcquireImage(continuation, width, height, attempt = 1)
            }, 300)

            continuation.invokeOnCancellation {
                stopCapture()
            }
        }
    }

    private fun tryAcquireImage(
        continuation: kotlin.coroutines.Continuation<Bitmap?>,
        width: Int,
        height: Int,
        attempt: Int,
    ) {
        try {
            val image = imageReader?.acquireLatestImage()
            android.util.Log.d("ScreenCaptureManager", "tryAcquireImage attempt=$attempt, image=$image")

            if (image != null) {
                val planes = image.planes
                val buffer = planes[0].buffer
                val pixelStride = planes[0].pixelStride
                val rowStride   = planes[0].rowStride
                val rowPadding  = rowStride - pixelStride * width

                val bitmap = Bitmap.createBitmap(
                    width + rowPadding / pixelStride,
                    height,
                    Bitmap.Config.ARGB_8888,
                )
                bitmap.copyPixelsFromBuffer(buffer)
                image.close()

                val croppedBitmap = Bitmap.createBitmap(bitmap, 0, 0, width, height)
                bitmap.recycle()

                stopCapture()
                continuation.resume(croppedBitmap)
            } else if (attempt < 5) {
                // Retry up to 5 times with increasing delay
                android.util.Log.d("ScreenCaptureManager", "Image not ready, retrying... attempt=$attempt")
                Handler(Looper.getMainLooper()).postDelayed({
                    tryAcquireImage(continuation, width, height, attempt + 1)
                }, 200)
            } else {
                android.util.Log.e("ScreenCaptureManager", "Failed to acquire image after $attempt attempts")
                stopCapture()
                continuation.resume(null)
            }
        } catch (e: Exception) {
            android.util.Log.e("ScreenCaptureManager", "Error processing image", e)
            stopCapture()
            continuation.resume(null)
        }
    }

    fun stopCapture() {
        virtualDisplay?.release()
        imageReader?.close()
        virtualDisplay = null
        imageReader    = null
    }

    fun release() {
        stopCapture()
        mediaProjection?.stop()
        mediaProjection = null
    }

    fun isReady(): Boolean = mediaProjection != null
}