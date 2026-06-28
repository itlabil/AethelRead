package com.sm.aethelread.util

import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.catch
import kotlinx.coroutines.flow.map

// String Extensions
fun String.normalize(): String {
    return this
        .lowercase()
        .trim()
        .replace(Regex("\\s+"), " ")
        .replace(Regex("[^a-z0-9\\s]"), "")
}

fun String.containsNormalized(other: String): Boolean {
    return this.normalize().contains(other.normalize())
}

// Flow Extensions
fun <T> Flow<T>.asResource(): Flow<Resource<T>> {
    return this
        .map<T, Resource<T>> { Resource.Success(it) }
        .catch { emit(Resource.Error(it.message ?: "Unknown error", it)) }
}