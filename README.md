# University Examination System - Docker Fix

Replace Dockerfile, compose.yaml and docker/entrypoint.sh in the Laravel project.

The Docker build intentionally does not run `php artisan optimize:clear` because
Laravel's `view:clear` fails when the application's configured view path does not
exist. The build creates `resources/views` and only runs the required Wayfinder
generator directly before `npm run build`.

Start:

    docker compose up -d --build

Check:

    docker compose ps
    docker compose logs -f app

Open http://localhost:8000
