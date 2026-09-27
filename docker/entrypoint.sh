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

# ---------------------------------------------------------
# Never use Vite development server in Docker
# ---------------------------------------------------------
rm -f /var/www/html/public/hot
rm -f /var/www/html/storage/vite.hot

# Generate an application key when the runtime environment does not provide one.
# A mounted .env file is updated when available; the value is also exported for
# the current process so Laravel can boot immediately.
if [ -z "${APP_KEY:-}" ]; then
    if [ ! -f .env ] && [ -f .env.example ]; then
        cp .env.example .env
    fi

    if [ -f .env ]; then
        php artisan key:generate --force --no-interaction >/dev/null
        APP_KEY="$(sed -n 's/^APP_KEY=//p' .env | head -n 1)"
        export APP_KEY
    else
        APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
        export APP_KEY
    fi
fi

echo "Waiting for PostgreSQL..."
until php -r '
$host = getenv("DB_HOST") ?: "postgres";
$port = (int) (getenv("DB_PORT") ?: 5432);
$fp = @fsockopen($host, $port, $errno, $errstr, 2);
if ($fp) { fclose($fp); exit(0); }
exit(1);
'; do
    sleep 2
done

echo "PostgreSQL is ready."

php artisan migrate --force

# Execute the Compose command when one is provided. This is important for the
# queue container; otherwise both app and queue containers would start HTTP.
if [ "$#" -gt 0 ]; then
    exec "$@"
fi

exec php artisan serve --host=0.0.0.0 --port=8000
