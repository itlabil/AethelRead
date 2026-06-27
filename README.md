# Aethel Read

<p align="center">
  <img src="./docs/assets/logo.png" alt="Aethel Read Logo" width="140">
</p>

<h1 align="center">Aethel Read</h1>

<p align="center">
  <strong>Your Reading Companion</strong>
</p>

<p align="center">
An Android companion application that helps readers identify characters, places, and items while reading novels, manga, manhwa, and manhua using on-device OCR and intelligent entity recognition.
</p>

---

## Overview

Aethel Read is **not a reading application**.

Instead, it acts as a smart companion that helps readers instantly identify:

* Characters
* Places
* Items

while reading from any application, including:

* Chrome
* Firefox
* Opera
* Novel Reader Apps
* Manga Reader Apps
* Manhwa Reader Apps
* Manhua Reader Apps

The application runs as a floating bubble and can recognize names appearing on the screen without interrupting the reading experience.

---

## Key Features

### Floating Bubble

A lightweight floating widget accessible from anywhere.

### On-device OCR

Text recognition is performed locally on the Android device.

No screenshots are uploaded to the server.

### Smart Entity Recognition

Recognizes:

* Character names
* Aliases
* Places
* Items

Supports multiple aliases for a single entity.

Example:

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

### Offline Cache

Entity data is cached locally.

Only missing or updated entities are downloaded.

---

### Hash Synchronization

The application compares entity hashes before downloading updates.

This minimizes:

* bandwidth
* storage usage
* synchronization time

---

### Multi-language Description

Entity descriptions are available in:

* English
* Indonesian

Names remain identical to the original novel.

Only descriptions are translated.

---

### Supported Content Types

* Manga
* Manhwa
* Manhua
* Other

---

## Project Goals

* Lightweight
* Fast
* Privacy-first
* Offline-friendly
* Minimal network usage
* Easy to extend

---

# Technology Stack

## Android

* Kotlin
* Jetpack Compose
* Room Database
* ML Kit OCR
* Hilt
* Retrofit
* Coil

---

## Backend

* Laravel 12
* PHP 8.4+
* JWT Authentication
* REST API
* PostgreSQL / MySQL Compatible

---

## Admin Panel

* Laravel Blade
* Tailwind CSS
* Alpine.js

---

## Database

Designed to be database agnostic.

Supported:

* PostgreSQL
* MySQL
* MariaDB

Uses:

* UUID v7
* Soft Delete
* Foreign Keys
* Hash Synchronization

---

# Versioning

Semantic Versioning (SemVer)

Example

```
v1.0.0
```

---

# License

License information will be added before the first public release.

---

# Project Status

Current Phase

> Blueprint & Architecture

Status

🟡 In Active Development

---

# Author

Aethel Read Team

Project initiated by:

**Erwin Dianto**

---

# Tagline

> **Aethel Read**
>
> **Your Reading Companion**
