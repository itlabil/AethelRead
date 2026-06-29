package com.sm.aethelread.data.local.database

import androidx.room.Database
import androidx.room.RoomDatabase
import com.sm.aethelread.data.local.dao.AliasDao
import com.sm.aethelread.data.local.dao.DescriptionDao
import com.sm.aethelread.data.local.dao.EntityDao
import com.sm.aethelread.data.local.dao.ImageDao
import com.sm.aethelread.data.local.dao.KeywordDao
import com.sm.aethelread.data.local.dao.NovelDao
import com.sm.aethelread.data.local.entity.AliasEntity
import com.sm.aethelread.data.local.entity.DescriptionEntity
import com.sm.aethelread.data.local.entity.EntityEntity
import com.sm.aethelread.data.local.entity.ImageEntity
import com.sm.aethelread.data.local.entity.KeywordEntity
import com.sm.aethelread.data.local.entity.NovelEntity

@Database(
    entities = [
        NovelEntity::class,
        EntityEntity::class,
        AliasEntity::class,
        KeywordEntity::class,
        DescriptionEntity::class,
        ImageEntity::class,
    ],
    version = 1,
    exportSchema = false,
)
abstract class AethelReadDatabase : RoomDatabase() {
    abstract fun novelDao(): NovelDao
    abstract fun entityDao(): EntityDao
    abstract fun aliasDao(): AliasDao
    abstract fun keywordDao(): KeywordDao
    abstract fun descriptionDao(): DescriptionDao
    abstract fun imageDao(): ImageDao
}