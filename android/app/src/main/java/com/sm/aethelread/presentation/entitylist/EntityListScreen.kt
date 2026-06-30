package com.sm.aethelread.presentation.entitylist

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Scaffold
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
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.lifecycle.compose.collectAsStateWithLifecycle
import coil.compose.AsyncImage
import com.sm.aethelread.domain.model.Entity
import com.sm.aethelread.presentation.components.EmptyState
import com.sm.aethelread.presentation.components.LoadingIndicator
import com.sm.aethelread.util.MatchType
import com.sm.aethelread.util.RecognitionMatch
import com.sm.aethelread.presentation.components.OfflineBanner

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun EntityListScreen(
    novelSlug: String,
    novelName: String,
    ocrText: String,
    onEntityClick: (Entity) -> Unit,
    onBack: () -> Unit,
    viewModel: EntityListViewModel = hiltViewModel(),
) {
    val uiState by viewModel.uiState.collectAsStateWithLifecycle()

    LaunchedEffect(novelSlug) {
        if (novelSlug.isNotEmpty()) {
            viewModel.onEvent(EntityListEvent.SetNovel(novelSlug, novelName))
        }
    }

    LaunchedEffect(ocrText) {
        if (ocrText.isNotBlank()) {
            viewModel.onEvent(EntityListEvent.OcrResult(ocrText))
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Column {
                        Text(
                            text  = novelName,
                            style = MaterialTheme.typography.titleMedium,
                            fontWeight = FontWeight.SemiBold,
                        )
                        Text(
                            text  = "${uiState.matches.size} entities found",
                            style = MaterialTheme.typography.bodySmall,
                            color = MaterialTheme.colorScheme.onPrimaryContainer,
                        )
                    }
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
                actions = {
                    if (uiState.isLoading) {
                        CircularProgressIndicator(
                            modifier = Modifier
                                .size(24.dp)
                                .padding(end = 16.dp),
                            color       = MaterialTheme.colorScheme.onPrimary,
                            strokeWidth = 2.dp,
                        )
                    } else {
                        IconButton(
                            onClick = { viewModel.onEvent(EntityListEvent.Sync) }
                        ) {
                            Icon(
                                imageVector        = Icons.Default.Refresh,
                                contentDescription = "Sync",
                                tint               = MaterialTheme.colorScheme.onPrimary,
                            )
                        }
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor    = MaterialTheme.colorScheme.primary,
                    titleContentColor = MaterialTheme.colorScheme.onPrimary,
                ),
            )
        },
    ) { paddingValues ->

        Column(modifier = Modifier.padding(paddingValues)) {

            OfflineBanner(isVisible = uiState.isOffline)

            when {
                uiState.isScanning -> {
                    LoadingIndicator()
                }

                uiState.matches.isEmpty() -> {
                    EmptyState(
                        title    = "No entities recognized",
                        subtitle = "Tap Scan on the floating bubble to scan screen text",
                    )
                }

                else -> {
                    LazyColumn(
                        modifier            = Modifier.fillMaxSize(),
                        contentPadding      = PaddingValues(16.dp),
                        verticalArrangement = Arrangement.spacedBy(12.dp),
                    ) {
                        items(
                            items = uiState.matches,
                            key   = { it.entity.slug },
                        ) { match ->
                            EntityMatchCard(
                                match   = match,
                                onClick = { onEntityClick(match.entity) },
                            )
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun EntityMatchCard(
    match: RecognitionMatch,
    onClick: () -> Unit,
    modifier: Modifier = Modifier,
) {
    Card(
        modifier  = modifier
            .fillMaxWidth()
            .clickable(onClick = onClick),
        shape     = RoundedCornerShape(16.dp),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
        colors    = CardDefaults.cardColors(
            containerColor = MaterialTheme.colorScheme.surface,
        ),
    ) {
        Row(
            modifier          = Modifier
                .fillMaxWidth()
                .padding(12.dp),
            verticalAlignment = Alignment.CenterVertically,
        ) {
            // Thumbnail
            Box(
                modifier          = Modifier
                    .size(56.dp)
                    .clip(RoundedCornerShape(12.dp))
                    .background(MaterialTheme.colorScheme.primaryContainer),
                contentAlignment  = Alignment.Center,
            ) {
                if (match.entity.image?.thumbnailUrl != null) {
                    AsyncImage(
                        model             = match.entity.image.thumbnailUrl,
                        contentDescription = match.entity.name,
                        contentScale      = ContentScale.Crop,
                        modifier          = Modifier.fillMaxSize(),
                    )
                } else {
                    Text(
                        text  = match.entity.name.first().uppercase(),
                        style = MaterialTheme.typography.titleLarge,
                        color = MaterialTheme.colorScheme.onPrimaryContainer,
                        fontWeight = FontWeight.Bold,
                    )
                }
            }

            Spacer(modifier = Modifier.width(12.dp))

            // Info
            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text      = match.entity.name,
                    style     = MaterialTheme.typography.titleSmall,
                    fontWeight = FontWeight.SemiBold,
                    maxLines  = 1,
                    overflow  = TextOverflow.Ellipsis,
                )
                Spacer(modifier = Modifier.height(4.dp))
                Text(
                    text  = match.entity.typeLabel,
                    style = MaterialTheme.typography.labelSmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                )
                if (match.matchType != MatchType.MAIN_NAME) {
                    Spacer(modifier = Modifier.height(2.dp))
                    Text(
                        text  = "Matched: ${match.matchedText}",
                        style = MaterialTheme.typography.labelSmall,
                        color = MaterialTheme.colorScheme.primary,
                    )
                }
            }

            // Match type badge
            MatchTypeBadge(matchType = match.matchType)
        }
    }
}

@Composable
private fun MatchTypeBadge(matchType: MatchType) {
    val (color, label) = when (matchType) {
        MatchType.MAIN_NAME -> Pair(MaterialTheme.colorScheme.primary, "Name")
        MatchType.ALIAS     -> Pair(MaterialTheme.colorScheme.secondary, "Alias")
        MatchType.KEYWORD   -> Pair(MaterialTheme.colorScheme.tertiary, "Keyword")
    }

    Box(
        modifier         = Modifier
            .clip(CircleShape)
            .background(color.copy(alpha = 0.15f))
            .padding(horizontal = 8.dp, vertical = 4.dp),
        contentAlignment = Alignment.Center,
    ) {
        Text(
            text  = label,
            style = MaterialTheme.typography.labelSmall,
            color = color,
            fontWeight = FontWeight.Medium,
        )
    }
}