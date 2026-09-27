#!/bin/sh
set -eu

cd /var/www/html

mkdir -p \
    resources/views \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chmod -R 775 storage bootstrap/cache || true

# Never use Vite development server in Docker
rm -f /var/www/html/public/hot
rm -f /var/www/html/storage/vite.hot

# ---------------------------------------------------------
# Create .env from .env.example if it doesn't exist
# ---------------------------------------------------------
if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

# ---------------------------------------------------------
# Generate APP_KEY if missing
# ---------------------------------------------------------
if [ -z "${APP_KEY:-}" ]; then
    php artisan key:generate --force --no-interaction >/dev/null
    APP_KEY="$(grep '^APP_KEY=' .env | cut -d '=' -f2-)"
    export APP_KEY
fi

echo "Waiting for PostgreSQL..."

until php -r '
$host = getenv("DB_HOST") ?: "postgres";
$port = (int) (getenv("DB_PORT") ?: 5432);

$fp = @fsockopen($host, $port, $errno, $errstr, 2);

if ($fp) {
    fclose($fp);
    exit(0);
}

exit(1);
'; do
    sleep 2
done

echo "PostgreSQL is ready."

php artisan migrate --force
php artisan optimize:clear

if [ "$#" -gt 0 ]; then
    exec "$@"
fi

exec php artisan serve --host=0.0.0.0 --port=8000