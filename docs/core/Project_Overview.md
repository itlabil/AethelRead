---
project: Aethel Read
tagline: Your Reading Companion
document: Project Overview
document_id: D-003
version: 1.0.0
status: Draft
language: English
last_updated: 2026-06-26
author: Erwin Dianto
---

# Project Overview

## Purpose

This document provides a high-level overview of the Aethel Read project.

It defines the project's vision, objectives, target users, scope, and overall direction. It serves as the primary reference for understanding what Aethel Read is, why it exists, and the problems it aims to solve.

This document is **Informative**, but all implementation decisions should remain consistent with the project's normative documents.

---

# Vision

To become the most trusted reading companion for readers of Light Novels, Manga, Manhwa, and Manhua by helping them instantly understand characters, places, items, and other important entities without interrupting their reading experience.

---

# Mission

Aethel Read aims to reduce reader confusion caused by long stories, multiple character names, aliases, locations, and special items by providing contextual information instantly through intelligent OCR recognition and a smart offline cache.

---

# Problem Statement

Readers often experience the following challenges:

- Forgetting who a character is after hundreds of chapters.
- Confusing aliases, titles, and alternative names.
- Forgetting the relationship between characters.
- Losing track of important places or artifacts.
- Interrupting the reading experience by searching information on external websites.

These interruptions reduce immersion and negatively affect the overall reading experience.

---

# Solution

Aethel Read works alongside existing reading applications.

Instead of replacing reading platforms, it recognizes visible text on the user's screen, detects important entities using on-device OCR, and displays concise contextual information through a lightweight floating interface.

Information is retrieved from a local cache whenever possible, ensuring fast responses and offline availability.

Missing data is automatically synchronized from the server without interrupting the user.

---

# Product Identity

| Property | Value |
|----------|-------|
| Product Name | Aethel Read |
| Tagline | Your Reading Companion |
| Category | Reading Companion |
| Primary Platform | Android |
| Supported Content | Light Novel, Manga, Manhwa, Manhua |
| Languages | English, Indonesian |

---

# Target Audience

Primary Audience

- Readers of Light Novels
- Readers of Manga
- Readers of Manhwa
- Readers of Manhua

Secondary Audience

- Readers of Web Novels
- Readers of Fantasy Novels
- Readers of Wuxia/Xianxia novels
- Readers of Web Comics

---

# Core Features

## Smart OCR Recognition

Recognizes visible text directly from the device screen using on-device OCR.

---

## Intelligent Entity Detection

Identifies characters, places, items, and other supported entities from OCR results.

---

## Floating Companion

Displays recognition results inside a lightweight floating bubble without interrupting the reading experience.

---

## Smart Offline Cache

Uses a three-layer caching mechanism to maximize speed while minimizing network usage.

---

## Automatic Synchronization

Downloads missing entity data only when required.

Already cached information is reused whenever possible.

---

## Image Optimization

Entity images are automatically processed into optimized WEBP thumbnails for consistent appearance and reduced storage usage.

---

## Multi-language Support

Supports English and Indonesian descriptions while preserving original entity names.

---

# Product Principles

The application should always:

- Respect the user's reading flow.
- Minimize interruptions.
- Prioritize performance.
- Work offline whenever possible.
- Consume minimal storage.
- Synchronize only necessary data.
- Maintain a clean and simple user interface.

---

# Non-Goals

The following are intentionally outside the scope of the initial release.

- Reading novels inside Aethel Read.
- Hosting copyrighted novels or comics.
- Social features.
- Community comments.
- User-generated content.
- AI-generated story summaries.
- Automatic spoiler detection.
- Recommendation system.

These features may be considered in future versions but are not part of Version 1.

---

# Success Metrics

The project aims to achieve the following measurable goals.

- OCR recognition feels instantaneous.
- Cached entity lookup completes in under one second.
- Minimal mobile storage usage.
- Low network consumption.
- High cache hit ratio.
- Consistent floating UI performance.
- Smooth reading experience.

---

# Future Vision

Although Version 1 targets Android, the overall architecture is designed to support future expansion, including:

- Browser Extensions
- Desktop Companion
- Official Website
- Cross-device Synchronization
- Additional Platforms

Future expansion must remain consistent with the project's original philosophy of being a reading companion rather than a reading application.

---

# Dependencies

The project depends on:

- Laravel Backend API
- Android Client
- Administration Panel
- OCR Engine
- Image Processing Pipeline
- Smart Cache System

Each component is documented separately within the project documentation.

---

# Related Documents

Normative Documents

- Project_Standards.md

Supporting Documents

- PRD.md
- Architecture.md
- Database.md
- API.md
- Android.md
- Admin.md

---

# Revision History

| Version | Date | Description |
|----------|------------|-----------------------------|
| 1.0.0 | 2026-06-26 | Initial project overview |