package com.sm.aethelread.presentation.entitylist

import androidx.lifecycle.viewModelScope
import com.sm.aethelread.data.local.preferences.PreferencesManager
import com.sm.aethelread.domain.usecase.RecognizeEntitiesUseCase
import com.sm.aethelread.domain.usecase.SyncEntitiesUseCase
import com.sm.aethelread.presentation.base.BaseViewModel
import com.sm.aethelread.util.RecognitionMatch
import com.sm.aethelread.util.Resource
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.launch
import javax.inject.Inject

data class EntityListUiState(
    val matches: List<RecognitionMatch> = emptyList(),
    val isLoading: Boolean = false,
    val isScanning: Boolean = false,
    val error: String? = null,
    val ocrText: String = "",
    val novelSlug: String = "",
    val novelName: String = "",
)

sealed class EntityListEvent {
    data class SetNovel(val slug: String, val name: String) : EntityListEvent()
    data class OcrResult(val text: String) : EntityListEvent()
    data object Sync : EntityListEvent()
    data object ClearResults : EntityListEvent()
    data object LoadActiveNovel : EntityListEvent()
}

@HiltViewModel
class EntityListViewModel @Inject constructor(
    private val recognizeEntitiesUseCase: RecognizeEntitiesUseCase,
    private val syncEntitiesUseCase: SyncEntitiesUseCase,
    private val preferencesManager: PreferencesManager,
) : BaseViewModel<EntityListUiState>(EntityListUiState()) {

    fun onEvent(event: EntityListEvent) {
        when (event) {
            is EntityListEvent.SetNovel        -> setNovel(event.slug, event.name)
            is EntityListEvent.OcrResult       -> processOcrResult(event.text)
            is EntityListEvent.Sync            -> syncEntities()
            is EntityListEvent.ClearResults    -> updateState { copy(matches = emptyList(), ocrText = "") }
            is EntityListEvent.LoadActiveNovel -> loadActiveNovel()
        }
    }

    private fun loadActiveNovel() {
        viewModelScope.launch {
            val prefs = preferencesManager.appPreferences.first()
            if (prefs.selectedNovelSlug != null && prefs.selectedNovelName != null) {
                setNovel(prefs.selectedNovelSlug, prefs.selectedNovelName)
            }
        }
    }

    private fun setNovel(slug: String, name: String) {
        if (uiState.value.novelSlug != slug) {
            updateState { copy(novelSlug = slug, novelName = name) }
        }
    }

    private fun processOcrResult(text: String) {
        viewModelScope.launch {
            if (uiState.value.novelSlug.isEmpty()) {
                val prefs = preferencesManager.appPreferences.first()
                if (prefs.selectedNovelSlug != null && prefs.selectedNovelName != null) {
                    setNovel(prefs.selectedNovelSlug, prefs.selectedNovelName)
                } else {
                    updateState { copy(error = "No novel selected.") }
                    return@launch
                }
            }

            val novelSlug = uiState.value.novelSlug
            updateState { copy(isScanning = true, ocrText = text, error = null) }

            val matches = recognizeEntitiesUseCase(text, novelSlug)

            updateState {
                copy(
                    matches    = matches,
                    isScanning = false,
                    error      = if (matches.isEmpty()) "No entities recognized." else null,
                )
            }
        }
    }

    private fun syncEntities() {
        val novelSlug = uiState.value.novelSlug
        if (novelSlug.isEmpty()) return

        viewModelScope.launch {
            updateState { copy(isLoading = true, error = null) }

            when (val result = syncEntitiesUseCase(novelSlug)) {
                is Resource.Success -> updateState {
                    copy(
                        isLoading = false,
                        error     = "Sync complete. ${result.data.newCount} new, ${result.data.updatedCount} updated.",
                    )
                }
                is Resource.Error   -> updateState {
                    copy(isLoading = false, error = result.message)
                }
                is Resource.Loading -> Unit
            }
        }
    }
}