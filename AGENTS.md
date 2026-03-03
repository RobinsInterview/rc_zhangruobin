# Repository Guidelines

## Project Structure & Module Organization
This repository is a Laravel 11 notification service MVP.

- `app/` core application code (controllers, events, listeners, services, models).
- `app/Services/Notifications/` notification domain logic (event definitions, delivery service, notifiers).
- `app/Notifications/Payloads/` per-event payload validation rules.
- `routes/api.php` public API endpoints.
- `config/` runtime config (`queue.php`, `notification_events.php`).
- `database/migrations/` schema definitions (`notifications`, `notification_attempts`, queue tables).
- `tests/Feature` API behavior tests; `tests/Unit` domain/unit tests.
- `README.md`, `AI_USAGE.md`, `STEPS.md` design context and delivery notes.

## Build, Test, and Development Commands
Use Docker commands (recommended when local PHP is unavailable):

- `docker run --rm -u $(id -u):$(id -g) -v "$PWD":/app -w /app composer:2 composer install`  
  Install PHP dependencies.
- `docker run --rm -u $(id -u):$(id -g) -v "$PWD":/app -w /app composer:2 php artisan migrate`  
  Run database migrations.
- `docker run --rm -u $(id -u):$(id -g) -v "$PWD":/app -w /app composer:2 php artisan test`  
  Run unit + feature tests.
- `docker run -d --name rc-redis -p 6379:6379 redis:7-alpine`  
  Start Redis for queue processing.
- `php artisan queue:work redis --tries=6` and `php artisan serve` (inside container)  
  Run worker and API server.

## Coding Style & Naming Conventions
- Follow PSR-12 and Laravel conventions; 4-space indentation.
- Class names: `PascalCase`; methods/variables: `camelCase`; constants: `UPPER_SNAKE_CASE`.
- Keep event types as lowercase snake case (example: `user_registered_from_ads`).
- Prefer small, single-purpose classes (Controller -> Service -> Notifier).
- Format with `./vendor/bin/pint` before submitting changes.

## Testing Guidelines
- Framework: PHPUnit (`php artisan test`).
- Test files end with `*Test.php`.
- Place HTTP/API tests in `tests/Feature`; pure logic tests in `tests/Unit`.
- For new event types, add at least:
  1) payload validation test,  
  2) API acceptance/422 test,  
  3) idempotency or delivery-state assertion.

## Commit & Pull Request Guidelines
No stable Git history pattern is established yet; use Conventional Commits going forward:
- `feat: add subscription_paid notifier`
- `fix: handle non-retryable 4xx responses`

PRs should include:
- What changed and why (1-2 paragraphs).
- Test evidence (command + result).
- Any config/migration impact.
- README updates when API, architecture, or operational steps change.

## Security & Configuration Tips
- Do not commit secrets; keep credentials in `.env`.
- Keep external target URLs configurable via `NOTIFY_*` env vars.
- `NullAuthenticator` is intentionally permissive for MVP; replace before production use.
