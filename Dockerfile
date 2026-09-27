# syntax=docker/dockerfile:1.7

FROM php:8.4-cli-bookworm AS base

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    ca-certificates \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql \
        pcntl \
        intl \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# The Laravel React starter + Wayfinder need PHP and Node in the
# same build stage because the frontend build may invoke Artisan.
COPY --from=node:24-bookworm-slim /usr/local /usr/local
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


FROM base AS build

WORKDIR /var/www/html

# Safe build-time environment. Runtime values are injected by Compose.
ENV APP_ENV=local \
    APP_DEBUG=false \
    APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/tmp/wayfinder.sqlite \
    CACHE_STORE=array \
    SESSION_DRIVER=array \
    QUEUE_CONNECTION=sync \
    LOG_CHANNEL=stderr \
    DOCKER_BUILD=true

RUN touch /tmp/wayfinder.sqlite

# Install PHP dependencies without running Laravel package scripts before
# artisan/application files have been copied.
COPY composer.json composer.lock* ./
RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-dev \
    --no-scripts

# Install JS dependencies.
COPY package.json package-lock.json* ./
RUN npm ci

# Copy the full Laravel application.
COPY . .

# Ensure Laravel's standard writable directories exist even when the
# application is Inertia/React-only and has no traditional Blade views.
RUN mkdir -p \
    resources/views \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Now artisan exists, so package discovery/autoload generation is safe.
RUN composer dump-autoload --optimize --no-dev

# Generate Wayfinder once during the PHP build stage. The Vite plugin is
# disabled during Docker build via DOCKER_BUILD=true, so Vite will not try
# to spawn another Artisan process.
RUN php artisan wayfinder:generate --with-form --no-interaction

# Build React/Vite assets.
RUN npm run build


FROM base AS app

WORKDIR /var/www/html

COPY --from=build /var/www/html /var/www/html

RUN mkdir -p \
    resources/views \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 8000

ENTRYPOINT ["entrypoint"]
