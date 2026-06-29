package com.sm.aethelread.presentation.navigation

import androidx.compose.runtime.Composable
import androidx.navigation.NavHostController
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import com.sm.aethelread.presentation.entitylist.EntityListScreen
import com.sm.aethelread.presentation.novelselection.NovelSelectionScreen

sealed class Screen(val route: String) {
    data object NovelSelection : Screen("novel_selection")
    data object EntityList     : Screen("entity_list/{novelSlug}/{novelName}") {
        fun createRoute(slug: String, name: String) =
            "entity_list/$slug/${name.replace("/", "_")}"
    }
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
                onNovelSelected = { novel ->
                    navController.navigate(
                        Screen.EntityList.createRoute(novel.slug, novel.name)
                    )
                },
            )
        }

        composable(Screen.EntityList.route) { backStackEntry ->
            val novelSlug = backStackEntry.arguments?.getString("novelSlug") ?: ""
            val novelName = backStackEntry.arguments?.getString("novelName") ?: ""

            EntityListScreen(
                novelSlug   = novelSlug,
                novelName   = novelName,
                ocrText     = "",
                onEntityClick = { entity ->
                    // Entity detail — Step 8
                },
                onBack      = { navController.popBackStack() },
            )
        }
    }
}