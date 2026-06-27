# REST API Design

## Purpose

This document defines the REST API contract for Aethel Read.

The API serves as the only communication layer between clients and the backend.

Clients include:

- Android Application
- Admin Panel
- Future Web Client
- Future Browser Extension

All clients must consume the API defined in this document.

---

# 1. API Versioning

Every endpoint shall include an API version.

Example

/api/v1/

Future versions

/api/v2/

/api/v3/

Version changes must remain backward compatible whenever possible.

---

# 2. Base URL

Development

https://localhost/api/v1

Staging

https://staging.example.com/api/v1

Production

https://api.aethelread.com/api/v1

---

# 3. API Principles

The API follows RESTful principles.

Rules

- Stateless
- Resource-oriented
- JSON only
- HTTPS only
- UTF-8 encoding

---

# 4. Authentication

Authentication uses JWT.

Authorization Header

Authorization: Bearer <token>

Public endpoints do not require authentication.

Administrative endpoints require JWT authentication.

---

# 5. Response Envelope

Every successful response follows the same structure.

{
    "success": true,
    "message": "Success",
    "data": {},
    "meta": {}
}

---

Every failed response follows the same structure.

{
    "success": false,
    "message": "Validation failed.",
    "errors": {}
}

This format is mandatory.

---

# 6. HTTP Status Codes

200 OK

Successful request.

201 Created

New resource created.

204 No Content

Successful request without response body.

400 Bad Request

Malformed request.

401 Unauthorized

Authentication required.

403 Forbidden

Permission denied.

404 Not Found

Requested resource not found.

409 Conflict

Duplicate resource.

422 Unprocessable Entity

Validation failed.

429 Too Many Requests

Rate limit exceeded.

500 Internal Server Error

Unexpected server error.

---

# 7. Pagination

List endpoints support pagination.

Query Parameters

?page=1

?per_page=20

Default

page = 1

per_page = 20

Maximum

per_page = 100

Example Response

meta

current_page

per_page

total

last_page

---

# 8. Sorting

Sorting format

?sort=name

Descending

?sort=-name

Multiple sorting

?sort=type,name

---

# 9. Filtering

Filtering uses query parameters.

Examples

?type=character

?language=en

?status=published

Multiple filters may be combined.

---

# 10. Searching

General search parameter

?q=cheon

Search is case-insensitive.

Search uses normalized text.

---

# 11. Rate Limiting

Public API

60 requests / minute

Authenticated API

300 requests / minute

Synchronization endpoints may use different limits.

---

# 12. Content-Type

Every request

Content-Type:

application/json

File uploads

multipart/form-data

---

# 13. Date Format

All timestamps use ISO-8601.

Example

2026-06-26T10:30:00Z

---

# 14. UUID Format

All resources use UUID Version 7.

Numeric IDs are never exposed.

---

# 15. Error Object

Example

{
    "success": false,
    "message": "Validation failed.",
    "errors": {
        "title": [
            "Title is required."
        ]
    }
}

---

# 16. Operation IDs

Every endpoint has a unique operation ID.

Examples

auth.login

auth.logout

novels.index

novels.show

entities.search

entities.show

entities.sync

users.store

users.update

users.destroy

These identifiers remain stable across API versions.

---

# 17. Authentication API

Authentication uses JWT.

Only administrator accounts may authenticate.

Reader accounts are outside the scope of Version 1.

---

# POST /auth/login

Operation ID

auth.login

Permission

Public

Description

Authenticate an administrator and issue a JWT access token.

Request

POST /api/v1/auth/login

Content-Type

application/json

Body

{
    "email": "admin@example.com",
    "password": "secret"
}

Validation

email

- required
- valid email

password

- required
- minimum 8 characters

Success Response

HTTP 200

{
    "success": true,
    "message": "Login successful.",
    "data": {
        "access_token": "...",
        "token_type": "Bearer",
        "expires_in": 3600,
        "user": {
            "uuid": "...",
            "name": "Administrator",
            "email": "admin@example.com",
            "role": {
                "slug": "super-admin",
                "name": "Super Admin"
            }
        }
    }
}

Possible Errors

401

AUTH_INVALID_CREDENTIALS

422

VALIDATION_FAILED

---

# POST /auth/logout

Operation ID

auth.logout

Permission

Authenticated

Description

Invalidate the current JWT token.

Request

Authorization

Bearer Token

Success Response

HTTP 200

{
    "success": true,
    "message": "Logout successful.",
    "data": null
}

Possible Errors

401

AUTH_TOKEN_INVALID

---

# POST /auth/refresh

Operation ID

auth.refresh

Permission

Authenticated

Description

Generate a new access token.

Request

Authorization

Bearer Token

Success Response

HTTP 200

{
    "success": true,
    "message": "Token refreshed.",
    "data": {
        "access_token": "...",
        "expires_in": 3600
    }
}

Possible Errors

401

AUTH_TOKEN_EXPIRED

AUTH_TOKEN_INVALID

---

# GET /auth/me

Operation ID

auth.me

Permission

Authenticated

Description

Return the currently authenticated administrator.

Success Response

HTTP 200

{
    "success": true,
    "message": "User retrieved successfully.",
    "data": {
        "uuid": "...",
        "name": "...",
        "email": "...",
        "role": {
            "slug": "editor",
            "name": "Editor"
        }
    }
}

---

# Password Rules

Passwords shall satisfy the following requirements.

Minimum

8 characters

Recommended

- Uppercase
- Lowercase
- Number
- Symbol

Password hashes shall never be returned.

---

# JWT Lifetime

Access Token

60 minutes

Refresh Token

Not used in Version 1.

The Refresh endpoint issues a new JWT based on the current authenticated session.

---

# Authentication Errors

AUTH_INVALID_CREDENTIALS

Incorrect email or password.

AUTH_TOKEN_INVALID

The supplied token is invalid.

AUTH_TOKEN_EXPIRED

The supplied token has expired.

AUTH_UNAUTHORIZED

Authentication required.

AUTH_FORBIDDEN

Insufficient permissions.

---

# 18. Master Data API

Master data provides reference information used throughout the application.

Master data changes infrequently.

All endpoints are read-only.

Authentication is not required.

---

# GET /content-types

Operation ID

content-types.index

Description

Return all supported content types.

Example Response

HTTP 200

{
    "success": true,
    "message": "Content types retrieved successfully.",
    "data": [
        {
            "uuid": "...",
            "slug": "manga",
            "name": "Manga"
        },
        {
            "uuid": "...",
            "slug": "manhwa",
            "name": "Manhwa"
        },
        {
            "uuid": "...",
            "slug": "manhua",
            "name": "Manhua"
        },
        {
            "uuid": "...",
            "slug": "other",
            "name": "Other"
        }
    ]
}

---

# GET /entity-types

Operation ID

entity-types.index

Description

Return all entity types.

Example Response

{
    "success": true,
    "message": "Entity types retrieved successfully.",
    "data": [
        {
            "uuid": "...",
            "slug": "character",
            "name": "Character"
        },
        {
            "uuid": "...",
            "slug": "place",
            "name": "Place"
        },
        {
            "uuid": "...",
            "slug": "item",
            "name": "Item"
        }
    ]
}

---

# GET /entity-subtypes

Operation ID

entity-subtypes.index

Description

Return available entity subtypes.

Optional Query

?entity_type=character

Example Response

{
    "success": true,
    "message": "Entity subtypes retrieved successfully.",
    "data": [
        {
            "uuid": "...",
            "slug": "main-character",
            "name": "Main Character"
        },
        {
            "uuid": "...",
            "slug": "villain",
            "name": "Villain"
        }
    ]
}

---

# GET /languages

Operation ID

languages.index

Description

Return supported languages.

Example Response

{
    "success": true,
    "message": "Languages retrieved successfully.",
    "data": [
        {
            "uuid": "...",
            "code": "en",
            "name": "English",
            "native_name": "English"
        },
        {
            "uuid": "...",
            "code": "id",
            "name": "Indonesian",
            "native_name": "Bahasa Indonesia"
        }
    ]
}

---

# Cache Strategy

Clients may cache master data.

Recommended cache duration

24 hours

Clients may refresh manually when needed.

---

# Error Responses

404

Resource not found.

500

Unexpected server error.

---

# 19. Novel API

Novel endpoints provide information about supported novels.

Android uses these endpoints to populate the novel selector shown in the floating panel.

Public endpoints.

Authentication is not required.

---

# GET /novels

Operation ID

novels.index

Description

Returns a paginated list of supported novels.

Query Parameters

page

per_page

q

type

sort

Examples

GET /api/v1/novels

GET /api/v1/novels?type=manhwa

GET /api/v1/novels?q=nano

GET /api/v1/novels?sort=title

Success Response

{
    "success": true,
    "message": "Novels retrieved successfully.",
    "data": [
        {
            "uuid": "...",
            "slug": "nano-machine",
            "title": "Nano Machine",
            "type": {
                "slug": "manhwa",
                "name": "Manhwa"
            },
            "cover_image": "...",
            "entity_count": 352,
            "updated_at": "...",
            "hash": "..."
        }
    ],
    "meta": {
        "current_page":1,
        "per_page":20,
        "total":120,
        "last_page":6
    }
}

---

# GET /novels/{uuid}

Operation ID

novels.show

Description

Returns detailed information about one novel.

Success Response

{
    "success": true,
    "message": "Novel retrieved successfully.",
    "data": {
        "uuid":"...",
        "slug":"nano-machine",
        "title":"Nano Machine",
        "type":{
            "slug":"manhwa",
            "name":"Manhwa"
        },
        "cover_image":"...",
        "entity_count":352,
        "updated_at":"...",
        "hash":"..."
    }
}

404

NOVEL_NOT_FOUND

---

# GET /novels/recent

Operation ID

novels.recent

Description

Returns recently updated novels.

Default

20 novels

Purpose

Allows Android to quickly display recently updated novels.

---

# GET /novels/hash

Operation ID

novels.hash

Description

Returns only synchronization information.

Example

{
    "success":true,
    "data":[
        {
            "uuid":"...",
            "hash":"...",
            "updated_at":"..."
        }
    ]
}

Purpose

Allows Android to determine whether cached novel metadata requires updating.

Only metadata is returned.

---

# Caching

Clients should cache novel metadata.

Recommended duration

24 hours

Hash comparison should be performed before downloading updated records.

---

# Possible Errors

NOVEL_NOT_FOUND

VALIDATION_FAILED

RATE_LIMIT_EXCEEDED

INTERNAL_SERVER_ERROR

---

# 20. Entity API

Entity endpoints provide searchable story information.

Entities include:

- Character
- Place
- Item

Android performs OCR locally.

The backend never processes screenshots or OCR text.

---

# GET /entities

Operation ID

entities.index

Description

Returns entities.

Supports filtering.

Query Parameters

novel_id

type

subtype

page

per_page

sort

Example

GET /api/v1/entities?novel_id=...

---

# GET /entities/{uuid}

Operation ID

entities.show

Description

Returns one entity.

Example Response

{
    "success":true,
    "message":"Entity retrieved successfully.",
    "data":{
        "uuid":"...",
        "slug":"mok-gyeongwoon",
        "main_name":"Mok Gyeongwoon",

        "type":{
            "slug":"character",
            "name":"Character"
        },

        "subtype":{
            "slug":"main-character",
            "name":"Main Character"
        },

        "image":"...",

        "descriptions":[
            {
                "language":"en",
                "description":"..."
            },
            {
                "language":"id",
                "description":"..."
            }
        ],

        "aliases":[
            "Cheon Ma",
            "Heavenly Demon"
        ],

        "updated_at":"...",
        "hash":"..."
    }
}

404

ENTITY_NOT_FOUND

---

# POST /entities/search

Operation ID

entities.search

Description

Returns entities matching names supplied by Android.

Android already performs OCR.

Server only searches the supplied names.

Request

{
    "novel_uuid":"...",

    "names":[
        "Cheon Ma",
        "Mok Gyeongwoon",
        "Blade God"
    ]
}

Success Response

{
    "success":true,

    "data":[

        {
            "matched":"Cheon Ma",

            "entity_uuid":"...",

            "main_name":"Mok Gyeongwoon",

            "hash":"..."
        },

        {
            "matched":"Blade God",

            "entity_uuid":"...",

            "main_name":"Blade God",

            "hash":"..."
        }

    ]
}

Purpose

Android determines which entities require downloading.

---

# POST /entities/sync

Operation ID

entities.sync

Description

Synchronizes entity data.

Request

{
    "entities":[

        {
            "uuid":"...",
            "hash":"..."
        },

        {
            "uuid":"...",
            "hash":"..."
        }

    ]
}

Server compares hashes.

Only changed entities are returned.

Success Response

{
    "success":true,

    "data":[

        {
            "uuid":"...",

            "updated":true,

            "entity":{

            }

        },

        {
            "uuid":"...",

            "updated":false

        }

    ]
}

---

# POST /entities/download

Operation ID

entities.download

Description

Downloads entity data not available in cache.

Request

{
    "uuids":[
        "...",
        "..."
    ]
}

Success Response

{
    "success":true,

    "data":[

        {
            ...
        }

    ]
}

---

# GET /entities/hash

Operation ID

entities.hash

Description

Returns entity hashes only.

Useful for lightweight synchronization.

---

# Search Priority

The backend searches in the following order.

1.

Main Name

↓

2.

Alias

↓

3.

Keyword

If multiple entities match,

the backend returns all matches.

Android determines presentation order.

---

# Privacy

The backend never receives:

- Screenshots
- Images
- OCR output
- Reading history

Only candidate names supplied by Android.

---

# Revision History

| Version | Date | Description |
|----------|------|-------------|
| 1.4.0 | Added Entity API |