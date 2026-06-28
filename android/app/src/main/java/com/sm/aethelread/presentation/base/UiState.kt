package com.sm.aethelread.presentation.base

data class UiState<T>(
    val data: T? = null,
    val isLoading: Boolean = false,
    val error: String? = null,
    val isEmpty: Boolean = false,
) {
    val isSuccess get() = data != null && !isLoading && error == null
    val hasError get() = error != null
}