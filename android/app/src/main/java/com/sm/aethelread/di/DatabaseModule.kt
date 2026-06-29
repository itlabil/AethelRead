package com.sm.aethelread.di

import android.content.Context
import androidx.room.Room
import com.sm.aethelread.data.local.database.AethelReadDatabase
import com.sm.aethelread.util.Constants
import dagger.Module
import dagger.Provides
import dagger.hilt.InstallIn
import dagger.hilt.android.qualifiers.ApplicationContext
import dagger.hilt.components.SingletonComponent
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
object DatabaseModule {

    @Provides
    @Singleton
    fun provideDatabase(
        @ApplicationContext context: Context,
    ): AethelReadDatabase {
        return Room.databaseBuilder(
            context,
            AethelReadDatabase::class.java,
            Constants.DATABASE_NAME,
        )
            .fallbackToDestructiveMigration()
            .build()
    }

    @Provides
    fun provideNovelDao(database: AethelReadDatabase) = database.novelDao()

    @Provides
    fun provideEntityDao(database: AethelReadDatabase) = database.entityDao()

    @Provides
    fun provideAliasDao(database: AethelReadDatabase) = database.aliasDao()

    @Provides
    fun provideKeywordDao(database: AethelReadDatabase) = database.keywordDao()

    @Provides
    fun provideDescriptionDao(database: AethelReadDatabase) = database.descriptionDao()

    @Provides
    fun provideImageDao(database: AethelReadDatabase) = database.imageDao()
}