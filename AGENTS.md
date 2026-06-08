# Development Commands

## Backend (Symfony 7, PHP 8.3)
Run inside `php-fpm` container via `docker-compose exec php-fpm`:
- `./bin/phpunit` - Run unit tests
- `./bin/console cache:pool:clear server.cache` - Clear cached server data

## Frontend (Vue 3, Vite)
Run inside `vue` container via `docker-compose exec vue`:
- `npm run dev` - Start dev server at http://localhost:5173
- `npm run build` - Build for production (outputs to `frontend/dist`)
- `npm run test:unit` - Run Vitest unit tests
- `npm run lint` - ESLint with `--fix`
- `npm run format` - Prettier

## Docker
- `docker-compose up -d` - Start webserver, php-fpm, redis
- `docker-compose up -d vue` - Start frontend dev container
- `docker-compose exec php-fpm composer install` - Install PHP dependencies

# Architecture Notes
- Data source: `servers.xlsx` loaded via `phpoffice/phpspreadsheet`, cached in Redis
- API: `GET /servers` for server list with filtering, ordering, pagination
- Frontend served at http://localhost:8080 (nginx webserver) or http://localhost:5173 (vite dev)