# API Usage Guide — URL Shortener API

This guide explains how to test every endpoint using **Postman** or **cURL**.

---

## Base URL

| Environment   | Base URL                     |
|---------------|------------------------------|
| Artisan Serve | `http://127.0.0.1:8000`      |
| Laravel Herd  | `http://assignment-04.test`  |

All examples below use `http://127.0.0.1:8000`. Replace with your base URL as needed.

---

## Step 1 — Start the Server

```bash
php artisan serve
```

---

## Step 2 — Register a User

**POST** `/api/register`

### Postman
- Method: `POST`
- URL: `http://127.0.0.1:8000/api/register`
- Body → raw → JSON:

```json
{
  "name": "Md Abul Bashar",
  "email": "hmbashar@gmail.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### cURL
```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Md Abul Bashar",
    "email": "hmbashar@gmail.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Expected Response — `201 Created`
```json
{
  "message": "User registered successfully.",
  "user": {
    "id": 1,
    "name": "Md Abul Bashar",
    "email": "hmbashar@gmail.com"
  },
  "token": "1|abc123plainTextToken..."
}
```

> **Copy the `token` value.** You will need it for all protected endpoints.

---

## Step 3 — Login

**POST** `/api/login`

### cURL
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "hmbashar@gmail.com", "password": "password123"}'
```

### Expected Response — `200 OK`
```json
{
  "message": "Login successful.",
  "user": { "id": 1, "name": "Md Abul Bashar", "email": "hmbashar@gmail.com" },
  "token": "2|xyz789plainTextToken..."
}
```

### Wrong Credentials — `401 Unauthorized`
```json
{ "message": "Invalid credentials." }
```

---

## Step 4 — Using the Token

For all protected routes, add this header:

```
Authorization: Bearer <your-token-here>
```

### In Postman
- Open the **Authorization** tab
- Type: `Bearer Token`
- Paste your token in the Token field

### In cURL
Add `-H "Authorization: Bearer <TOKEN>"` to every command.

---

## Step 5 — User Profile

### Get Profile — `GET /api/user`
```bash
curl -X GET http://127.0.0.1:8000/api/user \
  -H "Authorization: Bearer <TOKEN>"
```

**Response `200`:**
```json
{
  "user": {
    "id": 1,
    "name": "Md Abul Bashar",
    "email": "hmbashar@gmail.com",
    "created_at": "2026-03-12T10:00:00.000000Z",
    "updated_at": "2026-03-12T10:00:00.000000Z"
  }
}
```

---

### Update Profile — `PUT /api/user`
```bash
curl -X PUT http://127.0.0.1:8000/api/user \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"name": "Updated Name"}'
```

**Response `200`:**
```json
{
  "message": "Profile updated successfully.",
  "user": { "id": 1, "name": "Updated Name", "email": "hmbashar@gmail.com" }
}
```

---

### Delete Account — `DELETE /api/user`
```bash
curl -X DELETE http://127.0.0.1:8000/api/user \
  -H "Authorization: Bearer <TOKEN>"
```

**Response: `204 No Content`** — User and all their URLs are deleted.

---

## Step 6 — Create a Short URL

**POST** `/api/urls`

### cURL
```bash
curl -X POST http://127.0.0.1:8000/api/urls \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"original_url": "https://www.google.com"}'
```

### With optional expiry date
```bash
curl -X POST http://127.0.0.1:8000/api/urls \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "original_url": "https://www.google.com",
    "expires_at": "2026-12-31 23:59:59"
  }'
```

### Expected Response — `201 Created`
```json
{
  "message": "Short URL created successfully.",
  "short_url": {
    "id": 1,
    "original_url": "https://www.google.com",
    "short_code": "aB3kZp",
    "short_link": "http://127.0.0.1:8000/aB3kZp",
    "clicks": 0,
    "expires_at": null,
    "created_at": "2026-03-12T10:00:00+00:00",
    "updated_at": "2026-03-12T10:00:00+00:00"
  }
}
```

> **Copy `short_code`** — you will use it to test the redirect.

### Validation Error — `422`
```json
{
  "message": "The original url field is required.",
  "errors": {
    "original_url": ["The original url field is required."]
  }
}
```

---

## Step 7 — List Short URLs (Paginated)

**GET** `/api/urls`

```bash
curl -X GET "http://127.0.0.1:8000/api/urls?page=1" \
  -H "Authorization: Bearer <TOKEN>"
```

**Response `200`:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "original_url": "https://www.google.com",
      "short_code": "aB3kZp",
      "clicks": 2,
      "expires_at": null,
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "per_page": 10,
  "total": 1
}
```

---

## Step 8 — View a Specific Short URL

**GET** `/api/urls/{id}`

```bash
curl -X GET http://127.0.0.1:8000/api/urls/1 \
  -H "Authorization: Bearer <TOKEN>"
```

If the URL belongs to another user — **`403 Forbidden`:**
```json
{ "message": "This action is unauthorized." }
```

---

## Step 9 — Update a Short URL

**PUT** `/api/urls/{id}`

```bash
curl -X PUT http://127.0.0.1:8000/api/urls/1 \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "original_url": "https://www.example.com",
    "expires_at": "2027-01-01 00:00:00"
  }'
```

**Response `200`:**
```json
{
  "message": "Short URL updated successfully.",
  "short_url": { ... }
}
```

---

## Step 10 — Delete a Short URL

**DELETE** `/api/urls/{id}`

```bash
curl -X DELETE http://127.0.0.1:8000/api/urls/1 \
  -H "Authorization: Bearer <TOKEN>"
```

**Response: `204 No Content`**

---

## Step 11 — Use the Short URL (Public Redirect)

No token required. Open in a browser or use cURL:

```bash
# Follow the redirect (-L flag)
curl -L http://127.0.0.1:8000/aB3kZp

# Just check the status code without following
curl -I http://127.0.0.1:8000/aB3kZp
```

| Scenario              | Response               |
|-----------------------|------------------------|
| Valid short code      | `302 Found` → redirect |
| Expired short code    | `410 Gone`             |
| Unknown short code    | `404 Not Found`        |

**410 Gone response:**
```json
{ "message": "This short URL has expired." }
```

---

## Step 12 — Logout

**POST** `/api/logout`

```bash
curl -X POST http://127.0.0.1:8000/api/logout \
  -H "Authorization: Bearer <TOKEN>"
```

**Response: `204 No Content`** — Token is revoked immediately.

---

## HTTP Status Code Reference

| Code | Meaning                              | When                                      |
|------|--------------------------------------|-------------------------------------------|
| 200  | OK                                   | Successful GET / PUT                      |
| 201  | Created                              | Register / Create URL                     |
| 204  | No Content                           | Logout / Delete                           |
| 302  | Found (Redirect)                     | Successful short URL hit                  |
| 401  | Unauthorized                         | Missing or invalid token / wrong password |
| 403  | Forbidden                            | Accessing another user's URL              |
| 404  | Not Found                            | Short code does not exist                 |
| 410  | Gone                                 | Short URL has expired                     |
| 422  | Unprocessable Entity (validation)    | Failed form validation                    |

---

## Postman Collection Quick Setup

1. Create a new **Collection** called `URL Shortener API`
2. Set a **Collection Variable** `base_url` = `http://127.0.0.1:8000`
3. Set a **Collection Variable** `token` = *(fill in after register/login)*
4. For all protected requests, under **Authorization** tab → Type: `Bearer Token` → Token: `{{token}}`
5. Run **Register** first, copy the token, update the `token` variable
