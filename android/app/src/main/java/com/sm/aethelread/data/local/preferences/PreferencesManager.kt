package com.sm.aethelread.data.local.preferences

import androidx.datastore.core.DataStore
import androidx.datastore.preferences.core.Preferences
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.floatPreferencesKey
import androidx.datastore.preferences.core.stringPreferencesKey
import com.sm.aethelread.util.Constants
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class PreferencesManager @Inject constructor(
    private val dataStore: DataStore<Preferences>,
) {

    companion object {
        val KEY_SELECTED_NOVEL_SLUG = stringPreferencesKey(Constants.PREF_SELECTED_NOVEL_SLUG)
        val KEY_SELECTED_NOVEL_NAME = stringPreferencesKey(Constants.PREF_SELECTED_NOVEL_NAME)
        val KEY_APP_LOCALE          = stringPreferencesKey(Constants.PREF_APP_LOCALE)
        val KEY_BUBBLE_X            = floatPreferencesKey(Constants.PREF_BUBBLE_X)
        val KEY_BUBBLE_Y            = floatPreferencesKey(Constants.PREF_BUBBLE_Y)
    }

    /*
    |--------------------------------------------------------------------------
    | Selected Novel
    |--------------------------------------------------------------------------
    */

    val selectedNovelSlug: Flow<String?> = dataStore.data.map { prefs ->
        prefs[KEY_SELECTED_NOVEL_SLUG]
    }

    val selectedNovelName: Flow<String?> = dataStore.data.map { prefs ->
        prefs[KEY_SELECTED_NOVEL_NAME]
    }

    suspend fun saveSelectedNovel(slug: String, name: String) {
        dataStore.edit { prefs ->
            prefs[KEY_SELECTED_NOVEL_SLUG] = slug
            prefs[KEY_SELECTED_NOVEL_NAME] = name
        }
    }

    suspend fun clearSelectedNovel() {
        dataStore.edit { prefs ->
            prefs.remove(KEY_SELECTED_NOVEL_SLUG)
            prefs.remove(KEY_SELECTED_NOVEL_NAME)
        }
    }

    /*
    |--------------------------------------------------------------------------
    | App Locale
    |--------------------------------------------------------------------------
    */

    val appLocale: Flow<String> = dataStore.data.map { prefs ->
        prefs[KEY_APP_LOCALE] ?: Constants.DEFAULT_LOCALE
    }

    suspend fun saveAppLocale(locale: String) {
        dataStore.edit { prefs ->
            prefs[KEY_APP_LOCALE] = locale
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Bubble Position
    |--------------------------------------------------------------------------
    */

    val bubbleX: Flow<Float> = dataStore.data.map { prefs ->
        prefs[KEY_BUBBLE_X] ?: 0f
    }

    val bubbleY: Flow<Float> = dataStore.data.map { prefs ->
        prefs[KEY_BUBBLE_Y] ?: 200f
    }

    suspend fun saveBubblePosition(x: Float, y: Float) {
        dataStore.edit { prefs ->
            prefs[KEY_BUBBLE_X] = x
            prefs[KEY_BUBBLE_Y] = y
        }
    }

    /*
    |--------------------------------------------------------------------------
    | App Preferences (combined)
    |--------------------------------------------------------------------------
    */

    val appPreferences: Flow<AppPreferences> = dataStore.data.map { prefs ->
        AppPreferences(
            selectedNovelSlug = prefs[KEY_SELECTED_NOVEL_SLUG],
            selectedNovelName = prefs[KEY_SELECTED_NOVEL_NAME],
            appLocale         = prefs[KEY_APP_LOCALE] ?: Constants.DEFAULT_LOCALE,
            bubbleX           = prefs[KEY_BUBBLE_X] ?: 0f,
            bubbleY           = prefs[KEY_BUBBLE_Y] ?: 200f,
        )
    }
}

data class AppPreferences(
    val selectedNovelSlug: String?,
    val selectedNovelName: String?,
    val appLocale: String,
    val bubbleX: Float,
    val bubbleY: Float,
)