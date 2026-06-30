package com.sm.aethelread.service

import android.app.Notification
import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.app.Service
import android.content.Context
import android.content.Intent
import android.graphics.PixelFormat
import android.os.Build
import android.os.IBinder
import android.view.Gravity
import android.view.MotionEvent
import android.view.View
import android.widget.TextView
import androidx.compose.runtime.mutableStateOf
import androidx.core.app.NotificationCompat
import com.sm.aethelread.MainActivity
import com.sm.aethelread.data.local.preferences.PreferencesManager
import com.sm.aethelread.domain.usecase.RecognizeEntitiesUseCase
import com.sm.aethelread.domain.usecase.ScanScreenUseCase
import com.sm.aethelread.util.ScreenCaptureManager
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.SupervisorJob
import kotlinx.coroutines.cancel
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.launch
import javax.inject.Inject
import kotlin.math.abs

@AndroidEntryPoint
class BubbleService : Service() {

    @Inject lateinit var preferencesManager: PreferencesManager
    @Inject lateinit var scanScreenUseCase: ScanScreenUseCase
    @Inject lateinit var recognizeEntitiesUseCase: RecognizeEntitiesUseCase
    @Inject lateinit var screenCaptureManager: ScreenCaptureManager

    private lateinit var windowManager: android.view.WindowManager
    private var bubbleView: View? = null

    private val serviceScope = CoroutineScope(SupervisorJob() + Dispatchers.Main)

    companion object {
        const val CHANNEL_ID      = "aethel_read_bubble"
        const val NOTIFICATION_ID = 1001

        const val ACTION_START    = "ACTION_START"
        const val ACTION_STOP     = "ACTION_STOP"

        const val EXTRA_RESULT_CODE = "EXTRA_RESULT_CODE"
        const val EXTRA_RESULT_DATA = "EXTRA_RESULT_DATA"

        var isRunning = mutableStateOf(false)

        // Callback untuk kirim OCR result ke UI
        var onOcrResult: ((String) -> Unit)? = null
        var onScanStateChanged: ((Boolean) -> Unit)? = null

        fun start(context: Context, resultCode: Int, data: Intent) {
            android.util.Log.d("BubbleService", "start() called with resultCode=$resultCode")
            val intent = Intent(context, BubbleService::class.java).apply {
                action = ACTION_START
                putExtra(EXTRA_RESULT_CODE, resultCode)
                putExtra(EXTRA_RESULT_DATA, data)
            }
            android.util.Log.d("BubbleService", "Intent extras resultCode=${intent.getIntExtra(EXTRA_RESULT_CODE, -999)}")

            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                context.startForegroundService(intent)
            } else {
                context.startService(intent)
            }
        }

        fun stop(context: Context) {
            context.startService(
                Intent(context, BubbleService::class.java).apply {
                    action = ACTION_STOP
                }
            )
        }
    }

    override fun onBind(intent: Intent?): IBinder? = null

    override fun onCreate() {
        super.onCreate()
        windowManager = getSystemService(WINDOW_SERVICE) as android.view.WindowManager
        createNotificationChannel()
    }

    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
        android.util.Log.d("BubbleService", "onStartCommand action=${intent?.action}")
        when (intent?.action) {
            ACTION_START -> {
                val resultCode = intent.getIntExtra(EXTRA_RESULT_CODE, Int.MIN_VALUE)
                val data       = intent.getParcelableExtra<Intent>(EXTRA_RESULT_DATA)
                android.util.Log.d("BubbleService", "resultCode=$resultCode, data=$data")

                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
                    startForeground(
                        NOTIFICATION_ID,
                        buildNotification(),
                        android.content.pm.ServiceInfo.FOREGROUND_SERVICE_TYPE_MEDIA_PROJECTION,
                    )
                } else {
                    startForeground(NOTIFICATION_ID, buildNotification())
                }

                if (resultCode != Int.MIN_VALUE && data != null) {
                    screenCaptureManager.setupProjection(resultCode, data)
                } else {
                    android.util.Log.e("BubbleService", "Missing resultCode or data for projection!")
                }

                showBubble()
                isRunning.value = true
            }

            ACTION_STOP -> {
                removeBubble()
                screenCaptureManager.release()
                stopForeground(STOP_FOREGROUND_REMOVE)
                stopSelf()
                isRunning.value = false
            }
        }
        return START_STICKY
    }

    override fun onDestroy() {
        super.onDestroy()
        removeBubble()
        screenCaptureManager.release()
        serviceScope.cancel()
        isRunning.value = false
    }

    /*
    |--------------------------------------------------------------------------
    | Bubble UI
    |--------------------------------------------------------------------------
    */

    private fun showBubble() {
        if (bubbleView != null) return

        val layoutFlag = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            android.view.WindowManager.LayoutParams.TYPE_APPLICATION_OVERLAY
        } else {
            @Suppress("DEPRECATION")
            android.view.WindowManager.LayoutParams.TYPE_PHONE
        }

        val size   = (64 * resources.displayMetrics.density).toInt()

        // ← UPDATE BAGIAN INI
        val params = android.view.WindowManager.LayoutParams(
            size, size,
            layoutFlag,
            android.view.WindowManager.LayoutParams.FLAG_NOT_FOCUSABLE or
                    android.view.WindowManager.LayoutParams.FLAG_WATCH_OUTSIDE_TOUCH,
            PixelFormat.TRANSLUCENT,
        ).apply {
            gravity = Gravity.TOP or Gravity.START
            x = 0
            y = 200
        }

        // Restore saved position
        serviceScope.launch {
            val prefs = preferencesManager.appPreferences.first()
            params.x  = prefs.bubbleX.toInt()
            params.y  = prefs.bubbleY.toInt()
            if (bubbleView != null) {
                windowManager.updateViewLayout(bubbleView, params)
            }
        }

        bubbleView = createBubbleView(params)
        windowManager.addView(bubbleView, params)
    }

    private fun createBubbleView(params: android.view.WindowManager.LayoutParams): View {
        val size = (64 * resources.displayMetrics.density).toInt()

        return android.widget.FrameLayout(this).apply {
            layoutParams = android.widget.FrameLayout.LayoutParams(size, size)

            // Circle background
            background = android.graphics.drawable.GradientDrawable().apply {
                shape = android.graphics.drawable.GradientDrawable.OVAL
                setColor(android.graphics.Color.parseColor("#9333EA"))
                setStroke(
                    (2 * resources.displayMetrics.density).toInt(),
                    android.graphics.Color.parseColor("#7E22CE"),
                )
            }

            elevation = 8f * resources.displayMetrics.density

            // "A" label
            addView(
                TextView(context).apply {
                    text     = "A"
                    textSize = 22f
                    gravity  = Gravity.CENTER
                    typeface = android.graphics.Typeface.DEFAULT_BOLD
                    setTextColor(android.graphics.Color.WHITE)
                    layoutParams = android.widget.FrameLayout.LayoutParams(
                        size, size, Gravity.CENTER,
                    )
                }
            )

            setupDragAndClick(this, params)
        }
    }

    private fun setupDragAndClick(
        view: View,
        params: android.view.WindowManager.LayoutParams,
    ) {
        var initialX      = 0
        var initialY      = 0
        var initialTouchX = 0f
        var initialTouchY = 0f
        var isDragging    = false

        view.setOnTouchListener { _, event ->
            when (event.action) {
                MotionEvent.ACTION_DOWN -> {
                    android.util.Log.d("BubbleService", "ACTION_DOWN")
                    initialX      = params.x
                    initialY      = params.y
                    initialTouchX = event.rawX
                    initialTouchY = event.rawY
                    isDragging    = false
                    true
                }

                MotionEvent.ACTION_MOVE -> {
                    android.util.Log.d("BubbleService", "ACTION_MOVE")
                    val dx = event.rawX - initialTouchX
                    val dy = event.rawY - initialTouchY
                    if (abs(dx) > 10 || abs(dy) > 10) {
                        isDragging = true
                        params.x   = initialX + dx.toInt()
                        params.y   = initialY + dy.toInt()
                        windowManager.updateViewLayout(view, params)
                    }
                    true
                }

                MotionEvent.ACTION_UP -> {
                    android.util.Log.d("BubbleService", "ACTION_UP isDragging=$isDragging")
                    if (!isDragging) {
                        android.util.Log.d("BubbleService", "Calling performScan()")
                        performScan()
                    } else {
                        serviceScope.launch {
                            preferencesManager.saveBubblePosition(
                                params.x.toFloat(),
                                params.y.toFloat(),
                            )
                        }
                    }
                    isDragging = false
                    true
                }

                else -> false
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | OCR Pipeline
    |--------------------------------------------------------------------------
    */

    private fun performScan() {
        serviceScope.launch {
            onScanStateChanged?.invoke(true)

            try {
                val prefs = preferencesManager.appPreferences.first()
                val novelSlug = prefs.selectedNovelSlug

                if (novelSlug == null) {
                    showToast("Please select a novel first.")
                    onScanStateChanged?.invoke(false)
                    return@launch
                }

                val bitmap = screenCaptureManager.captureScreen()

                if (bitmap == null) {
                    showToast("Failed to capture screen. Try again.")
                    onScanStateChanged?.invoke(false)
                    return@launch
                }

                val ocrResult = scanScreenUseCase(bitmap)
                bitmap.recycle()

                if (ocrResult.isSuccess && ocrResult.fullText.isNotBlank()) {
                    bringAppToForeground()
                    onOcrResult?.invoke(ocrResult.fullText)
                } else {
                    showToast("No text detected on screen.")
                }
            } catch (e: Exception) {
                e.printStackTrace()
                showToast("Scan failed. Please try again.")
            } finally {
                onScanStateChanged?.invoke(false)
            }
        }
    }

    private fun showToast(message: String) {
        android.os.Handler(android.os.Looper.getMainLooper()).post {
            android.widget.Toast.makeText(this, message, android.widget.Toast.LENGTH_SHORT).show()
        }
    }

    private fun bringAppToForeground() {
        val intent = Intent(this, com.sm.aethelread.MainActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or
                    Intent.FLAG_ACTIVITY_REORDER_TO_FRONT or
                    Intent.FLAG_ACTIVITY_SINGLE_TOP
        }
        startActivity(intent)
    }

    private fun removeBubble() {
        bubbleView?.let {
            windowManager.removeView(it)
            bubbleView = null
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Notification
    |--------------------------------------------------------------------------
    */

    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                CHANNEL_ID,
                "Aethel Read Bubble",
                NotificationManager.IMPORTANCE_LOW,
            ).apply {
                description = "Aethel Read floating bubble service"
                setShowBadge(false)
            }
            getSystemService(NotificationManager::class.java)
                .createNotificationChannel(channel)
        }
    }

    private fun buildNotification(): Notification {
        val pendingIntent = PendingIntent.getActivity(
            this, 0,
            Intent(this, MainActivity::class.java),
            PendingIntent.FLAG_IMMUTABLE,
        )

        return NotificationCompat.Builder(this, CHANNEL_ID)
            .setContentTitle("Aethel Read")
            .setContentText("Reading companion is active — tap bubble to scan")
            .setSmallIcon(android.R.drawable.ic_menu_view)
            .setContentIntent(pendingIntent)
            .setOngoing(true)
            .setSilent(true)
            .build()
    }
}