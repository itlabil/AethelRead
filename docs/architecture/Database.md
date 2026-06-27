# Database Design

## Purpose

This document defines the conceptual, logical, and physical database design for Aethel Read.

The database serves as the single persistent storage for all official application data.

Its design follows the architectural principles defined in Architecture.md.

---

# 1. Design Goals

The database is designed to achieve the following goals.

- High maintainability
- High consistency
- Database portability
- Efficient synchronization
- Extensible schema
- Minimal redundancy

---

# 2. Conceptual Data Model

The database is centered around the Entity domain.

```text
User
 │
 └────────────┐
              │
              ▼
            Novel
              │
              │
      ┌───────┴────────┐
      │                │
      ▼                ▼
   Entity         Description
      │
      ├────────────┐
      │            │
      ▼            ▼
   Alias        Keyword
      │
      ▼
    Image
```

---

# 3. Core Entities

The following objects represent the core database model.

---

## User

Represents an administrator.

Responsibilities

- Authentication
- Authorization
- Administration

---

## Novel

Represents a single story.

Responsibilities

- Defines recognition boundaries.
- Groups entities.

---

## Entity

Represents every searchable object.

Responsibilities

- Main Name
- Entity Type
- Image
- Searchable information

---

## Alias

Alternative searchable names.

One Entity may own multiple Aliases.

---

## Keyword

Additional searchable words.

Invisible to users.

Used only during recognition.

---

## Description

Localized textual information.

One Entity may have multiple descriptions.

Each description belongs to one language.

---

## Image

Optimized image metadata.

Stores information about generated image files.

---

# 4. Relationship Overview

User

↓

creates

↓

Novel

↓

contains

↓

Entity

↓

owns

↓

Alias

↓

Keyword

↓

Description

↓

Image

---

# 5. Cardinality

User

1

↓

N

Novel

---

Novel

1

↓

N

Entity

---

Entity

1

↓

N

Alias

---

Entity

1

↓

N

Keyword

---

Entity

1

↓

N

Description

---

Entity

1

↓

1

Image

---

# 6. Domain Rules

The following rules apply.

Every Entity belongs to exactly one Novel.

Every Alias belongs to exactly one Entity.

Every Keyword belongs to exactly one Entity.

Every Description belongs to exactly one Entity.

Every Image belongs to exactly one Entity.

Descriptions are localized.

Aliases are searchable.

Keywords are invisible.

Main Name remains unique inside the same Novel.

---

# 7. Logical Database Design

The logical database model defines the tables, relationships, and responsibilities of each data object.

The design is independent of any specific database engine.

---

# 8. Entity Relationship Diagram (Logical)

```text
+------------------+
| users            |
+------------------+
| uuid (PK)        |
| role_id (FK)     |
| name             |
| email            |
| password         |
| ...              |
+------------------+

          │

          ▼

+------------------+
| novels           |
+------------------+
| uuid (PK)        |
| type             |
| slug             |
| title            |
| cover_image      |
| status           |
| created_at       |
| updated_at       |
| deleted_at       |
+------------------+

          │ 1

          │

          ▼ N

+------------------+
| entities         |
+------------------+
| uuid (PK)        |
| novel_id (FK)    |
| type             |
| subtype          |
| slug             |
| main_name        |
| image_id (FK)    |
| hash             |
| status           |
| created_at       |
| updated_at       |
| deleted_at       |
+------------------+

      │
      ├──────────────┐
      │              │
      ▼              ▼

+----------------+   +----------------+
| aliases        |   | keywords       |
+----------------+   +----------------+
| uuid (PK)      |   | uuid (PK)      |
| entity_id (FK) |   | entity_id (FK) |
| alias          |   | keyword        |
| normalized     |   | normalized     |
+----------------+   +----------------+

      │
      ├──────────────┐
      │              │
      ▼              ▼

+----------------------+   +----------------------+
| entity_descriptions  |   | entity_images        |
+----------------------+   +----------------------+
| uuid (PK)            |   | uuid (PK)            |
| entity_id (FK)       |   | entity_id (FK)       |
| language             |   | storage_path         |
| description          |   | thumbnail_path       |
| updated_at           |   | hash                 |
+----------------------+   | width                |
                           | height               |
                           | updated_at           |
                           +----------------------+
```

---

# 9. Table Responsibilities

## users

Stores administrator accounts.

Authentication is performed through JWT.

---

## novels

Stores all supported novels.

A novel defines the search boundary.

---

## entities

Stores every searchable object.

This is the central table of the application.

---

## aliases

Stores alternative names used for recognition.

Visible to administrators.

Invisible to readers.

---

## keywords

Stores additional searchable words.

Invisible to readers.

Used only by the Recognition Engine.

---

## entity_descriptions

Stores localized descriptions.

One entity may have multiple languages.

---

## entity_images

Stores optimized image metadata.

Each image belongs to exactly one entity.

Only one image may be active for Version 1.

---

# 10. Naming Rules

Table names

- plural
- snake_case

Column names

- snake_case

Primary Keys

- uuid

Foreign Keys

- xxx_id

Boolean fields

- is_xxx

Timestamp fields

- created_at
- updated_at
- deleted_at

---

# 11. Data Integrity Rules

Every foreign key shall be enforced.

Every entity must belong to one novel.

Alias values may repeat across different novels.

Main Name must be unique within the same novel.

One entity may have multiple descriptions.

Each language may exist only once per entity.

Only one active image is allowed per entity.

---

# 12. Soft Delete Strategy

The following tables use soft delete.

- users
- novels
- entities
- aliases
- keywords
- entity_descriptions
- entity_images

Deleted data remains recoverable until permanently removed.

---

# 13. Physical Database Design

This section defines the physical structure of the database.

The design remains compatible with PostgreSQL, MySQL, and MariaDB.

---

# 14. Standard Columns

Every primary table follows the same structure.

Example

uuid

created_at

updated_at

deleted_at

Status fields are added only when required.

---

# 15. Table Specifications

## roles

Purpose

Stores administrator roles.

Columns

| Column | Type | Nullable | Notes |
|---------|------|----------|------|
| uuid | UUID | No | Primary Key |
| slug | VARCHAR(50) | No | Unique |
| name | VARCHAR(100) | No | Display Name |
| description | TEXT | Yes | Optional |
| created_at | TIMESTAMP | No | |
| updated_at | TIMESTAMP | No | |
| deleted_at | TIMESTAMP | Yes | Soft Delete |

---

## users

| Column | Type |
|---------|------|
| uuid | UUID |
| role_id | UUID |
| name | VARCHAR(150) |
| email | VARCHAR(255) |
| password | VARCHAR(255) |
| created_at | TIMESTAMP |
| updated_at | TIMESTAMP |
| deleted_at | TIMESTAMP |

Constraints

- email UNIQUE

---

## novels

| Column | Type |
|---------|------|
| uuid | UUID |
| type | VARCHAR(20) |
| slug | VARCHAR(200) |
| title | VARCHAR(255) |
| cover_image | VARCHAR(255) |
| status | VARCHAR(30) |
| created_at | TIMESTAMP |
| updated_at | TIMESTAMP |
| deleted_at | TIMESTAMP |

Constraints

- slug UNIQUE

---

## entities

| Column | Type |
|---------|------|
| uuid | UUID |
| novel_id | UUID |
| type | VARCHAR(30) |
| subtype | VARCHAR(50) |
| slug | VARCHAR(255) |
| main_name | VARCHAR(255) |
| image_id | UUID |
| hash | CHAR(64) |
| status | VARCHAR(30) |
| created_at | TIMESTAMP |
| updated_at | TIMESTAMP |
| deleted_at | TIMESTAMP |

Constraints

UNIQUE

(novel_id, slug)

---

## aliases

| Column | Type |
|---------|------|
| uuid | UUID |
| entity_id | UUID |
| alias | VARCHAR(255) |
| normalized | VARCHAR(255) |
| created_at | TIMESTAMP |
| updated_at | TIMESTAMP |
| deleted_at | TIMESTAMP |

---

## keywords

| Column | Type |
|---------|------|
| uuid | UUID |
| entity_id | UUID |
| keyword | VARCHAR(255) |
| normalized | VARCHAR(255) |
| created_at | TIMESTAMP |
| updated_at | TIMESTAMP |
| deleted_at | TIMESTAMP |

---

## entity_descriptions

| Column | Type |
|---------|------|
| uuid | UUID |
| entity_id | UUID |
| language | CHAR(2) |
| description | TEXT |
| created_at | TIMESTAMP |
| updated_at | TIMESTAMP |
| deleted_at | TIMESTAMP |

Constraints

UNIQUE

(entity_id, language)

---

## entity_images

| Column | Type |
|---------|------|
| uuid | UUID |
| entity_id | UUID |
| storage_path | VARCHAR(500) |
| thumbnail_path | VARCHAR(500) |
| width | INTEGER |
| height | INTEGER |
| hash | CHAR(64) |
| created_at | TIMESTAMP |
| updated_at | TIMESTAMP |
| deleted_at | TIMESTAMP |

---

# 16. Foreign Key Rules

roles

↓

users

users.role_id

↓

roles.uuid

---

novels

↓

entities

entities.novel_id

↓

novels.uuid

---

entities

↓

aliases

aliases.entity_id

↓

entities.uuid

---

entities

↓

keywords

keywords.entity_id

↓

entities.uuid

---

entities

↓

entity_descriptions

entity_descriptions.entity_id

↓

entities.uuid

---

entities

↓

entity_images

entity_images.entity_id

↓

entities.uuid

---

# 17. UUID Strategy

All primary keys use UUID Version 7.

Benefits

- Globally unique
- Ordered by creation time
- Better index locality than UUID v4
- Safe for distributed systems

---

# 18. Hash Strategy

Every Entity has a SHA-256 hash.

Every Image has a SHA-256 hash.

Hash changes whenever synchronized content changes.

Hash is never manually edited.

---

# 19. Database Constraints

The database shall enforce data integrity through primary keys, foreign keys, unique constraints, and check constraints.

Application logic shall never replace database integrity rules.

---

# 20. Primary Keys

All primary tables use:

- UUID Version 7
- Primary Key
- Not Null

Every child table references the parent UUID through a foreign key.

---

# 21. Foreign Key Constraints

The following relationships are enforced.

| Parent | Child |
|----------|-------|
| roles | users |
| novels | entities |
| entities | aliases |
| entities | keywords |
| entities | entity_descriptions |
| entities | entity_images |

Foreign keys shall always maintain referential integrity.

---

## Delete Strategy

Parent records shall not be physically deleted while child records still exist.

Soft Delete is preferred.

Hard Delete is reserved for maintenance operations.

---

# 22. Unique Constraints

The following unique constraints are required.

roles

- slug

users

- email

novels

- slug

entities

- (novel_id, slug)

aliases

- (entity_id, normalized)

keywords

- (entity_id, normalized)

entity_descriptions

- (entity_id, language)

---

# 23. Check Constraints

Where supported by the database engine, the following validations should be enforced.

language

Allowed values

- en
- id

---

content type

Allowed values

- manga
- manhwa
- manhua
- other

---

entity type

Allowed values

- character
- place
- item

---

Image dimensions

Width > 0

Height > 0

---

# 24. Indexing Strategy

Indexes shall be created based on search frequency rather than table size.

---

## novels

Indexes

- slug
- type

---

## entities

Indexes

- novel_id
- slug
- main_name
- hash
- type
- subtype

Composite Index

(novel_id, main_name)

---

## aliases

Indexes

- entity_id
- normalized

Composite Index

(entity_id, normalized)

---

## keywords

Indexes

- entity_id
- normalized

Composite Index

(entity_id, normalized)

---

## entity_descriptions

Indexes

- entity_id
- language

---

## entity_images

Indexes

- entity_id
- hash

---

users

Indexes

- email
- role_id

---

# 25. Recognition Optimization

Recognition queries are expected to be the most frequently executed operations.

Search order

1. Main Name
2. Alias
3. Keyword

The database should optimize this access pattern.

Normalization shall occur before querying.

---

# 26. Synchronization Optimization

Synchronization requests use hash comparison.

Recommended query order

Entity UUID

↓

Hash

↓

Compare

↓

Download only if changed

Indexes on UUID and Hash shall be maintained.

---

# 27. Image Optimization

Only optimized images are stored for client distribution.

Original uploaded images are retained only if required by administration.

Android devices receive:

- Thumbnail
- Optimized image

Original files are never distributed.

---

# 28. Scalability Considerations

The schema shall support future growth.

Expected growth includes

- More novels
- More entities
- More aliases
- More languages
- Additional entity types

Schema redesign should not be required.

---

# 29. Migration Strategy

Database migrations shall follow a deterministic order.

The following sequence shall always be respected.

Step 1

roles

↓

users

---

Step 2

content_types

↓

novels

---

Step 3

entity_types

↓

entity_subtypes

↓

entities

---

Step 4

aliases

↓

keywords

↓

entity_descriptions

↓

entity_images

This order guarantees that all foreign key dependencies exist before child tables are created.

---

# 30. Seeder Strategy

The application includes mandatory seed data.

These records shall be inserted during initial installation.

---

## Roles

Required

- Super Admin
- Admin
- Editor

---

## Content Types

Required

- Manga
- Manhwa
- Manhua
- Other

---

## Entity Types

Required

- Character
- Place
- Item

---

## Languages

Required

- English (en)
- Indonesian (id)

---

## Entity Subtypes

Initially empty.

Subtypes are managed by administrators according to project needs.

---

# 31. Database Standards

The following standards apply to every table.

---

## Primary Key

UUID Version 7

---

## Naming Convention

Tables

snake_case

Plural form

Columns

snake_case

Foreign Keys

xxx_id

---

## Timestamps

Every primary table shall contain

created_at

updated_at

deleted_at

---

## Soft Delete

Enabled for every business table.

Physical deletion is reserved for maintenance tasks.

---

## Character Encoding

UTF-8

The database must support multilingual content.

---

## Collation

Choose a Unicode-compatible collation that provides consistent case-insensitive comparisons where appropriate.

The exact collation should be selected according to the target database engine (PostgreSQL, MySQL, or MariaDB).

---

# 32. Constraint Naming Convention

Primary Keys

pk_<table>

Example

pk_entities

---

Foreign Keys

fk_<table>_<column>

Example

fk_entities_novel_id

---

Unique Constraints

uq_<table>_<column>

Example

uq_users_email

---

Indexes

idx_<table>_<column>

Composite indexes

idx_entities_novel_main_name

---

# 33. Backup and Recovery Guidelines

The production database shall follow the organization's backup policy.

General principles include:

- Regular automated backups.
- Periodic restore verification.
- Secure backup storage.
- Version compatibility checks before restoration.

The implementation schedule and retention policy are operational concerns and are outside the scope of this document.

---

# 34. Future Compatibility

The database is designed to support future enhancements without structural redesign.

Possible future additions include:

- Additional entity types
- Additional languages
- Multiple images per entity
- Reader accounts
- Favorite entities
- Reading history
- Browser extension support

The current schema should remain compatible with these extensions.

---

# 35. Database Compliance Checklist

Every implementation shall satisfy the following requirements.

Schema

- [ ] UUID Version 7 used consistently.
- [ ] Foreign keys implemented.
- [ ] Soft Delete enabled.
- [ ] Naming conventions followed.

---

Integrity

- [ ] Unique constraints enforced.
- [ ] Referential integrity enforced.
- [ ] Main Name unique within each Novel.

---

Performance

- [ ] Required indexes created.
- [ ] Recognition queries optimized.
- [ ] Hash synchronization supported.

---

Portability

- [ ] Database agnostic design maintained.
- [ ] No engine-specific features required.

---

Maintainability

- [ ] Schema normalized.
- [ ] Lookup tables used where applicable.
- [ ] Future expansion supported.

---

# 36. Conclusion

The database of Aethel Read is designed around a single principle:

> Store data once, keep it consistent, and make it efficiently searchable.

The schema prioritizes:

- Data integrity
- Offline synchronization
- Extensibility
- Performance
- Portability

Future development shall preserve these principles.

---

# Revision History

| Version | Date | Description |
|----------|------------|----------------------------------------------|
| 1.4.0 | 2026-06-26 | Finalized Database Design document |