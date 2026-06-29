package com.sm.aethelread.domain.usecase

import com.sm.aethelread.data.local.preferences.PreferencesManager
import javax.inject.Inject

class SaveSelectedNovelUseCase @Inject constructor(
    private val preferencesManager: PreferencesManager,
) {
    suspend operator fun invoke(slug: String, name: String) {
        preferencesManager.saveSelectedNovel(slug, name)
    }
}