# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Symfony 7.4 REST API backend + Vue 3 TypeScript frontend, containerized with Docker. The backend serves server inventory data (parsed from `.xlsx`) filtered/sorted/paginated via a single `GET /servers` endpoint. Frontend displays a filterable, sortable, comparable table.

## Local Development

Services are managed via Docker Compose:

```bash
docker-compose up -d                                      # Start webserver, php-fpm, redis
docker-compose exec php-fpm composer install              # Install backend dependencies
docker-compose --profile dev up vue                       # Start Vue dev server (optional)
docker-compose exec vue npm i && npm run dev              # Frontend dev deps + server
```

URLs:
- Backend API: `http://localhost/servers`
- Frontend (dist): `http://localhost:8080`
- Vite dev server: `http://localhost:5173`

## Commands

### Backend (run inside `php-fpm` container or with `docker-compose exec php-fpm`)

```bash
composer install
./bin/phpunit                                # Run all tests
./bin/phpunit tests/path/to/TestClass.php    # Run a single test file
./bin/console cache:pool:clear server.cache  # Clear Redis cache
```

### Frontend (run inside `vue` container or from `frontend/`)

```bash
npm run dev          # Vite dev server
npm run build        # Production build
npm run type-check   # TypeScript validation
npm run lint         # ESLint with auto-fix
npm run format       # Prettier
npm run test:unit    # Vitest unit tests
npm run test:e2e     # Cypress E2E tests
```

### CI

CI runs only PHPUnit (no frontend tests). Triggered on every push via `.github/workflows/ci.yml`.

## Architecture

### Backend (`backend/`)

- **Single endpoint:** `GET /servers` — filtering, ordering, pagination via query params
- **Data source:** `.xlsx` file parsed by `phpoffice/phpspreadsheet`, cached in Redis (`server.cache` pool)
- **No database** — entire dataset lives in Redis cache
- **Pattern:** Controller → UseCase → Repository → Domain

Key layers:
- `src/Controller/` — Symfony attribute-based routing
- `src/UseCase/` — `GetServersUseCase` orchestrates fetching + filtering
- `src/Repository/` — `ServerRepository` wraps cache; implements `RepositoryInterface`
- `src/Request/GetServersRequest` — wraps validated query params (page, itemsPerPage, filters, order)
- `src/Response/GetServersResponse` — wraps result + `Meta` (pagination info)
- `src/Domain/` — `Server`, `ServerCollection`, `ServerComparator`; HDD parsing logic lives here
- `src/Error/` — Typed error classes (`InvalidOrderFieldError`, `InvalidFilterFieldError`, etc.)

### Frontend (`frontend/`)

- **Vue 3 + TypeScript + Vite + Pinia + Vue Router + TailwindCSS**
- **Single view:** `HomeView` contains Filters, Server table, Pagination, and Compare panel
- **State:** Pinia store holds server list, selected filters, pagination state, comparison selection
- **API:** Axios service calls `GET /servers`; all filtering/sorting is server-side

Key components:
- `Server` — renders the data table
- `Filters` — filter controls (location, RAM, HDD type, etc.)
- `Pagination` — page navigation
- `Compare` / `CompareCard` — side-by-side server comparison with diff highlighting

### Notable Details

- HDD field parsing (e.g. `"2x500GBSATA2"` → count + capacity + type) happens in the backend domain layer
- CORS is handled by `nelmio/cors-bundle`
- `symfony/serializer` + `symfony/validator` handle request deserialization and validation
