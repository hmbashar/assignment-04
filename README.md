# Assignment : 04

### Name : Md Abul Bashar
### Email : hmbashar@gmail.com

---

## Project Description

A secure **RESTful URL Shortener API** built with **Laravel 12** and **Laravel Sanctum** for token-based authentication.

Features:
- User registration & login with Sanctum plain-text API tokens
- Authenticated user profile management (CRUD)
- URL shortening with auto-generated short codes
- Click tracking and optional link expiry
- Public redirect endpoint with 302, 410, and 404 responses
- Policy-based authorization (users can only access their own URLs)

---

## Technology Stack

| Layer         | Technology                  |
|---------------|-----------------------------|
| Framework     | Laravel 12                  |
| Auth          | Laravel Sanctum (API Token) |
| Database      | SQLite                      |
| PHP           | 8.5+                        |

---

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/hmbashar/assignment-04.git
cd assignment-04

# 2. Install PHP dependencies
composer install

# 3. Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# 4. Run database migrations
php artisan migrate

# 5. Serve the application (or use Laravel Herd)
php artisan serve
```

> **Base URL (Herd):** `http://assignment-04.test`  
> **Base URL (Artisan Serve):** `http://127.0.0.1:8000`

---

## API Endpoints


### Authentication (Public)

| Method | Endpoint           | Description                        | Auth |
|--------|--------------------|------------------------------------|------|
| POST   | `/api/register`    | Register a new user, returns token | No   |
| POST   | `/api/login`       | Login, returns token                | No   |
| POST   | `/api/logout`      | Revoke current token               | Yes  |

### User Profile (Protected)

| Method      | Endpoint    | Description                    | Auth |
|-------------|-------------|--------------------------------|------|
| GET         | `/api/user` | Get authenticated user profile | Yes  |
| PUT / PATCH | `/api/user` | Update name or email           | Yes  |
| DELETE      | `/api/user` | Delete account + all URLs      | Yes  |

### URL Management (Protected)

| Method | Endpoint           | Description                        | Auth |
|--------|--------------------|------------------------------------|------|
| GET    | `/api/urls`        | List user's URLs (paginated)       | Yes  |
| POST   | `/api/urls`        | Create a new short URL             | Yes  |
| GET    | `/api/urls/{id}`   | Get specific URL (403 if not own)  | Yes  |
| PUT    | `/api/urls/{id}`   | Update URL or expiry               | Yes  |
| DELETE | `/api/urls/{id}`   | Delete URL                         | Yes  |

### Public Redirect (No Auth)

| Method | Endpoint         | Description                          |
|--------|------------------|--------------------------------------|
| GET    | `/{short_code}`  | Redirect (302), 410 expired, 404 NF  |

---

## Authentication

All protected routes require the `Authorization` header:

```
Authorization: Bearer <your-api-token>
```

---

## Example Requests

### Register

```bash
curl -X POST http://assignment-04.test/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Md Abul Bashar",
    "email": "hmbashar@gmail.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Response (201):**
```json
{
  "message": "User registered successfully.",
  "user": { "id": 1, "name": "Md Abul Bashar", "email": "hmbashar@gmail.com" },
  "token": "1|abc123plainTextToken..."
}
```

---

### Login

```bash
curl -X POST http://assignment-04.test/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "hmbashar@gmail.com", "password": "password123"}'
```

**Response (200):**
```json
{
  "message": "Login successful.",
  "user": { "id": 1, "name": "Md Abul Bashar", "email": "hmbashar@gmail.com" },
  "token": "2|xyz789plainTextToken..."
}
```

**Invalid credentials Response (401):**
```json
{ "message": "Invalid credentials." }
```

---

### Logout

```bash
curl -X POST http://assignment-04.test/api/logout \
  -H "Authorization: Bearer 2|xyz789plainTextToken..."
```

**Response: 204 No Content**

---

### Get Profile

```bash
curl -X GET http://assignment-04.test/api/user \
  -H "Authorization: Bearer <TOKEN>"
```

**Response (200):**
```json
{
  "user": { "id": 1, "name": "Md Abul Bashar", "email": "hmbashar@gmail.com", "created_at": "..." }
}
```

---

### Update Profile

```bash
curl -X PUT http://assignment-04.test/api/user \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"name": "Bashar Updated"}'
```

---

### Delete Account

```bash
curl -X DELETE http://assignment-04.test/api/user \
  -H "Authorization: Bearer <TOKEN>"
```

**Response: 204 No Content**

---

## URL Management

### Create Short URL

```bash
curl -X POST http://assignment-04.test/api/urls \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "original_url": "https://www.google.com",
    "expires_at": "2026-12-31 23:59:59"
  }'
```

**Response (201):**
```json
{
  "message": "Short URL created successfully.",
  "short_url": {
    "id": 1,
    "original_url": "https://www.google.com",
    "short_code": "aB3kZp",
    "short_link": "http://assignment-04.test/aB3kZp",
    "clicks": 0,
    "expires_at": "2026-12-31T23:59:59+06:00",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

### List URLs (Paginated)

```bash
curl -X GET "http://assignment-04.test/api/urls?page=1" \
  -H "Authorization: Bearer <TOKEN>"
```

---

### Get Specific URL

```bash
curl -X GET http://assignment-04.test/api/urls/1 \
  -H "Authorization: Bearer <TOKEN>"
```

**403 Forbidden (not your URL):**
```json
{ "message": "This action is unauthorized." }
```

---

### Update URL

```bash
curl -X PUT http://assignment-04.test/api/urls/1 \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"original_url": "https://www.example.com", "expires_at": "2027-01-01 00:00:00"}'
```

---

### Delete URL

```bash
curl -X DELETE http://assignment-04.test/api/urls/1 \
  -H "Authorization: Bearer <TOKEN>"
```

**Response: 204 No Content**

---

## Public Redirect

```bash
# 302 Redirect (active URL)
curl -I http://assignment-04.test/aB3kZp

# 410 Gone (expired URL)
# Response: { "message": "This short URL has expired." }

# 404 Not Found (unknown code)
# Response: { "message": "Short URL not found." }
```

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── AuthController.php      # register, login, logout
│   │   │   ├── UserController.php      # show, update, destroy
│   │   │   └── ShortUrlController.php  # CRUD for URLs
│   │   └── RedirectController.php      # Public redirect
│   └── Requests/
│       ├── Auth/
│       │   ├── RegisterRequest.php
│       │   └── LoginRequest.php
│       ├── User/
│       │   └── UpdateProfileRequest.php
│       └── Url/
│           ├── StoreUrlRequest.php
│           └── UpdateUrlRequest.php
├── Models/
│   ├── User.php                        # HasApiTokens + hasMany ShortUrl
│   └── ShortUrl.php                    # belongsTo User
├── Policies/
│   └── ShortUrlPolicy.php              # Ownership authorization
└── Services/
    └── ShortCodeService.php            # Unique short code generator
routes/
├── api.php                             # All API routes
└── web.php                             # Public redirect route
```

---

## HTTP Status Codes Used

| Code | Description                         |
|------|-------------------------------------|
| 200  | Success                             |
| 201  | Created                             |
| 204  | No Content (logout, delete)         |
| 302  | Redirect (short URL hit)            |
| 401  | Unauthorized (invalid credentials)  |
| 403  | Forbidden (not URL owner)           |
| 404  | Not Found                           |
| 410  | Gone (URL expired)                  |
| 422  | Unprocessable Entity (validation)   |
