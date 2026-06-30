package com.sm.aethelread.service

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.media.projection.MediaProjectionManager
import android.os.Bundle

class ScreenCaptureActivity : Activity() {

    companion object {
        const val REQUEST_CODE = 100

        fun start(context: Context) {
            context.startActivity(
                Intent(context, ScreenCaptureActivity::class.java).apply {
                    addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                }
            )
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val projectionManager =
            getSystemService(MEDIA_PROJECTION_SERVICE) as MediaProjectionManager

        startActivityForResult(
            projectionManager.createScreenCaptureIntent(),
            REQUEST_CODE,
        )
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)

        android.util.Log.d("ScreenCaptureActivity", "onActivityResult requestCode=$requestCode, resultCode=$resultCode, data=$data, RESULT_OK=$RESULT_OK")

        if (requestCode == REQUEST_CODE && resultCode == RESULT_OK && data != null) {
            android.util.Log.d("ScreenCaptureActivity", "Starting BubbleService with valid projection data")
            BubbleService.start(this, resultCode, data)
        } else {
            android.util.Log.e("ScreenCaptureActivity", "Permission denied or invalid result")
        }

        finish()
    }
}