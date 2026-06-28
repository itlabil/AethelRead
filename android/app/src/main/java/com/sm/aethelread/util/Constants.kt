package com.sm.aethelread.util

object Constants {

    // Database
    const val DATABASE_NAME = "aethel_read.db"
    const val DATABASE_VERSION = 1

    // DataStore
    const val DATASTORE_NAME = "aethel_read_prefs"

    // Preferences Keys
    const val PREF_SELECTED_NOVEL_SLUG = "selected_novel_slug"
    const val PREF_SELECTED_NOVEL_NAME = "selected_novel_name"
    const val PREF_APP_LOCALE = "app_locale"
    const val PREF_BUBBLE_X = "bubble_x"
    const val PREF_BUBBLE_Y = "bubble_y"

    // Default Values
    const val DEFAULT_LOCALE = "en"

    // Network
    const val NETWORK_TIMEOUT = 30L // seconds
    const val NETWORK_MAX_RETRY = 3

    // Sync
    const val SYNC_WORK_NAME = "aethel_read_sync"
    const val SYNC_INTERVAL_HOURS = 6L

    // OCR
    const val OCR_MIN_CONFIDENCE = 0.7f
    const val OCR_MIN_TEXT_LENGTH = 2

    // Recognition
    const val RECOGNITION_MIN_MATCH_LENGTH = 3

    // Image
    const val IMAGE_PLACEHOLDER = "placeholder"
}