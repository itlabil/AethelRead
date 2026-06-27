---
project: Aethel Read
tagline: Your Reading Companion
document: Glossary
document_id: D-004
version: 1.0.0
status: Draft
language: English
last_updated: 2026-06-26
author: Erwin Dianto
reviewer: ChatGPT (Technical Lead)
---

# Glossary

## Purpose

This document defines the official terminology used throughout the Aethel Read project.

Every documentation file, source code, API response, database schema, and AI-generated implementation should follow these definitions to maintain consistency.

This document is considered a **Normative Document**.

---

# A

## Alias

An alternative name of an Entity used during searching.

Aliases exist only to improve recognition and search accuracy.

Example

Main Name

```
Mok Gyeongwoon
```

Aliases

```
Cheon Ma
Heavenly Demon
Young Master Mok
Lord of Demons
```

---

# C

## Cache

Locally stored data used to reduce network requests and improve recognition speed.

---

## Cache Hit

A successful lookup where requested data already exists in the local cache.

---

## Cache Miss

A lookup where requested data does not exist locally and must be downloaded from the backend.

---

## Canonical Name

The official display name of an Entity.

This value is shown to users.

Canonical Name is also called **Main Name**.

---

# D

## Description

Localized information explaining an Entity.

Descriptions support multiple languages.

Entity names themselves remain unchanged.

---

# E

## Entity

A searchable object recognized by Aethel Read.

Every Entity belongs to exactly one Novel.

Supported Entity Types

- Character
- Item
- Place

Future versions may introduce additional types without affecting existing architecture.

---

## Entity Type

The primary category assigned to an Entity.

Current values

- Character
- Item
- Place

---

# F

## Floating Bubble

A movable overlay displayed above other applications.

It provides quick access to OCR recognition results.

---

# H

## Hash

A SHA-256 checksum representing the latest version of an Entity or image.

Used to determine whether local cached data requires updating.

---

# M

## Main Name

The primary name of an Entity.

Displayed throughout the application.

Every Alias points to exactly one Main Name.

---

# N

## Normalizer

A text processing component that converts OCR results into a normalized format before matching.

Examples include:

- lowercase conversion
- whitespace normalization
- punctuation removal
- OCR character correction

---

## Novel

A collection containing all Entities belonging to a single story.

Entities cannot belong to multiple Novels.

---

# O

## OCR

Optical Character Recognition.

Performed locally on the Android device using Google ML Kit.

No screenshot is uploaded to the server.

---

# P

## Place

A location within a Novel.

Examples

- Kingdom
- City
- Dungeon
- Mountain
- Academy

---

# R

## Reading Companion

The official product category of Aethel Read.

Unlike reading applications, Aethel Read enhances existing reading platforms instead of replacing them.

---

# S

## Slug

A human-readable unique identifier used in URLs.

Example

```
nano-machine
```

instead of

```
01984...
```

---

## Smart Cache

The three-layer caching strategy used by Aethel Read.

Priority

Memory Cache

↓

Room Database

↓

REST API

---

# T

## Thumbnail

An optimized WEBP square image generated automatically during upload.

Used to reduce storage usage and improve loading speed.

---

# U

## UUID

Universally Unique Identifier.

Aethel Read uses UUID v7 as the primary key for every database table.

UUIDs are intended for internal use only.

---

# Revision History

| Version | Date | Description |
|----------|------------|----------------------|
| 1.0.0 | 2026-06-26 | Initial glossary |