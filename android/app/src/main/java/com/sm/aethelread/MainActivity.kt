package com.sm.aethelread

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.runtime.DisposableEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.core.splashscreen.SplashScreen.Companion.installSplashScreen
import com.sm.aethelread.presentation.navigation.AethelReadNavGraph
import com.sm.aethelread.presentation.theme.AethelReadTheme
import com.sm.aethelread.service.BubbleService
import dagger.hilt.android.AndroidEntryPoint

@AndroidEntryPoint
class MainActivity : ComponentActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        installSplashScreen()
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()

        setContent {
            var pendingOcrText by remember { mutableStateOf<String?>(null) }
            var ocrTriggerCount by remember { mutableStateOf(0) }

            DisposableEffect(Unit) {
                android.util.Log.d("MainActivity", "Registering onOcrResult callback")
                BubbleService.onOcrResult = { text ->
                    android.util.Log.d("MainActivity", "onOcrResult received: ${text.take(50)}")
                    pendingOcrText = text
                    ocrTriggerCount++
                    android.util.Log.d("MainActivity", "ocrTriggerCount=$ocrTriggerCount")
                }
                onDispose {
                    android.util.Log.d("MainActivity", "Disposing onOcrResult callback")
                    BubbleService.onOcrResult = null
                }
            }

            AethelReadTheme {
                AethelReadNavGraph(
                    pendingOcrText   = pendingOcrText,
                    ocrTriggerCount  = ocrTriggerCount,
                )
            }
        }
    }
}