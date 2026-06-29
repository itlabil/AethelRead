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
import android.view.LayoutInflater
import android.view.MotionEvent
import android.view.View
import android.view.WindowManager
import android.widget.ImageView
import android.widget.TextView
import androidx.compose.runtime.mutableStateOf
import androidx.core.app.NotificationCompat
import com.sm.aethelread.MainActivity
import com.sm.aethelread.R
import com.sm.aethelread.data.local.preferences.PreferencesManager
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

    @Inject
    lateinit var preferencesManager: PreferencesManager

    private lateinit var windowManager: WindowManager
    private var bubbleView: View? = null
    private var panelView: View? = null

    private val serviceScope = CoroutineScope(SupervisorJob() + Dispatchers.Main)

    companion object {
        const val CHANNEL_ID    = "aethel_read_bubble"
        const val NOTIFICATION_ID = 1001

        const val ACTION_START  = "ACTION_START"
        const val ACTION_STOP   = "ACTION_STOP"
        const val ACTION_SCAN   = "ACTION_SCAN"

        var isRunning = mutableStateOf(false)
        var onScanRequested: (() -> Unit)? = null

        fun start(context: Context) {
            val intent = Intent(context, BubbleService::class.java).apply {
                action = ACTION_START
            }
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                context.startForegroundService(intent)
            } else {
                context.startService(intent)
            }
        }

        fun stop(context: Context) {
            val intent = Intent(context, BubbleService::class.java).apply {
                action = ACTION_STOP
            }
            context.startService(intent)
        }
    }

    override fun onBind(intent: Intent?): IBinder? = null

    override fun onCreate() {
        super.onCreate()
        windowManager = getSystemService(WINDOW_SERVICE) as WindowManager
        createNotificationChannel()
    }

    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
        when (intent?.action) {
            ACTION_START -> {
                startForeground(NOTIFICATION_ID, buildNotification())
                showBubble()
                isRunning.value = true
            }
            ACTION_STOP -> {
                removeBubble()
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
            WindowManager.LayoutParams.TYPE_APPLICATION_OVERLAY
        } else {
            @Suppress("DEPRECATION")
            WindowManager.LayoutParams.TYPE_PHONE
        }

        val params = WindowManager.LayoutParams(
            WindowManager.LayoutParams.WRAP_CONTENT,
            WindowManager.LayoutParams.WRAP_CONTENT,
            layoutFlag,
            WindowManager.LayoutParams.FLAG_NOT_FOCUSABLE,
            PixelFormat.TRANSLUCENT,
        ).apply {
            gravity = Gravity.TOP or Gravity.START
            x = 0
            y = 200
        }

        // Restore last position
        serviceScope.launch {
            val prefs = preferencesManager.appPreferences.first()
            params.x = prefs.bubbleX.toInt()
            params.y = prefs.bubbleY.toInt()
            windowManager.updateViewLayout(bubbleView, params)
        }

        bubbleView = createBubbleView(params)
        windowManager.addView(bubbleView, params)
    }

    private fun createBubbleView(params: WindowManager.LayoutParams): View {
        val view = View(this).apply {
            // Draw bubble programmatically
            setBackgroundResource(android.R.drawable.btn_default)
        }

        // Buat bubble view menggunakan canvas
        val bubbleLayout = android.widget.FrameLayout(this).apply {
            val size = (64 * resources.displayMetrics.density).toInt()

            // Circle background
            val circle = View(context).apply {
                layoutParams = android.widget.FrameLayout.LayoutParams(size, size)
                background = androidx.core.content.ContextCompat.getDrawable(
                    context,
                    android.R.drawable.presence_online,
                )
            }

            // Scan button text
            val scanText = TextView(context).apply {
                text    = "A"
                textSize = 20f
                gravity = Gravity.CENTER
                setTextColor(android.graphics.Color.WHITE)
                layoutParams = android.widget.FrameLayout.LayoutParams(
                    size, size,
                    Gravity.CENTER,
                )
            }

            addView(circle)
            addView(scanText)
        }

        setupDragAndClick(bubbleLayout, params)
        return bubbleLayout
    }

    private fun setupDragAndClick(
        view: View,
        params: WindowManager.LayoutParams,
    ) {
        var initialX = 0
        var initialY = 0
        var initialTouchX = 0f
        var initialTouchY = 0f
        var isDragging = false

        view.setOnTouchListener { _, event ->
            when (event.action) {
                MotionEvent.ACTION_DOWN -> {
                    initialX      = params.x
                    initialY      = params.y
                    initialTouchX = event.rawX
                    initialTouchY = event.rawY
                    isDragging    = false
                    true
                }

                MotionEvent.ACTION_MOVE -> {
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
                    if (!isDragging) {
                        // Tap = Scan
                        onScanRequested?.invoke()
                    } else {
                        // Save position
                        serviceScope.launch {
                            preferencesManager.saveBubblePosition(
                                params.x.toFloat(),
                                params.y.toFloat(),
                            )
                        }
                    }
                    true
                }

                else -> false
            }
        }
    }

    private fun removeBubble() {
        bubbleView?.let {
            windowManager.removeView(it)
            bubbleView = null
        }
        panelView?.let {
            windowManager.removeView(it)
            panelView = null
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
            val manager = getSystemService(NotificationManager::class.java)
            manager.createNotificationChannel(channel)
        }
    }

    private fun buildNotification(): Notification {
        val pendingIntent = PendingIntent.getActivity(
            this,
            0,
            Intent(this, MainActivity::class.java),
            PendingIntent.FLAG_IMMUTABLE,
        )

        return NotificationCompat.Builder(this, CHANNEL_ID)
            .setContentTitle("Aethel Read")
            .setContentText("Reading companion is active")
            .setSmallIcon(android.R.drawable.ic_menu_view)
            .setContentIntent(pendingIntent)
            .setOngoing(true)
            .setSilent(true)
            .build()
    }
}