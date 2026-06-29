package com.sm.aethelread.presentation.novelselection

import androidx.lifecycle.viewModelScope
import com.sm.aethelread.domain.model.Novel
import com.sm.aethelread.domain.usecase.GetNovelsUseCase
import com.sm.aethelread.domain.usecase.SaveSelectedNovelUseCase
import com.sm.aethelread.presentation.base.BaseViewModel
import com.sm.aethelread.util.Resource
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.launch
import javax.inject.Inject

data class NovelSelectionUiState(
    val novels: List<Novel> = emptyList(),
    val isLoading: Boolean = false,
    val error: String? = null,
    val selectedNovelSlug: String? = null,
)

sealed class NovelSelectionEvent {
    data class SelectNovel(val novel: Novel) : NovelSelectionEvent()
    data object Refresh : NovelSelectionEvent()
}

@HiltViewModel
class NovelSelectionViewModel @Inject constructor(
    private val getNovelsUseCase: GetNovelsUseCase,
    private val saveSelectedNovelUseCase: SaveSelectedNovelUseCase,
) : BaseViewModel<NovelSelectionUiState>(NovelSelectionUiState()) {

    init {
        loadNovels()
    }

    fun onEvent(event: NovelSelectionEvent) {
        when (event) {
            is NovelSelectionEvent.SelectNovel -> selectNovel(event.novel)
            is NovelSelectionEvent.Refresh     -> loadNovels()
        }
    }

    private fun loadNovels() {
        viewModelScope.launch {
            getNovelsUseCase().collect { resource ->
                when (resource) {
                    is Resource.Loading -> updateState { copy(isLoading = true, error = null) }
                    is Resource.Success -> updateState {
                        copy(
                            novels    = resource.data,
                            isLoading = false,
                            error     = null,
                        )
                    }
                    is Resource.Error   -> updateState {
                        copy(isLoading = false, error = resource.message)
                    }
                }
            }
        }
    }

    private fun selectNovel(novel: Novel) {
        viewModelScope.launch {
            saveSelectedNovelUseCase(novel.slug, novel.name)
            updateState { copy(selectedNovelSlug = novel.slug) }
        }
    }
}