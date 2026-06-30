package com.sm.aethelread.presentation.settings

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.FilterChip
import androidx.compose.material3.HorizontalDivider
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.material3.TopAppBar
import androidx.compose.material3.TopAppBarDefaults
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import androidx.compose.material3.Switch
import androidx.compose.runtime.getValue
import androidx.compose.ui.platform.LocalContext
import com.sm.aethelread.service.BubbleService
import com.sm.aethelread.util.BubblePermissionHelper
import com.sm.aethelread.service.ScreenCaptureActivity

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SettingsScreen(
    onBack: () -> Unit,
    viewModel: SettingsViewModel = hiltViewModel(),
) {
    val uiState by viewModel.uiState.collectAsStateWithLifecycle()
    val context = LocalContext.current
    val isBubbleRunning by BubbleService.isRunning

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        text       = "Settings",
                        style      = MaterialTheme.typography.titleMedium,
                        fontWeight = FontWeight.SemiBold,
                    )
                },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(
                            imageVector        = Icons.Default.ArrowBack,
                            contentDescription = "Back",
                            tint               = MaterialTheme.colorScheme.onPrimary,
                        )
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor    = MaterialTheme.colorScheme.primary,
                    titleContentColor = MaterialTheme.colorScheme.onPrimary,
                ),
            )
        },
    ) { paddingValues ->

        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .verticalScroll(rememberScrollState())
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp),
        ) {

            // Active Novel Section
            SettingsSection(title = "Reading") {
                SettingsRow(
                    label   = "Active Novel",
                    value   = uiState.selectedNovelName ?: "None selected",
                )
                if (uiState.selectedNovelSlug != null) {
                    Spacer(modifier = Modifier.height(8.dp))
                    OutlinedButton(
                        onClick = {
                            viewModel.onEvent(SettingsEvent.ClearSelectedNovel)
                        },
                        shape    = RoundedCornerShape(10.dp),
                        modifier = Modifier.fillMaxWidth(),
                    ) {
                        Text(
                            text  = "Clear Selected Novel",
                            style = MaterialTheme.typography.labelMedium,
                        )
                    }
                }
            }

            // Language Section
            SettingsSection(title = "Language") {
                Row(
                    modifier              = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment     = Alignment.CenterVertically,
                ) {
                    Text(
                        text  = "Description Language",
                        style = MaterialTheme.typography.bodyMedium,
                        color = MaterialTheme.colorScheme.onSurface,
                    )
                    Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                        listOf("en" to "English", "id" to "Indonesia").forEach { (code, label) ->
                            FilterChip(
                                selected = uiState.appLocale == code,
                                onClick  = {
                                    viewModel.onEvent(SettingsEvent.ChangeLocale(code))
                                },
                                label = {
                                    Text(
                                        text  = label,
                                        style = MaterialTheme.typography.labelSmall,
                                    )
                                },
                                shape = RoundedCornerShape(8.dp),
                            )
                        }
                    }
                }
            }

            // App Info Section
            SettingsSection(title = "About") {
                SettingsRow(label = "App Name",  value = "Aethel Read")
                HorizontalDivider(modifier = Modifier.padding(vertical = 8.dp))
                SettingsRow(label = "Version",   value = "1.0.0")
                HorizontalDivider(modifier = Modifier.padding(vertical = 8.dp))
                SettingsRow(label = "Category",  value = "Reading Companion")
                HorizontalDivider(modifier = Modifier.padding(vertical = 8.dp))
                SettingsRow(label = "Platform",  value = "Android")
            }

            // Floating Bubble Section
            SettingsSection(title = "Floating Bubble") {
                Row(
                    modifier              = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment     = Alignment.CenterVertically,
                ) {
                    Column {
                        Text(
                            text  = "Reading Companion",
                            style = MaterialTheme.typography.bodyMedium,
                            color = MaterialTheme.colorScheme.onSurface,
                        )
                        Text(
                            text  = if (BubbleService.isRunning.value) "Active" else "Inactive",
                            style = MaterialTheme.typography.labelSmall,
                            color = if (BubbleService.isRunning.value) {
                                MaterialTheme.colorScheme.primary
                            } else {
                                MaterialTheme.colorScheme.onSurfaceVariant
                            },
                        )
                    }

                    Switch(
                        checked         = isBubbleRunning,
                        onCheckedChange = { enabled ->
                            if (enabled) {
                                if (BubblePermissionHelper.hasOverlayPermission(context)) {
                                    ScreenCaptureActivity.start(context)
                                } else {
                                    BubblePermissionHelper.requestOverlayPermission(context)
                                }
                            } else {
                                BubbleService.stop(context)
                            }
                        },
                    )
                }
            }

            // Sync Section
            SettingsSection(title = "Data Sync") {
                Row(
                    modifier              = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment     = Alignment.CenterVertically,
                ) {
                    Column {
                        Text(
                            text  = "Sync Now",
                            style = MaterialTheme.typography.bodyMedium,
                            color = MaterialTheme.colorScheme.onSurface,
                        )
                        Text(
                            text  = "Automatically syncs every 6 hours",
                            style = MaterialTheme.typography.labelSmall,
                            color = MaterialTheme.colorScheme.onSurfaceVariant,
                        )
                    }

                    OutlinedButton(
                        onClick = { viewModel.onEvent(SettingsEvent.TriggerSync) },
                        shape   = RoundedCornerShape(10.dp),
                    ) {
                        Text(
                            text  = "Sync",
                            style = MaterialTheme.typography.labelMedium,
                        )
                    }
                }
            }

        }
    }
}

@Composable
private fun SettingsSection(
    title: String,
    content: @Composable () -> Unit,
) {
    Card(
        modifier  = Modifier.fillMaxWidth(),
        shape     = RoundedCornerShape(16.dp),
        colors    = CardDefaults.cardColors(
            containerColor = MaterialTheme.colorScheme.surface,
        ),
        elevation = CardDefaults.cardElevation(defaultElevation = 1.dp),
    ) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text(
                text       = title,
                style      = MaterialTheme.typography.labelLarge,
                fontWeight = FontWeight.SemiBold,
                color      = MaterialTheme.colorScheme.primary,
            )
            Spacer(modifier = Modifier.height(12.dp))
            HorizontalDivider()
            Spacer(modifier = Modifier.height(12.dp))
            content()
        }
    }
}

@Composable
private fun SettingsRow(
    label: String,
    value: String,
) {
    Row(
        modifier              = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment     = Alignment.CenterVertically,
    ) {
        Text(
            text  = label,
            style = MaterialTheme.typography.bodyMedium,
            color = MaterialTheme.colorScheme.onSurfaceVariant,
        )
        Text(
            text       = value,
            style      = MaterialTheme.typography.bodyMedium,
            fontWeight = FontWeight.Medium,
            color      = MaterialTheme.colorScheme.onSurface,
        )
    }
}

