---
project: Aethel Read
tagline: Your Reading Companion
document: Product Requirements Document
document_id: D-005
version: 1.0.0
status: Draft
language: English
last_updated: 2026-06-26
author: Erwin Dianto
---

# Product Requirements Document (PRD)

## 1. Introduction

### 1.1 Purpose

This document defines the functional and business requirements for Aethel Read.

Its purpose is to ensure that every feature, user interaction, and system behavior is clearly defined before implementation begins.

This document serves as the primary reference for product development throughout Version 1.

---

### 1.2 Product Summary

Aethel Read is an Android application that assists readers while reading Light Novels, Manga, Manhwa, Manhua, and other supported reading materials.

Instead of replacing existing reading applications, Aethel Read works alongside them by recognizing names appearing on the user's screen and providing contextual information through a lightweight floating interface.

The application helps readers quickly remember characters, places, and items without interrupting their reading experience.

---

### 1.3 Target Platforms

Version 1 supports:

* Android Smartphones

Future versions may expand to:

* Desktop
* Browser Extension
* Additional Mobile Platforms

---

## 2. Product Vision

Aethel Read aims to become the most reliable reading companion for fiction readers by providing instant contextual information without disrupting immersion.

The application prioritizes speed, simplicity, offline capability, and minimal user interaction.

Readers should spend less time searching online and more time enjoying the story.

---

## 3. Product Goals

### Goal 1

Reduce reading interruptions caused by searching character information.

---

### Goal 2

Help readers remember important entities throughout long-running stories.

---

### Goal 3

Provide information instantly with minimal interaction.

---

### Goal 4

Minimize internet usage through intelligent caching.

---

### Goal 5

Maintain a lightweight application suitable for long reading sessions.

---

## 4. Target Users

### Primary Users

Readers who frequently consume:

* Light Novels
* Manga
* Manhwa
* Manhua

These users often read stories containing hundreds of chapters and many recurring characters.

---

### Secondary Users

Readers of:

* Fantasy Novels
* Web Novels
* Wuxia
* Xianxia
* Cultivation Stories

---

## 5. User Problems

Readers commonly experience the following problems.

### P1

Forgotten character identities.

---

### P2

Confusion caused by multiple aliases and titles.

---

### P3

Difficulty remembering locations and important items.

---

### P4

Repeatedly opening web browsers to search information.

---

### P5

Breaking reading immersion while searching external sources.

---

## 6. Product Value Proposition

Aethel Read provides contextual information exactly when readers need it.

Instead of replacing existing reading applications, Aethel Read complements them by acting as an intelligent companion.

Its value lies in helping readers understand stories while preserving the natural reading flow.

---

## 7. Success Metrics

Version 1 will be considered successful if it achieves the following outcomes.

### User Experience

* Reading interruptions are significantly reduced.
* Entity information appears quickly after user interaction.
* Users can continue reading without switching applications.

---

### Performance

* Low application startup time.
* Minimal storage consumption.
* Efficient cache utilization.

---

### Product

* Supports multiple novels.
* Supports multilingual descriptions.
* Maintains consistent floating interface behavior.

---

## 8. Version Scope

### Included in Version 1

* Floating Bubble
* Novel Selection
* OCR Recognition
* Entity Recognition
* Character Lookup
* Item Lookup
* Place Lookup
* Detail View
* Offline Cache
* Smart Synchronization
* Image Thumbnail
* English Description
* Indonesian Description
* Admin Management
* REST API

---

### Excluded from Version 1

The following features are intentionally excluded.

* Reading novels inside the application
* User accounts for readers
* Community discussions
* Reviews and ratings
* AI-generated summaries
* AI recommendations
* Spoiler detection
* Automatic chapter recognition
* Browser Extension
* Desktop Application

These items belong to future versions and must not affect Version 1 implementation.

---

## 9. Functional Requirements

---

### FR-001 — Floating Bubble

#### Objective

Provide users with a persistent floating entry point while reading.

#### Description

The application shall display a movable floating bubble above supported reading applications.

The floating bubble acts as the primary interaction point for all recognition features.

#### User Flow

1. User enables Floating Bubble.
2. Bubble appears on screen.
3. User can drag the bubble to any screen position.
4. Bubble remains visible until manually disabled.

#### Acceptance Criteria

* Bubble is movable.
* Bubble remains visible across supported applications.
* Bubble does not interrupt reading.
* Bubble can be hidden by the user.

---

### FR-002 — Novel Selection

#### Objective

Allow users to define which novel is currently being read.

#### Description

Users shall select:

* Content Type
* Novel

The selected novel becomes the active context for all recognition processes.

The application remembers the last selected novel and restores it automatically.

#### User Flow

1. User taps the floating bubble.
2. User selects Content Type.
3. User selects Novel.
4. Current selection becomes active.

#### Acceptance Criteria

* Recent novel is restored automatically.
* User can change the active novel at any time.
* Recognition uses only the selected novel.

---

### FR-003 — OCR Recognition

#### Objective

Capture visible text from the current screen.

#### Description

The application recognizes text currently visible on screen after user interaction.

Only visible text is processed.

Recognition occurs entirely on the user's device.

#### User Flow

1. User taps Scan.
2. OCR starts.
3. Visible text is extracted.
4. OCR result is passed to the recognition engine.

#### Acceptance Criteria

* OCR starts manually.
* OCR completes without leaving the reading application.
* No screenshot is uploaded to the server.

---

### FR-004 — Entity Recognition

#### Objective

Identify supported entities from OCR results.

#### Description

The recognition engine compares OCR results against locally available searchable data.

Only entities belonging to the selected novel are considered.

#### User Flow

1. OCR result received.
2. Normalize text.
3. Search local index.
4. Download missing data if required.
5. Produce recognized entity list.

#### Acceptance Criteria

* Recognition supports aliases.
* Recognition ignores case differences.
* Recognition supports multiple entities simultaneously.

---

### FR-005 — Entity List

#### Objective

Display all recognized entities in a simple list.

#### Description

Recognized entities shall be presented inside the floating panel.

Each row displays:

* Thumbnail
* Main Name

Selecting an entity opens the detail page.

#### Acceptance Criteria

* Multiple entities supported.
* List is scrollable.
* Thumbnail displayed.
* Main Name displayed.

---

### FR-006 — Entity Detail

#### Objective

Display complete information for a selected entity.

#### Description

The detail page presents contextual information for the selected entity.

Displayed information includes:

* Image
* Main Name
* Description
* Alias List

Descriptions follow the currently selected application language.

Entity names remain unchanged.

#### Acceptance Criteria

* Image displayed.
* Description localized.
* Alias visible.
* Main Name always displayed.

---

### FR-007 — Offline Cache

#### Objective

Allow previously downloaded entities to be accessed without internet.

#### Description

Entity information stored locally shall remain available while offline.

Users should not notice whether information originates from cache or server.

#### Acceptance Criteria

* Cached entities available offline.
* Missing internet connection does not affect cached data.

---

### FR-008 — Smart Synchronization

#### Objective

Keep cached information up to date.

#### Description

Whenever an entity is requested, the application verifies whether newer information exists.

Only updated data is downloaded.

#### Acceptance Criteria

* No duplicate downloads.
* Updated information replaces outdated cache.
* Unchanged data is reused.

---

### FR-009 — Multi-language Description

#### Objective

Support multiple description languages.

#### Description

Entity descriptions shall be available in:

* English
* Indonesian

Changing application language updates displayed descriptions.

Entity names remain identical across all languages.

#### Acceptance Criteria

* English description supported.
* Indonesian description supported.
* Main Name unchanged.

---

### FR-010 — Image Optimization

#### Objective

Provide fast image loading with minimal storage usage.

#### Description

Every entity image shall be displayed using optimized thumbnails.

Images maintain a consistent square format throughout the application.

#### Acceptance Criteria

* Images load quickly.
* Square aspect ratio maintained.
* Consistent appearance.

---

## 10. Non-Functional Requirements

### NFR-001 — Performance

The application shall provide a responsive user experience.

Requirements:

* Floating Bubble shall open without noticeable delay.
* Entity lookup shall appear as quickly as possible.
* Scrolling shall remain smooth.
* Background synchronization shall not interrupt reading.

---

### NFR-002 — Reliability

The application shall continue operating even under unstable network conditions.

Requirements:

* Previously cached entities remain accessible.
* Temporary network failures shall not crash the application.
* Synchronization failures shall be retryable.

---

### NFR-003 — Offline Capability

The application shall prioritize local resources before requesting remote data.

Requirements:

* Cached entities are available without internet.
* Users can continue reading normally while offline.

---

### NFR-004 — Usability

The application shall require minimal user interaction.

Requirements:

* Recognition should require as few steps as possible.
* Information must be easy to understand.
* Floating interface shall remain simple and unobtrusive.

---

### NFR-005 — Maintainability

The product shall support future expansion without major redesign.

Requirements:

* New entity types can be introduced.
* New novels can be added without application updates.
* New languages can be introduced in future versions.

---

### NFR-006 — Security

The application shall respect user privacy.

Requirements:

* OCR processing occurs locally on the device.
* Reading content is never uploaded to the server.
* Only required application data is synchronized.

---

## 11. Business Rules

The following rules define the official business behavior of Aethel Read.

---

### BR-001

One Novel contains many Entities.

---

### BR-002

One Entity belongs to exactly one Novel.

---

### BR-003

Every Entity has exactly one Main Name.

---

### BR-004

One Entity may have multiple Aliases.

Aliases exist only for recognition and searching.

---

### BR-005

Entity search shall consider:

* Main Name
* Alias
* Keyword

---

### BR-006

Entity names are never translated.

Only descriptions support multiple languages.

---

### BR-007

Recognition shall only search within the currently selected Novel.

---

### BR-008

Users may change the active Novel at any time.

---

### BR-009

Entity images shall maintain a consistent square format.

---

### BR-010

Entity information shall remain available after successful synchronization.

---

### BR-011

Descriptions may be updated independently without changing entity identity.

---

### BR-012

The application shall never function as a reading platform.

Its purpose is to assist readers while using external reading applications.

---

## 12. Acceptance Criteria Summary

Version 1 shall be considered complete when the following requirements are satisfied.

### Core Features

* Floating Bubble
* Novel Selection
* OCR Recognition
* Entity Recognition
* Entity List
* Entity Detail

---

### Data Management

* Offline Cache
* Smart Synchronization
* Image Optimization
* Multi-language Descriptions

---

### Administration

* Novel Management
* Entity Management
* Alias Management
* Image Upload
* Description Management

---

### Backend

* REST API
* JWT Authentication
* Role-based Administration

---

## 13. Version 1 Release Checklist

Before Version 1 is released, the following conditions shall be met.

### Product

* All Functional Requirements implemented.
* All Non-Functional Requirements satisfied.
* Business Rules verified.

---

### Administration

* Admin Panel operational.
* Entity management completed.
* Image upload functional.
* User authentication functional.

---

### Android

* Floating Bubble operational.
* OCR recognition operational.
* Entity recognition operational.
* Offline cache operational.

---

### Backend

* API documented.
* Authentication implemented.
* Synchronization implemented.

---

### Quality Assurance

* Functional testing completed.
* Integration testing completed.
* User acceptance testing completed.

---

## 14. Out of Scope

The following items are intentionally excluded from Version 1.

* Reading novels inside Aethel Read.
* User accounts for readers.
* Story recommendations.
* Community features.
* AI-generated summaries.
* Automatic spoiler detection.
* Browser extensions.
* Desktop applications.
* iOS support.

These items may be considered in future versions but are outside the scope of Version 1.

---

## 15. Revision History

| Version | Date       | Description                           |
| ------- | ---------- | ------------------------------------- |
| 1.0.0   | 2026-06-26 | Initial Product Requirements Document |
