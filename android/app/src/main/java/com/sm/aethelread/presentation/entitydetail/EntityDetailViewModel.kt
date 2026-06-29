package com.sm.aethelread.presentation.entitydetail

import androidx.lifecycle.viewModelScope
import com.sm.aethelread.data.local.preferences.PreferencesManager
import com.sm.aethelread.domain.model.Entity
import com.sm.aethelread.domain.usecase.GetEntityDetailUseCase
import com.sm.aethelread.presentation.base.BaseViewModel
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.launch
import javax.inject.Inject

data class EntityDetailUiState(
    val entity: Entity? = null,
    val isLoading: Boolean = false,
    val error: String? = null,
    val locale: String = "en",
)

sealed class EntityDetailEvent {
    data class Load(val novelSlug: String, val entitySlug: String) : EntityDetailEvent()
    data class SwitchLocale(val locale: String) : EntityDetailEvent()
}

@HiltViewModel
class EntityDetailViewModel @Inject constructor(
    private val getEntityDetailUseCase: GetEntityDetailUseCase,
    private val preferencesManager: PreferencesManager,
) : BaseViewModel<EntityDetailUiState>(EntityDetailUiState()) {

    fun onEvent(event: EntityDetailEvent) {
        when (event) {
            is EntityDetailEvent.Load        -> loadEntity(event.novelSlug, event.entitySlug)
            is EntityDetailEvent.SwitchLocale -> updateState { copy(locale = event.locale) }
        }
    }

    private fun loadEntity(novelSlug: String, entitySlug: String) {
        viewModelScope.launch {
            updateState { copy(isLoading = true, error = null) }

            // Get preferred locale
            val locale = preferencesManager.appLocale.first()
            updateState { copy(locale = locale) }

            val entity = getEntityDetailUseCase(novelSlug, entitySlug)

            if (entity != null) {
                updateState { copy(entity = entity, isLoading = false) }
            } else {
                updateState { copy(isLoading = false, error = "Entity not found.") }
            }
        }
    }
}