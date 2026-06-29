package com.sm.aethelread.presentation.entitydetail

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.ExperimentalLayoutApi
import androidx.compose.foundation.layout.FlowRow
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material3.AssistChip
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.FilterChip
import androidx.compose.material3.HorizontalDivider
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Scaffold
import androidx.compose.material3.SuggestionChip
import androidx.compose.material3.Text
import androidx.compose.material3.TopAppBar
import androidx.compose.material3.TopAppBarDefaults
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import coil.compose.AsyncImage
import com.sm.aethelread.domain.model.Entity
import com.sm.aethelread.presentation.components.ErrorMessage
import com.sm.aethelread.presentation.components.LoadingIndicator

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun EntityDetailScreen(
    novelSlug: String,
    entitySlug: String,
    onBack: () -> Unit,
    viewModel: EntityDetailViewModel = hiltViewModel(),
) {
    val uiState by viewModel.uiState.collectAsStateWithLifecycle()

    LaunchedEffect(entitySlug) {
        viewModel.onEvent(EntityDetailEvent.Load(novelSlug, entitySlug))
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        text  = uiState.entity?.name ?: "Entity Detail",
                        style = MaterialTheme.typography.titleMedium,
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

        when {
            uiState.isLoading -> LoadingIndicator()

            uiState.error != null -> ErrorMessage(
                message = uiState.error!!,
                onRetry = {
                    viewModel.onEvent(EntityDetailEvent.Load(novelSlug, entitySlug))
                },
            )

            uiState.entity != null -> EntityDetailContent(
                entity   = uiState.entity!!,
                locale   = uiState.locale,
                onLocaleSwitch = { locale ->
                    viewModel.onEvent(EntityDetailEvent.SwitchLocale(locale))
                },
                modifier = Modifier.padding(paddingValues),
            )
        }
    }
}

@OptIn(ExperimentalLayoutApi::class)
@Composable
private fun EntityDetailContent(
    entity: Entity,
    locale: String,
    onLocaleSwitch: (String) -> Unit,
    modifier: Modifier = Modifier,
) {
    Column(
        modifier = modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(16.dp),
    ) {

        // Image + Name Header
        Card(
            modifier = Modifier.fillMaxWidth(),
            shape    = RoundedCornerShape(20.dp),
            colors   = CardDefaults.cardColors(
                containerColor = MaterialTheme.colorScheme.primaryContainer,
            ),
        ) {
            Row(
                modifier          = Modifier
                    .fillMaxWidth()
                    .padding(16.dp),
                verticalAlignment = Alignment.CenterVertically,
            ) {
                // Image
                Box(
                    modifier         = Modifier
                        .size(80.dp)
                        .clip(RoundedCornerShape(16.dp))
                        .background(MaterialTheme.colorScheme.primary.copy(alpha = 0.2f)),
                    contentAlignment = Alignment.Center,
                ) {
                    if (entity.image?.thumbnailUrl != null) {
                        AsyncImage(
                            model              = entity.image.thumbnailUrl,
                            contentDescription = entity.name,
                            contentScale       = ContentScale.Crop,
                            modifier           = Modifier.fillMaxSize(),
                        )
                    } else {
                        Text(
                            text       = entity.name.first().uppercase(),
                            style      = MaterialTheme.typography.headlineMedium,
                            color      = MaterialTheme.colorScheme.onPrimaryContainer,
                            fontWeight = FontWeight.Bold,
                        )
                    }
                }

                Spacer(modifier = Modifier.width(16.dp))

                // Name & Type
                Column(modifier = Modifier.weight(1f)) {
                    Text(
                        text       = entity.name,
                        style      = MaterialTheme.typography.titleLarge,
                        fontWeight = FontWeight.Bold,
                        color      = MaterialTheme.colorScheme.onPrimaryContainer,
                    )
                    Spacer(modifier = Modifier.height(6.dp))
                    SuggestionChip(
                        onClick = {},
                        label   = {
                            Text(
                                text  = entity.typeLabel,
                                style = MaterialTheme.typography.labelSmall,
                            )
                        },
                        shape = RoundedCornerShape(8.dp),
                    )
                }
            }
        }

        Spacer(modifier = Modifier.height(16.dp))

        // Description Section
        if (entity.descriptions.isNotEmpty()) {
            Card(
                modifier = Modifier.fillMaxWidth(),
                shape    = RoundedCornerShape(16.dp),
                colors   = CardDefaults.cardColors(
                    containerColor = MaterialTheme.colorScheme.surface,
                ),
                elevation = CardDefaults.cardElevation(defaultElevation = 1.dp),
            ) {
                Column(modifier = Modifier.padding(16.dp)) {

                    // Locale switcher
                    Row(
                        modifier              = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment     = Alignment.CenterVertically,
                    ) {
                        Text(
                            text       = "Description",
                            style      = MaterialTheme.typography.titleSmall,
                            fontWeight = FontWeight.SemiBold,
                        )
                        Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                            listOf("en" to "EN", "id" to "ID").forEach { (code, label) ->
                                FilterChip(
                                    selected = locale == code,
                                    onClick  = { onLocaleSwitch(code) },
                                    label    = {
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

                    Spacer(modifier = Modifier.height(12.dp))
                    HorizontalDivider()
                    Spacer(modifier = Modifier.height(12.dp))

                    // Description content
                    val description = entity.descriptions.find { it.locale == locale }
                        ?: entity.descriptions.firstOrNull()

                    if (description != null) {
                        Text(
                            text  = description.content,
                            style = MaterialTheme.typography.bodyMedium,
                            color = MaterialTheme.colorScheme.onSurfaceVariant,
                        )
                    } else {
                        Text(
                            text  = "No description available.",
                            style = MaterialTheme.typography.bodyMedium,
                            color = MaterialTheme.colorScheme.onSurfaceVariant,
                        )
                    }
                }
            }

            Spacer(modifier = Modifier.height(12.dp))
        }

        // Aliases Section
        if (entity.aliases.isNotEmpty()) {
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
                        text       = "Also known as",
                        style      = MaterialTheme.typography.titleSmall,
                        fontWeight = FontWeight.SemiBold,
                    )
                    Spacer(modifier = Modifier.height(12.dp))
                    HorizontalDivider()
                    Spacer(modifier = Modifier.height(12.dp))
                    FlowRow(
                        horizontalArrangement = Arrangement.spacedBy(8.dp),
                        verticalArrangement   = Arrangement.spacedBy(8.dp),
                    ) {
                        entity.aliases.forEach { alias ->
                            AssistChip(
                                onClick = {},
                                label   = {
                                    Text(
                                        text  = alias.name,
                                        style = MaterialTheme.typography.labelMedium,
                                    )
                                },
                                shape = RoundedCornerShape(8.dp),
                            )
                        }
                    }
                }
            }

            Spacer(modifier = Modifier.height(12.dp))
        }

        // Novel Info
        Card(
            modifier  = Modifier.fillMaxWidth(),
            shape     = RoundedCornerShape(16.dp),
            colors    = CardDefaults.cardColors(
                containerColor = MaterialTheme.colorScheme.surfaceVariant,
            ),
        ) {
            Row(
                modifier          = Modifier
                    .fillMaxWidth()
                    .padding(16.dp),
                verticalAlignment = Alignment.CenterVertically,
            ) {
                Text(
                    text  = "Novel",
                    style = MaterialTheme.typography.labelMedium,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                )
                Spacer(modifier = Modifier.weight(1f))
                Text(
                    text       = entity.novelSlug,
                    style      = MaterialTheme.typography.labelMedium,
                    fontWeight = FontWeight.Medium,
                    color      = MaterialTheme.colorScheme.onSurface,
                )
            }
        }

        Spacer(modifier = Modifier.height(32.dp))
    }
}