package com.sm.aethelread.di

import com.sm.aethelread.data.repository.EntityRepositoryImpl
import com.sm.aethelread.data.repository.NovelRepositoryImpl
import com.sm.aethelread.domain.repository.EntityRepository
import com.sm.aethelread.domain.repository.NovelRepository
import dagger.Binds
import dagger.Module
import dagger.hilt.InstallIn
import dagger.hilt.components.SingletonComponent
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
abstract class RepositoryModule {

    @Binds
    @Singleton
    abstract fun bindNovelRepository(
        impl: NovelRepositoryImpl,
    ): NovelRepository

    @Binds
    @Singleton
    abstract fun bindEntityRepository(
        impl: EntityRepositoryImpl,
    ): EntityRepository
}