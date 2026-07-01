package com.sm.aethelread.presentation.entitylist

import androidx.lifecycle.viewModelScope
import com.sm.aethelread.data.local.preferences.PreferencesManager
import com.sm.aethelread.domain.model.Entity
import com.sm.aethelread.domain.usecase.GetEntitiesUseCase
import com.sm.aethelread.domain.usecase.RecognizeEntitiesUseCase
import com.sm.aethelread.domain.usecase.SyncEntitiesUseCase
import com.sm.aethelread.presentation.base.BaseViewModel
import com.sm.aethelread.util.MatchType
import com.sm.aethelread.util.NetworkObserver
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
    val isOffline: Boolean = false,
    val isShowingScanResult: Boolean = false,
)

sealed class EntityListEvent {
    data class SetNovel(val slug: String, val name: String) : EntityListEvent()
    data class OcrResult(val text: String) : EntityListEvent()
    data object Sync : EntityListEvent()
    data object ClearResults : EntityListEvent()
    data object LoadActiveNovel : EntityListEvent()
    data object ShowAllEntities : EntityListEvent()
}

@HiltViewModel
class EntityListViewModel @Inject constructor(
    private val recognizeEntitiesUseCase: RecognizeEntitiesUseCase,
    private val getEntitiesUseCase: GetEntitiesUseCase,
    private val syncEntitiesUseCase: SyncEntitiesUseCase,
    private val preferencesManager: PreferencesManager,
    private val networkObserver: NetworkObserver,
) : BaseViewModel<EntityListUiState>(EntityListUiState()) {

    init {
        observeNetwork()
    }

    fun onEvent(event: EntityListEvent) {
        when (event) {
            is EntityListEvent.SetNovel        -> setNovel(event.slug, event.name)
            is EntityListEvent.OcrResult       -> processOcrResult(event.text)
            is EntityListEvent.Sync            -> syncEntities()
            is EntityListEvent.ClearResults    -> showAllEntities()
            is EntityListEvent.LoadActiveNovel -> loadActiveNovel()
            is EntityListEvent.ShowAllEntities -> showAllEntities()
        }
    }

    private fun observeNetwork() {
        viewModelScope.launch {
            networkObserver.observe().collect { isConnected ->
                updateState { copy(isOffline = !isConnected) }
            }
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
        val isNewNovel = uiState.value.novelSlug != slug
        updateState { copy(novelSlug = slug, novelName = name) }

        if (isNewNovel) {
            loadAllEntities(slug)
        }
    }

    /**
     * Load and display ALL entities for the novel (browse mode).
     */
    private fun loadAllEntities(novelSlug: String) {
        viewModelScope.launch {
            updateState { copy(isLoading = true, error = null, isShowingScanResult = false) }

            getEntitiesUseCase(novelSlug).collect { resource ->
                when (resource) {
                    is Resource.Loading -> updateState { copy(isLoading = true) }
                    is Resource.Success -> {
                        val matches = resource.data.map { entity ->
                            RecognitionMatch(
                                entity      = entity,
                                matchedText = entity.name,
                                matchType   = MatchType.MAIN_NAME,
                                confidence  = 1.0f,
                            )
                        }.sortedBy { it.entity.name }

                        updateState {
                            copy(
                                matches    = matches,
                                isLoading  = false,
                                error      = if (matches.isEmpty()) "No entities found for this novel." else null,
                                isShowingScanResult = false,
                            )
                        }
                    }
                    is Resource.Error -> updateState {
                        copy(
                            isLoading = false,
                            error     = if (matches.isEmpty()) resource.message else null,
                        )
                    }
                }
            }
        }
    }

    /**
     * Switch back to browse mode (show all entities).
     */
    private fun showAllEntities() {
        val novelSlug = uiState.value.novelSlug
        if (novelSlug.isNotEmpty()) {
            loadAllEntities(novelSlug)
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
                    isShowingScanResult = true,
                    error      = if (matches.isEmpty()) "No entities recognized from scan." else null,
                )
            }
        }
    }

    private fun syncEntities() {
        val novelSlug = uiState.value.novelSlug
        if (novelSlug.isEmpty()) return

        if (uiState.value.isOffline) {
            updateState { copy(error = "Cannot sync while offline.") }
            return
        }

        viewModelScope.launch {
            updateState { copy(isLoading = true, error = null) }

            when (val result = syncEntitiesUseCase(novelSlug)) {
                is Resource.Success -> {
                    // Refresh list setelah sync
                    loadAllEntities(novelSlug)
                }
                is Resource.Error -> updateState {
                    copy(isLoading = false, error = result.message)
                }
                is Resource.Loading -> Unit
            }
        }
    }
}