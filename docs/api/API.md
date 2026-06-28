---
project: Aethel Read
document: API Documentation
version: 1.0.0
base_url: /api/v1
auth: JWT Bearer Token
---

# Aethel Read — API Documentation

## Base URL

```
/api/v1
```

## Authentication

Semua endpoint yang membutuhkan autentikasi menggunakan JWT Bearer Token.

```
Authorization: Bearer {token}
```

## Response Format

### Success

```json
{
  "success": true,
  "message": "Success message",
  "data": {}
}
```

### Error

```json
{
  "success": false,
  "message": "Error message",
  "errors": null
}
```

### Validation Error

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "field": ["Error message"]
  }
}
```

---

## Authentication Endpoints

### POST /api/v1/auth/login

Login dan dapatkan JWT token.

**Auth required:** No

**Request Body:**

```json
{
  "email": "admin@aethelread.com",
  "password": "password123"
}
```

**Response 200:**

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "access_token": "eyJ...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

**Response 401:**

```json
{
  "success": false,
  "message": "Invalid credentials",
  "errors": null
}
```

---

### POST /api/v1/auth/logout

Logout dan invalidate token.

**Auth required:** Yes

**Response 200:**

```json
{
  "success": true,
  "message": "Logout successful",
  "data": null
}
```

---

### POST /api/v1/auth/refresh

Refresh JWT token.

**Auth required:** Yes

**Response 200:**

```json
{
  "success": true,
  "message": "Token refreshed",
  "data": {
    "access_token": "eyJ...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

---

### GET /api/v1/auth/me

Dapatkan data user yang sedang login.

**Auth required:** Yes

**Response 200:**

```json
{
  "success": true,
  "message": "User retrieved",
  "data": {
    "id": "uuid",
    "name": "Super Admin",
    "email": "superadmin@aethelread.com",
    "role": "superadmin"
  }
}
```

---

## Novel Endpoints

### GET /api/v1/novels

Dapatkan semua novel yang aktif.

**Auth required:** No

**Response 200:**

```json
{
  "success": true,
  "message": "Novels retrieved successfully",
  "data": [
    {
      "slug": "nano-machine",
      "name": "Nano Machine",
      "type": "manhwa",
      "type_label": "Manhwa",
      "hash": "sha256...",
      "is_active": true,
      "created_at": "2026-06-26T00:00:00.000000Z",
      "updated_at": "2026-06-26T00:00:00.000000Z"
    }
  ]
}
```

---

### GET /api/v1/novels/{slug}

Dapatkan detail novel berdasarkan slug.

**Auth required:** No

**URL Parameters:**

| Parameter | Type   | Description  |
|-----------|--------|--------------|
| slug      | string | Novel slug   |

**Response 200:**

```json
{
  "success": true,
  "message": "Novel retrieved successfully",
  "data": {
    "slug": "nano-machine",
    "name": "Nano Machine",
    "type": "manhwa",
    "type_label": "Manhwa",
    "hash": "sha256...",
    "is_active": true,
    "created_at": "2026-06-26T00:00:00.000000Z",
    "updated_at": "2026-06-26T00:00:00.000000Z"
  }
}
```

**Response 404:**

```json
{
  "success": false,
  "message": "Resource not found.",
  "errors": null
}
```

---

## Entity Endpoints

### GET /api/v1/novels/{novelSlug}/entities

Dapatkan semua entity aktif dari sebuah novel.

Digunakan oleh Android untuk **initial download** ke local cache.

**Auth required:** No

**URL Parameters:**

| Parameter  | Type   | Description  |
|------------|--------|--------------|
| novelSlug  | string | Novel slug   |

**Response 200:**

```json
{
  "success": true,
  "message": "Entities retrieved successfully",
  "data": [
    {
      "slug": "cheon-yeo-woon",
      "name": "Cheon Yeo-Woon",
      "type": "character",
      "type_label": "Character",
      "hash": "sha256...",
      "is_active": true,
      "novel": null,
      "aliases": [],
      "keywords": [],
      "descriptions": [],
      "image": [],
      "created_at": "2026-06-26T00:00:00.000000Z",
      "updated_at": "2026-06-26T00:00:00.000000Z"
    }
  ]
}
```

---

### GET /api/v1/novels/{novelSlug}/entities/{entitySlug}

Dapatkan detail lengkap sebuah entity.

Digunakan oleh Android untuk menampilkan **Entity Detail Screen**.

**Auth required:** No

**URL Parameters:**

| Parameter   | Type   | Description   |
|-------------|--------|---------------|
| novelSlug   | string | Novel slug    |
| entitySlug  | string | Entity slug   |

**Query Parameters:**

| Parameter | Type   | Default | Description                    |
|-----------|--------|---------|--------------------------------|
| locale    | string | en      | Language code (`en` atau `id`) |

**Response 200:**

```json
{
  "success": true,
  "message": "Entity retrieved successfully",
  "data": {
    "slug": "cheon-yeo-woon",
    "name": "Cheon Yeo-Woon",
    "type": "character",
    "type_label": "Character",
    "hash": "sha256...",
    "is_active": true,
    "novel": {
      "slug": "nano-machine",
      "name": "Nano Machine"
    },
    "aliases": [
      { "id": "uuid", "name": "Heavenly Demon" },
      { "id": "uuid", "name": "Young Master Cheon" }
    ],
    "keywords": [
      { "id": "uuid", "keyword": "cheon" },
      { "id": "uuid", "keyword": "yeo-woon" }
    ],
    "descriptions": [
      {
        "locale": "en",
        "content": "The main protagonist of Nano Machine..."
      }
    ],
    "image": {
      "thumbnail_url": "http://localhost:8000/storage/images/thumbnails/xxx.webp",
      "original_url": "http://localhost:8000/storage/images/original/xxx.jpg",
      "hash": "sha256...",
      "width": 512,
      "height": 512,
      "size": 24680
    },
    "created_at": "2026-06-26T00:00:00.000000Z",
    "updated_at": "2026-06-26T00:00:00.000000Z"
  }
}
```

**Response 404:**

```json
{
  "success": false,
  "message": "Entity not found.",
  "errors": null
}
```

---

### POST /api/v1/novels/{novelSlug}/entities/sync

Sinkronisasi entity antara Android dan server.

Android mengirimkan hash entity yang dimiliki secara lokal. Server merespons dengan data yang perlu diupdate, ditambahkan, atau dihapus.

**Auth required:** No

**URL Parameters:**

| Parameter  | Type   | Description  |
|------------|--------|--------------|
| novelSlug  | string | Novel slug   |

**Request Body:**

```json
{
  "hashes": {
    "cheon-yeo-woon": "sha256...",
    "nano-machine-lab": "sha256..."
  }
}
```

**Response 200:**

```json
{
  "success": true,
  "message": "Sync completed successfully",
  "data": {
    "sync": {
      "new": 5,
      "updated": 2,
      "deleted": ["old-entity-slug"]
    },
    "entities": [
      {
        "slug": "new-entity",
        "name": "New Entity",
        "type": "character",
        "hash": "sha256...",
        "aliases": [],
        "keywords": [],
        "descriptions": [],
        "image": []
      }
    ]
  }
}
```

---

## Error Codes

| HTTP Code | Description                              |
|-----------|------------------------------------------|
| 200       | Success                                  |
| 201       | Created                                  |
| 400       | Bad Request                              |
| 401       | Unauthenticated / Token expired          |
| 403       | Forbidden                                |
| 404       | Resource not found                       |
| 405       | Method not allowed                       |
| 422       | Validation failed                        |
| 500       | Internal server error                    |

---

## Entity Types

| Type      | Description          |
|-----------|----------------------|
| character | Character in a novel |
| place     | Location in a novel  |
| item      | Item in a novel      |

---

## Novel Types

| Type    | Description |
|---------|-------------|
| manga   | Japanese manga |
| manhwa  | Korean manhwa  |
| manhua  | Chinese manhua |
| other   | Other types    |

---

## Supported Locales

| Locale | Language   |
|--------|------------|
| en     | English    |
| id     | Indonesian |

---

## Revision History

| Version | Date       | Description              |
|---------|------------|--------------------------|
| 1.0.0   | 2026-06-28 | Initial API Documentation |