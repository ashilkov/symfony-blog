# Blog Platform (Symfony + API Platform + GraphQL + React)

<b>Currently in development.</b>

Portfolio/demo project: a blog platform with a clean separation between a Symfony backend (API only) and a React (Vite.js + TypeScript) frontend stored in a separate repository.

- Backend: Symfony 7, Doctrine ORM, API Platform (REST + GraphQL)
- API style: REST for Auth; GraphQL for everything else
- Architecture: Domain-Driven Design (DDD) with bounded contexts
- Database: PostgreSQL via Docker (local dev). SQLite can be used for quick local runs if preferred.
- Frontend: React + Vite + TypeScript (separate repo)

## TL;DR (Local Dev)

- Requirements: Docker + Docker Compose, Make (optional), PHP 8.2 + Composer (optional if running without Docker)
- Start services: `docker compose up -d`
- Install backend deps: `docker compose exec php composer install`
- Run migrations: `docker compose exec php bin/console doctrine:migrations:migrate -n`
- Seed data (optional if fixtures exist): `docker compose exec php bin/console doctrine:fixtures:load -n`
- Explore API docs (dev):
  - REST (Swagger UI): http://localhost:8000/api/docs
  - GraphQL endpoint: http://localhost:8000/api/graphql

Ports and service names depend on the compose file; adjust if you changed defaults.

## Tech Stack

- Symfony 7 (HTTP kernel, console, config, routing)
- Doctrine ORM 3 (data mapping)
- API Platform 4 (REST & GraphQL)
- PostgreSQL (Docker) for local dev
- PHPUnit for tests

## Architecture

This backend follows DDD with two bounded contexts:

- Blog Context:
  - Blog: blog metadata and ownership
  - Post: articles within a blog
  - BlogUser: relation of users to blogs (roles/collaboration)
  - Subscription: link between a user and a blog for following
- User Context:
  - User + Auth: registration, login and profile basics

Bounded contexts keep domain models cohesive and enforce clear boundaries between blog features and user/auth concerns.

## API Design

- REST (Auth only)
  - Authentication and user management are exposed as REST endpoints (e.g., login, refresh, register). Exact routes may vary depending on your security setup; see the auth controller/security config in the codebase.
- GraphQL (all other resources)
  - CRUD-like operations and queries for domain resources (e.g., Blog, Post) are exposed through GraphQL.
  - Endpoint: `/api/graphql`
  - API Platform auto-generates schema and resolvers based on resource metadata.

## Frontend

Repository: https://github.com/ashilkov/symfony-blog-frontend
The React (Vite + TS) frontend lives in a separate repository and consumes:
- REST endpoints for Auth
- GraphQL endpoint for all other operations

Configure the frontend `.env` with:
- VITE_API_BASE_URL (for REST)
- VITE_GRAPHQL_ENDPOINT (for GraphQL, e.g., http://localhost:8000/api/graphql)

## Useful Developer Notes

- Serialization groups are used to control read/write fields on resources.
- GraphQL queries are secured; some collections may be filtered to the current user when relevant.
- Migrations are the single source of truth for schema changes; never edit tables manually.
- Use DTOs and custom data providers as needed when crossing context boundaries.

## Roadmap

- Auth flows (login, refresh tokens, registration)
- Blog/user roles and permissions
- Post reactions and comments
- Subscriptions and personalized feeds
- Production Docker images and CI/CD

## Contributing

Issues and PRs are welcome. Please keep changes scoped to a bounded context and include tests where appropriate.

## License

MIT
