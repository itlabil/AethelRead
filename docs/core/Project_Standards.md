---
project: Aethel Read
tagline: Your Reading Companion
document: Project Standards
document_id: D-002
version: 1.0.0
status: Draft
language: English
last_updated: 2026-06-26
author: Erwin Dianto
---

# Project Standards

## Purpose

This document defines the official engineering standards, architectural principles, development conventions, and technology decisions for the Aethel Read project.

All contributors, whether human developers or AI assistants, must follow this document throughout the project lifecycle.

This document is considered a **Normative Document**.

---

# Vision

**Aethel Read** is an intelligent reading companion designed to help readers understand and remember characters, places, items, and other important entities while reading novels, manga, manhwa, and manhua without interrupting the reading experience.

Aethel Read is **not** a reading application.

It enhances existing reading applications by providing contextual information through on-device OCR and an intelligent caching system.

---

# Product Identity

| Property | Value |
|----------|-------|
| Product Name | Aethel Read |
| Tagline | Your Reading Companion |
| Category | Reading Companion |
| Platform | Android |
| Backend | Laravel REST API |
| Admin Panel | Laravel Blade |
| Primary Language | English |
| Supported UI Languages | English, Indonesian |

---

# Engineering Principles

The following principles guide every technical decision in this project.

## Documentation First

Every feature must be documented before implementation begins.

---

## Offline First

The application should continue functioning even when internet connectivity is unavailable whenever possible.

---

## Cache Before API

Always attempt to retrieve data from the local cache before requesting data from the server.

---

## Hash Before Download

Downloaded resources must be synchronized using SHA-256 hashes instead of timestamps or revision numbers.

---

## Performance First

Reading experience must never be interrupted by unnecessary processing.

OCR, caching, and synchronization should be lightweight and asynchronous.

---

## Database Agnostic

Application code must not depend on vendor-specific database features.

The backend must remain compatible with:

- PostgreSQL
- MySQL
- MariaDB

without changing business logic.

---

## No Vendor Lock-in

The system must avoid unnecessary dependency on specific vendors or cloud providers.

Storage providers, database engines, authentication providers, and infrastructure should remain replaceable.

---

## Prefer Standards over Cleverness

Choose maintainable and standardized solutions over clever but complex implementations.

---

## Keep It Simple

Complexity should only be introduced when it provides measurable benefits.

---

## SOLID Principles

Backend architecture must follow SOLID principles whenever applicable.

---

# Architecture Principles

The project follows the following architectural concepts.

- Clean Architecture
- Repository Pattern
- Service Layer
- Dependency Injection
- Stateless REST API
- MVVM (Android)
- Offline-first synchronization
- Three-layer smart cache
- UUID v7 identifiers
- Slug-based public resources

---

# Technology Stack

## Backend

- PHP 8.4
- Laravel 12
- PostgreSQL (Development)
- MySQL / MariaDB (Production Supported)
- JWT Authentication
- RESTful API
- Repository Pattern
- Service Layer
- API Resources
- Form Request Validation

---

## Android

- Kotlin
- Material Design 3
- MVVM
- Hilt
- Retrofit
- Room
- Coroutines
- Flow
- Google ML Kit OCR

---

## Admin Panel

- Laravel Blade
- Tailwind CSS 4
- Alpine.js
- Lucide Icons

---

## Image Processing

- Intervention Image v3
- Automatic Crop
- Automatic Resize
- Square Format
- WEBP Conversion
- Thumbnail Generation
- SHA-256 Hash

---

# Development Environment

## Operating System

Ubuntu 22.04 LTS

---

## Development Database

PostgreSQL

---

## Production Database

- PostgreSQL
- MySQL
- MariaDB

---

## Java

JDK 21

---

## Android IDE

Android Studio (Latest Stable)

---

## PHP

PHP 8.4

---

# Repository Standards

The project uses a Monorepo structure.

Main directories:

- backend/
- android/
- docs/
- ai/
- docker/
- scripts/
- resources/

---

# Branch Strategy

Main branches:

- main
- develop

Feature branches:

```
feature/<feature-name>
```

Bug fixes:

```
fix/<bug-name>
```

Hotfixes:

```
hotfix/<issue-name>
```

---

# Commit Convention

The project follows Conventional Commits.

Examples:

```
feat(api): implement JWT authentication

fix(android): improve OCR recognition

docs(database): update entity relationship

refactor(cache): simplify synchronization flow
```

---

# Database Standards

- UUID v7 as primary key
- Slug for public URLs
- Soft Delete where applicable
- Foreign Keys must use UUID
- Eloquent ORM preferred
- Raw SQL only when justified
- Avoid vendor-specific SQL features

---

# API Standards

Base URL

```
/api/v1/
```

Authentication

```
JWT Bearer Token
```

Response format

```
application/json
```

REST principles must be followed.

---

# Android Standards

Architecture

MVVM

UI

Material Design 3

Local Storage

Room Database

OCR

Google ML Kit

Networking

Retrofit

Concurrency

Kotlin Coroutines + Flow

---

# UI/UX Standards

Design Philosophy

- Clean
- Minimalist
- Fast
- Non-intrusive

Primary Color

Purple

Theme

Light & Dark

Floating Bubble

Always lightweight and draggable.

---

# AI Collaboration Standards

AI assistants are considered development assistants.

Generated code must:

- Follow project standards
- Follow coding guidelines
- Never bypass architecture
- Never introduce vendor lock-in
- Remain maintainable

---

# Frozen Decisions

The following decisions are considered frozen unless strong technical justification exists.

- Product Name
- Product Tagline
- Monorepo Structure
- Laravel 12
- Kotlin
- Material Design 3
- Tailwind CSS
- UUID v7
- Database Agnostic Design
- REST API
- JWT Authentication
- Offline First
- Three-layer Smart Cache
- OCR Pipeline
- Hash Synchronization
- Slug-based Resources

---

# Revision History

| Version | Date | Description |
|----------|------------|---------------------------|
| 1.0.0 | 2026-06-26 | Initial project standards |