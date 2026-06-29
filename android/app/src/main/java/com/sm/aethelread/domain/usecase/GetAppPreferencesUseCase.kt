package com.sm.aethelread.domain.usecase

import com.sm.aethelread.data.local.preferences.AppPreferences
import com.sm.aethelread.data.local.preferences.PreferencesManager
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetAppPreferencesUseCase @Inject constructor(
    private val preferencesManager: PreferencesManager,
) {
    operator fun invoke(): Flow<AppPreferences> {
        return preferencesManager.appPreferences
    }
}