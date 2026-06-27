# Architecture

## Purpose

This document defines the overall software architecture of Aethel Read.

It describes how the application's components interact, the architectural principles that govern implementation, and the responsibilities of each major subsystem.

This document is considered a **Normative Document**.

---

# 1. Architecture Goals

The architecture of Aethel Read is designed to achieve the following goals:

* High maintainability
* Offline-first experience
* High performance
* Low network usage
* Minimal storage consumption
* Easy extensibility
* Platform independence
* Clear separation of responsibilities

---

# 2. System Overview

Aethel Read consists of three primary systems.

```
                    +----------------------+
                    |     Admin Panel      |
                    +----------+-----------+
                               |
                               |
                         REST API (JWT)
                               |
                               |
+------------------------------+------------------------------+
|                        Backend Service                      |
|-------------------------------------------------------------|
| Authentication | Novel | Entity | Image | Sync | Search     |
+------------------------------+------------------------------+
                               |
                               |
                         HTTPS / JSON
                               |
                               |
+-------------------------------------------------------------+
|                     Android Application                     |
|-------------------------------------------------------------|
| Floating Bubble | OCR | Recognition | Cache | Detail View   |
+-------------------------------------------------------------+
```

The backend serves as the single source of truth.

The Android application acts as an intelligent client that prioritizes local resources before requesting remote data.

The Admin Panel manages all application content.

---

# 3. High-Level Components

## Android Client

Responsibilities

* User interaction
* OCR recognition
* Local search
* Cache management
* Entity presentation
* Background synchronization

The Android client never manages business content directly.

---

## Backend Service

Responsibilities

* Authentication
* Data management
* Synchronization
* Search data delivery
* Image delivery
* Version consistency

The backend provides data but never performs OCR.

---

## Admin Panel

Responsibilities

* Novel management
* Entity management
* Alias management
* Description management
* Image management
* User management

The Admin Panel is the only interface allowed to modify application data.

---

# 4. Communication Principles

Communication follows these rules.

Rule 1

Android communicates only with the Backend API.

---

Rule 2

The Admin Panel communicates only with the Backend.

---

Rule 3

Android never communicates directly with the Admin Panel.

---

Rule 4

The Backend is the only component allowed to access the database.

---

Rule 5

The database is never exposed to clients.

---

# 5. Architectural Principles

The architecture follows the following principles.

## Separation of Concerns

Each component has one primary responsibility.

---

## Single Source of Truth

All official data originates from the Backend.

---

## Offline First

Local data is preferred whenever available.

---

## Cache Before Network

Network requests occur only when required.

---

## Stateless Communication

Every API request contains all required information.

---

## Platform Independence

Business rules remain independent from implementation technology.

---

## Extensibility

New entity types, languages, and novels can be added without redesigning the architecture.

---

# 6. System Boundaries

Android Application

Responsible for:

* OCR
* Recognition
* Presentation
* Local cache

Not responsible for:

* Data management
* Authentication administration
* Content editing

---

Backend

Responsible for:

* Business data
* Authentication
* Synchronization
* Validation

Not responsible for:

* OCR
* User interface

---

Admin Panel

Responsible for:

* Data maintenance
* Administration
* Content management

Not responsible for:

* Reading assistance
* OCR
* Offline cache

---

# 7. Domain Model

## Domain Principle

Everything searchable inside Aethel Read is an **Entity**.

Characters, Places, and Items are not independent models. They are specialized types of the same domain object.

This approach keeps the architecture simple, scalable, and extensible.

Future entity types can be introduced without changing the core architecture.

---

## Core Domain Model

```text
Novel
│
├── Entity
│      │
│      ├── Character
│      ├── Place
│      └── Item
│
├── Description
│
├── Alias
│
├── Keyword
│
└── Image
```

Relationships

- One Novel contains many Entities.
- One Entity belongs to exactly one Novel.
- One Entity owns one Main Name.
- One Entity may own multiple Aliases.
- One Entity may own multiple Keywords.
- One Entity may contain multiple localized Descriptions.
- One Entity may have one primary Image.

---

## Domain Responsibilities

### Novel

Represents a story.

Responsibilities

- Organizes entities.
- Defines search boundaries.
- Provides context for recognition.

---

### Entity

Represents every searchable object.

Responsibilities

- Stores the Main Name.
- Owns aliases.
- Owns descriptions.
- Owns image.
- Owns keywords.
- Defines entity type.

---

### Alias

Alternative names used exclusively for searching.

Aliases never replace the Main Name.

---

### Keyword

Additional searchable words that improve recognition accuracy.

Keywords are invisible to users.

---

### Description

Localized textual information describing an Entity.

Descriptions are displayed according to the selected application language.

---

### Image

Visual representation of an Entity.

Images are optimized before distribution.

---

# 8. Layer Architecture

The application follows a layered architecture.

```text
Presentation Layer
        │
        ▼
Application Layer
        │
        ▼
Domain Layer
        │
        ▼
Infrastructure Layer
```

Each layer has a single responsibility.

Dependencies always point downward.

Lower layers never depend on upper layers.

---

## Presentation Layer

Responsibilities

- User Interface
- Floating Bubble
- Entity List
- Entity Detail
- User Interaction

This layer contains no business rules.

---

## Application Layer

Responsibilities

- Use Cases
- Application Services
- Workflow Coordination
- Request Handling

This layer coordinates domain objects.

It does not contain persistence logic.

---

## Domain Layer

Responsibilities

- Business Rules
- Domain Models
- Validation Rules
- Recognition Rules

This layer represents the heart of the application.

It is independent from frameworks and databases.

---

## Infrastructure Layer

Responsibilities

- REST API
- Local Storage
- Image Storage
- Network Communication
- OCR Integration
- Cache Implementation

Infrastructure supports the Domain Layer but never defines business rules.

---

# 9. Dependency Rules

The following dependency rules apply throughout the project.

Rule 1

Presentation depends on Application.

---

Rule 2

Application depends on Domain.

---

Rule 3

Infrastructure depends on Domain.

---

Rule 4

Domain depends on nothing.

---

Rule 5

Business rules shall never depend on frameworks.

---

Rule 6

Frameworks may change without affecting the Domain Layer.

---

# 10. Domain Events

The architecture recognizes the following logical events.

Entity Recognized

Occurs after successful recognition.

---

Entity Selected

Occurs when the user opens entity details.

---

Cache Hit

Occurs when requested data exists locally.

---

Cache Miss

Occurs when requested data must be synchronized.

---

Entity Updated

Occurs when newer entity information replaces cached data.

---

Image Updated

Occurs when a newer optimized image replaces the local version.

---

Novel Changed

Occurs when the user selects another active novel.

---

# 11. Recognition Pipeline

Recognition is performed through a multi-stage pipeline.

Each stage has a single responsibility.

```text
User
 │
 ▼
Floating Bubble
 │
 ▼
OCR Engine
 │
 ▼
Text Normalizer
 │
 ▼
Recognition Engine
 │
 ▼
Local Cache
 │
 ▼
Backend Synchronization (if needed)
 │
 ▼
Recognition Result
 │
 ▼
Entity Detail
```

Each stage operates independently.

Failure in one stage shall not affect the others.

---

# 12. OCR Flow

The OCR Flow begins after the user manually initiates a scan.

```text
User

↓

Tap Floating Bubble

↓

Tap Scan

↓

Capture Current Screen

↓

OCR Engine

↓

Extract Visible Text

↓

Send Text to Recognition Engine
```

The OCR Engine performs text extraction only.

It has no knowledge of novels, entities, aliases, or application data.

---

# 13. Text Normalization

Before recognition begins, OCR results are normalized.

Normalization improves matching accuracy.

Typical normalization includes:

- Convert to lowercase
- Trim whitespace
- Remove duplicated spaces
- Normalize punctuation
- Normalize quotation marks
- Normalize OCR-specific character mistakes

Example

```text
"Heavenly   Demon"

↓

"heavenly demon"
```

Normalized text is used only for searching.

Original OCR text remains unchanged.

---

# 14. Recognition Flow

The Recognition Engine compares normalized text against searchable data.

Search order

```text
Main Name

↓

Alias

↓

Keyword
```

Only entities belonging to the selected Novel participate in recognition.

The engine may recognize multiple entities during a single scan.

Recognition Result

```text
OCR

↓

Normalize

↓

Search

↓

Matched Entities

↓

Display List
```

---

# 15. Smart Cache Flow

The application follows a cache-first strategy.

```text
Recognition Request

↓

Memory Cache

↓

Found ?

↓

YES → Return Result

↓

NO

↓

Local Database

↓

Found ?

↓

YES

↓

Return Result

↓

Check Update

↓

If Updated

↓

Download New Data

↓

Refresh Cache

↓

NO

↓

Backend API

↓

Download Entity

↓

Save Cache

↓

Return Result
```

The user always receives the fastest available result.

Synchronization occurs only when necessary.

---

# 16. Cache Layers

The application uses three cache layers.

Layer 1

Memory Cache

Purpose

Fastest access.

Temporary during application lifetime.

---

Layer 2

Local Database

Purpose

Persistent offline storage.

Available without internet connection.

---

Layer 3

Backend API

Purpose

Official source of truth.

Provides the latest entity information.

---

# 17. Synchronization Strategy

Synchronization follows an on-demand model.

Rules

- Missing entities are downloaded only when requested.
- Existing entities are reused whenever possible.
- Updated entities replace outdated local copies.
- Images follow the same synchronization process.
- Synchronization never blocks reading.

---

# 18. Image Flow

Entity images follow a dedicated pipeline.

```text
Admin Upload

↓

Image Processor

↓

Crop

↓

Resize

↓

Convert WEBP

↓

Generate Thumbnail

↓

Store

↓

Android Download

↓

Local Cache

↓

Display
```

Only optimized images are distributed to clients.

Original uploaded files are never sent to Android devices.

---

# 19. Error Handling Principles

Recognition failures shall not interrupt the reading experience.

Possible outcomes

OCR Failure

↓

Retry available

---

Cache Failure

↓

Fallback to Backend

---

Network Failure

↓

Use Cached Data

---

Image Failure

↓

Display Placeholder

---

Unknown Entity

↓

No Result

---

# 20. Synchronization Architecture

Aethel Read follows an **On-Demand Synchronization** model.

The Android application downloads only the data required to satisfy the current recognition request.

Entire novels are never synchronized automatically.

---

## Synchronization Principles

The synchronization process follows these principles.

- Synchronize only when necessary.
- Download only requested entities.
- Minimize network traffic.
- Preserve offline usability.
- Avoid duplicate downloads.
- Keep synchronization transparent to users.

---

## Entity Synchronization Flow

```text
Recognition Request
        │
        ▼
Check Local Cache
        │
        ├─────────────── Found ───────────────┐
        │                                     │
        ▼                                     ▼
Check Entity Hash                      Return Local Data
        │
        ▼
Hash Changed?
        │
   ┌────┴────┐
   │         │
  Yes       No
   │         │
   ▼         ▼
Download     Return Local Data
Latest Data
   │
   ▼
Replace Local Cache
   │
   ▼
Return Updated Entity
```

Only entities whose hash has changed are downloaded again.

---

# 21. Image Synchronization

Images follow the same synchronization strategy as entity data.

Each image has its own hash value.

Image updates occur independently from entity updates.

This allows image improvements without modifying entity information.

---

## Image Synchronization Flow

```text
Entity Requested
        │
        ▼
Image Exists?
        │
   ┌────┴────┐
   │         │
  Yes       No
   │         │
   ▼         ▼
Check Hash  Download Image
   │
   ▼
Hash Changed?
   │
 ┌─┴──┐
 │    │
Yes   No
 │    │
 ▼    ▼
Download
New Image
 │
 ▼
Replace Cache
```

Only optimized images are distributed to Android clients.

---

# 22. Cache Consistency

The application maintains cache consistency through hash validation.

Rules

- Every entity has a hash.
- Every image has a hash.
- Hash comparison determines update requirements.
- Cached data remains valid until a newer hash is available.

This strategy avoids unnecessary downloads while ensuring users receive updated information.

---

# 23. Security Boundaries

The architecture enforces clear responsibility boundaries.

---

## Android Application

Allowed

- Perform OCR.
- Search local cache.
- Request entity data.
- Display information.

Not Allowed

- Modify entities.
- Delete entities.
- Upload images.
- Access database directly.

---

## Backend Service

Allowed

- Authenticate requests.
- Validate data.
- Synchronize entities.
- Manage images.
- Serve API responses.

Not Allowed

- Perform OCR.
- Modify client cache directly.
- Access Android local storage.

---

## Admin Panel

Allowed

- Manage novels.
- Manage entities.
- Manage aliases.
- Upload images.
- Edit descriptions.
- Manage users.

Not Allowed

- Access Android cache.
- Communicate directly with Android devices.

---

# 24. Scalability Principles

The architecture is designed for long-term growth.

The following extensions should be possible without major redesign.

Supported future expansion

- Additional entity types
- Additional languages
- More novels
- More images
- Larger datasets
- Additional client platforms

Core architecture shall remain unchanged.

---

# 25. Failure Recovery

The system shall recover gracefully whenever possible.

---

## OCR Failure

Recognition process stops.

User may retry.

---

## Network Failure

Cached data remains available.

Synchronization resumes later.

---

## Synchronization Failure

Previously cached data remains valid.

Retry occurs on the next request.

---

## Image Failure

Placeholder image is displayed.

Image download may be retried later.

---

## Unknown Entity

No result is displayed.

Application continues operating normally.

---

# 26. Architectural Constraints

The following constraints apply to every implementation.

- Business rules belong to the Domain Layer.
- Clients never access the database directly.
- All content modifications occur through the Backend.
- Android remains functional with limited or no connectivity.
- Server remains the single source of truth.
- Entity identity never changes after creation.
- Main Name remains stable throughout the entity lifecycle.

---

# 27. Component Relationships

The following diagram illustrates the relationship between the major components of Aethel Read.

```text
                     +----------------------+
                     |     Admin Panel      |
                     +----------+-----------+
                                |
                                | REST API (JWT)
                                ▼
+--------------------------------------------------------------+
|                        Backend Service                        |
|--------------------------------------------------------------|
| Authentication | Novel | Entity | Image | Sync | Search API  |
+--------------------------------------------------------------+
                                ▲
                                |
                         HTTPS / JSON
                                |
+--------------------------------------------------------------+
|                     Android Application                      |
|--------------------------------------------------------------|
| Floating | OCR | Recognition | Cache | Entity Detail | UI    |
+--------------------------------------------------------------+
```

Each component has clearly separated responsibilities.

No component may bypass another component's responsibility.

---

# 28. Architectural Decision Records (ADR)

The following architectural decisions are considered permanent for Version 1.

---

## ADR-001

### Entity-Centric Domain

Decision

Everything searchable in Aethel Read is represented as an Entity.

Reason

Provides a scalable and extensible domain model.

Impact

New entity types can be introduced without redesigning the architecture.

---

## ADR-002

### Offline First

Decision

The Android application prioritizes local resources before requesting remote data.

Reason

Improves reading experience and minimizes network dependency.

Impact

Users can continue reading without internet access if data has already been synchronized.

---

## ADR-003

### Cache Before Network

Decision

Recognition always checks local cache before contacting the backend.

Reason

Reduces latency and server load.

Impact

Lower bandwidth usage and faster response time.

---

## ADR-004

### Hash-Based Synchronization

Decision

Entity and image updates are determined using hash comparison.

Reason

Avoids unnecessary downloads.

Impact

Efficient synchronization with minimal network traffic.

---

## ADR-005

### Backend as Single Source of Truth

Decision

Only the backend owns official application data.

Reason

Ensures consistency across all clients.

Impact

Clients never modify business data directly.

---

## ADR-006

### Local OCR

Decision

OCR processing is performed entirely on the Android device.

Reason

Protects user privacy and reduces server dependency.

Impact

Reading content never leaves the user's device.

---

# 29. Design Principles

All future development shall follow these principles.

---

## Principle 1

Business rules belong to the Domain Layer.

---

## Principle 2

Frameworks must not influence business rules.

---

## Principle 3

Every component has one primary responsibility.

---

## Principle 4

Communication occurs only through defined interfaces.

---

## Principle 5

Client applications remain lightweight.

---

## Principle 6

The backend owns all business data.

---

## Principle 7

Synchronization shall be transparent to users.

---

## Principle 8

Performance takes priority over unnecessary visual complexity.

---

## Principle 9

Data integrity is preferred over duplicated information.

---

## Principle 10

Extensibility is preferred over premature optimization.

---

# 30. Architecture Compliance Checklist

Every implementation shall satisfy the following checklist.

System

- [ ] Backend remains the single source of truth.
- [ ] Android never accesses the database directly.
- [ ] OCR runs locally.
- [ ] Recognition follows the defined pipeline.

---

Data

- [ ] Entity identity remains stable.
- [ ] Main Name is immutable.
- [ ] Aliases are searchable.
- [ ] Keywords remain invisible to users.
- [ ] Local cache remains synchronized.

---

Security

- [ ] Authentication required for administration.
- [ ] Android never modifies business data.
- [ ] Database is never exposed publicly.

---

Performance

- [ ] Cache-first strategy implemented.
- [ ] Background synchronization does not interrupt reading.
- [ ] Optimized images are distributed.

---

Maintainability

- [ ] Domain Layer remains framework-independent.
- [ ] Dependencies follow defined architecture.
- [ ] Future entity types require no architectural redesign.

---

# 31. Conclusion

The architecture of Aethel Read is designed around a simple principle:

> Help readers understand stories without interrupting their reading experience.

Every architectural decision—from Entity-Centric design, Offline First strategy, Smart Cache, and Hash-Based Synchronization to Local OCR—supports this principle.

Future development shall preserve these architectural foundations.

---

# Revision History

| Version | Date | Description |
|----------|------------|---------------------------------------------|
| 1.3.0 | 2026-06-26 | Finalized Architecture document |