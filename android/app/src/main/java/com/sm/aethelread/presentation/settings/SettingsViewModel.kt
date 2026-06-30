package com.sm.aethelread.presentation.settings

import androidx.lifecycle.viewModelScope
import com.sm.aethelread.data.local.preferences.PreferencesManager
import com.sm.aethelread.presentation.base.BaseViewModel
import com.sm.aethelread.worker.SyncScheduler
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.launch
import javax.inject.Inject

data class SettingsUiState(
    val selectedNovelSlug: String? = null,
    val selectedNovelName: String? = null,
    val appLocale: String = "en",
    val isLoading: Boolean = false,
)

sealed class SettingsEvent {
    data class ChangeLocale(val locale: String) : SettingsEvent()
    data object ClearSelectedNovel : SettingsEvent()
    data object TriggerSync : SettingsEvent()
}

@HiltViewModel
class SettingsViewModel @Inject constructor(
    private val preferencesManager: PreferencesManager,
    private val syncScheduler: SyncScheduler,
) : BaseViewModel<SettingsUiState>(SettingsUiState()) {

    init {
        loadPreferences()
    }

    fun onEvent(event: SettingsEvent) {
        when (event) {
            is SettingsEvent.ChangeLocale       -> changeLocale(event.locale)
            is SettingsEvent.ClearSelectedNovel -> clearSelectedNovel()
            is SettingsEvent.TriggerSync        -> triggerSync()
        }
    }

    private fun loadPreferences() {
        viewModelScope.launch {
            preferencesManager.appPreferences.collect { prefs ->
                updateState {
                    copy(
                        selectedNovelSlug = prefs.selectedNovelSlug,
                        selectedNovelName = prefs.selectedNovelName,
                        appLocale         = prefs.appLocale,
                    )
                }
            }
        }
    }

    private fun changeLocale(locale: String) {
        viewModelScope.launch {
            preferencesManager.saveAppLocale(locale)
        }
    }

    private fun clearSelectedNovel() {
        viewModelScope.launch {
            preferencesManager.clearSelectedNovel()
        }
    }

    private fun triggerSync() {
        syncScheduler.triggerImmediateSync()
    }
}