package com.sm.aethelread.presentation.navigation

import androidx.compose.runtime.Composable
import androidx.navigation.NavHostController
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import com.sm.aethelread.presentation.novelselection.NovelSelectionScreen

sealed class Screen(val route: String) {
    data object NovelSelection : Screen("novel_selection")
    data object Settings       : Screen("settings")
}

@Composable
fun AethelReadNavGraph(
    navController: NavHostController = rememberNavController(),
) {
    NavHost(
        navController    = navController,
        startDestination = Screen.NovelSelection.route,
    ) {
        composable(Screen.NovelSelection.route) {
            NovelSelectionScreen(
                onNovelSelected = {
                    // Akan diupdate di step berikutnya
                },
            )
        }

        // Screens lain akan ditambahkan di step berikutnya
    }
}