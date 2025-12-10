# Laravel Backend Exam

A Laravel application running with Docker Sail, MySQL 8, and phpMyAdmin.

## Services

| Service | URL | Port |
|---------|-----|------|
| Laravel Application | http://localhost | 80 |
| phpMyAdmin | http://localhost:8080 | 8080 |
| MySQL 8 | localhost:3306 | 3306 |

## Getting Started

### Prerequisites

- Docker Desktop installed and running

### Installation

1. **Start Docker Desktop**

2. **Build and start the containers:**
   ```bash
   docker compose up -d --build
   ```

3. **Install Composer dependencies (first time only):**
   ```bash
   docker compose exec laravel.test composer install
   ```

4. **Generate application key (first time only):**
   ```bash
   docker compose exec laravel.test php artisan key:generate
   ```

5. **Run migrations:**
   ```bash
   docker compose exec laravel.test php artisan migrate
   ```

## Accessing the Application

- **Laravel App**: http://localhost
- **phpMyAdmin**: http://localhost:8080
  - Server: mysql
  - Username: sail
  - Password: password

## Database Credentials

- **Host**: mysql (from inside containers) or localhost (from host)
- **Port**: 3306
- **Database**: laravel
- **Username**: sail
- **Password**: password

## Useful Commands

```bash
# Start containers
docker compose up -d

# Stop containers
docker compose down

# View logs
docker compose logs -f

# Access Laravel container shell
docker compose exec laravel.test bash

# Run Artisan commands
docker compose exec laravel.test php artisan <command>

# Run Composer commands
docker compose exec laravel.test composer <command>

# Run tests
docker compose exec laravel.test php artisan test
```

## Container Management

You can view and manage your containers in Docker Desktop after running `docker compose up -d`.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

