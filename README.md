# News Aggregator API

A REST API that pulls articles from **NewsAPI**, **The Guardian**, and **The New York Times** into a single searchable feed. Supports filtering by source, category, author, and date range. Authenticated users can set preferences to personalize their feed — preferred sources, categories, and authors are score-ranked to the top.

## Tech Stack

- **Framework** — Laravel 13 (PHP 8.4)
- **Database** — MySQL 8
- **Caching** — Redis 7
- **Authentication** — Laravel Sanctum (token-based)
- **API Docs** — Swagger (L5-Swagger)
- **Containerisation** — Docker & Docker Compose

## Features

- Unified article feed from three independent news providers
- Search and filter by keyword, source, category, author, and date range
- JWT-based authentication via Laravel Sanctum
- User preference system — articles are ranked based on your saved preferences:
  - Preferred sources
  - Preferred categories
  - Preferred authors
- Scheduled hourly article fetching
- Swagger UI for interactive API exploration
- Dockerised setup with automated migrations and seeding on first boot
- Feature test suite covering auth, articles, filters, and preferences

## Fetching Articles

Migrations and an initial article fetch run automatically on first boot. A dedicated scheduler container also starts alongside the app, running `php artisan schedule:work` to fetch new articles every hour — no additional setup needed.

## Architecture

```
HTTP Request
     ↓
Controller   (handles HTTP, validation)
     ↓
Service      (business logic)
     ↓
Repository   (database queries)
     ↓
Database
```

## Database Structure

**`articles`** — the core table. Each article belongs to a source and optionally a category, and stores a title, description, URL, and published date.

**`sources`**, **`categories`**, **`authors`** — lookup tables that normalise repeated values across articles. Authors have a many-to-many relationship with articles via `article_authors`, since an article can have multiple authors.

**`user_preferred_sources`**, **`user_preferred_categories`**, **`user_preferred_authors`** — pivot tables linking users to their saved preferences. Each is a composite primary key of `user_id` and the related entity. These are joined at query time to calculate a relevance score and rank articles.

## API Reference

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/auth/register` | — | Register a new user |
| POST | `/api/auth/login` | — | Login and receive a token |
| POST | `/api/auth/logout` | Required | Invalidate the current token |
| GET | `/api/articles` | — | List articles (search, filter, paginate) |
| GET | `/api/articles/{id}` | — | Get a single article |
| GET | `/api/me/preferences` | Required | Get current user preferences |
| PUT | `/api/me/preferences` | Required | Update user preferences |
| GET | `/api/filters/sources` | — | List all available sources |
| GET | `/api/filters/categories` | — | List all available categories |
| GET | `/api/filters/authors` | — | List all available authors |

Full interactive docs are available via Swagger at [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation) once the app is running.

To regenerate the docs after code changes:

```bash
php artisan l5-swagger:generate
```

## Running with Docker (recommended)

**Requirements:** Docker and Docker Compose

You will need API keys from each provider before starting:

- [NewsAPI](https://newsapi.org/register)
- [The Guardian](https://open-platform.theguardian.com/access/)
- [New York Times](https://developer.nytimes.com/get-started)

```bash
git clone <repo-url>
cd news-aggregator

cp .env.example .env
php artisan key:generate
```

Open `.env` and fill in your API keys:

```env
NEWSAPI_KEY=your_newsapi_key
GUARDIAN_API_KEY=your_guardian_key
NYT_API_KEY=your_nyt_key
```

Then start everything:

```bash
docker compose up -d --build
```

Migrations and an initial article fetch run automatically on first boot.

> **Note:** API keys must be set before starting Docker.

---

## Manual Setup (alternative)

```bash
git clone <repo-url>
cd news-aggregator

composer install

cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials and API keys:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=news_aggregator
DB_USERNAME=root
DB_PASSWORD=

NEWSAPI_KEY=your_newsapi_key
GUARDIAN_API_KEY=your_guardian_key
NYT_API_KEY=your_nyt_key
```

Run migrations and fetch initial articles:

```bash
php artisan migrate
php artisan articles:fetch
```

Start the development server:

```bash
php artisan serve
```

## Running Tests

Feature tests cover authentication, article listing and filtering, user preferences, and filter endpoints.

```bash
php artisan test
```
