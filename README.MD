# Blog Platform API

Modern backend for a multi-user blogging platform built with Symfony and API Platform.

## Features

- Multi-user blogs with role-based permissions
- GraphQL API for efficient data fetching
- JWT authentication with refresh tokens
- Real-time subscriptions and feeds
- Clean Domain-Driven Design architecture

## Quick Start

```bash
# Start services
docker compose up -d

# Install dependencies
docker compose exec php composer install

# Run migrations
docker compose exec php bin/console doctrine:migrations:migrate -n
```

**API Endpoints:**
- REST Documentation: http://localhost:8000/api/docs
- GraphQL Playground: http://localhost:8000/api/graphql

## Technology Stack

- Symfony 7 + API Platform
- PostgreSQL + Doctrine ORM
- JWT Authentication
- GraphQL & REST Hybrid API

## Frontend

The React frontend is available at:  
https://github.com/ashilkov/symfony-blog-frontend

## Development

The system is built using Domain-Driven Design with two bounded contexts:
- **Blog**: Posts, comments, subscriptions
- **User**: Authentication and profiles

All database changes are managed through Doctrine migrations.

## License

MIT
