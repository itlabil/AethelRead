package com.sm.aethelread.worker

import android.content.Context
import androidx.hilt.work.HiltWorker
import androidx.work.CoroutineWorker
import androidx.work.WorkerParameters
import com.sm.aethelread.data.local.preferences.PreferencesManager
import com.sm.aethelread.domain.usecase.SyncEntitiesUseCase
import com.sm.aethelread.util.Resource
import dagger.assisted.Assisted
import dagger.assisted.AssistedInject
import kotlinx.coroutines.flow.first

@HiltWorker
class SyncWorker @AssistedInject constructor(
    @Assisted context: Context,
    @Assisted params: WorkerParameters,
    private val syncEntitiesUseCase: SyncEntitiesUseCase,
    private val preferencesManager: PreferencesManager,
) : CoroutineWorker(context, params) {

    override suspend fun doWork(): Result {
        return try {
            val prefs = preferencesManager.appPreferences.first()
            val novelSlug = prefs.selectedNovelSlug

            if (novelSlug.isNullOrBlank()) {
                // Tidak ada novel aktif, tidak perlu sync
                return Result.success()
            }

            when (val result = syncEntitiesUseCase(novelSlug)) {
                is Resource.Success -> {
                    android.util.Log.d(
                        "SyncWorker",
                        "Sync success: ${result.data.newCount} new, ${result.data.updatedCount} updated, ${result.data.deletedSlugs.size} deleted"
                    )
                    Result.success()
                }
                is Resource.Error -> {
                    android.util.Log.e("SyncWorker", "Sync failed: ${result.message}")
                    Result.retry()
                }
                is Resource.Loading -> Result.success()
            }
        } catch (e: Exception) {
            android.util.Log.e("SyncWorker", "Sync exception", e)
            Result.retry()
        }
    }

    companion object {
        const val WORK_NAME = "aethel_read_periodic_sync"
        const val WORK_NAME_ONE_TIME = "aethel_read_one_time_sync"
    }
}