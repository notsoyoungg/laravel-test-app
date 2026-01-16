# =========================
# PHP BASE (CLI)
# =========================
FROM mirror.gcr.io/php:8.4-alpine AS php_cli_base

ARG PHP_EXTS="ctype fileinfo mbstring pcntl"
ARG PHP_PECL_EXTS="redis"

RUN apk add --no-cache \
        ${PHPIZE_DEPS} \
        oniguruma-dev \
        openssl \
        ca-certificates \
    && docker-php-ext-install -j$(nproc) ${PHP_EXTS} \
    && pecl install ${PHP_PECL_EXTS} \
    && docker-php-ext-enable ${PHP_PECL_EXTS} \
    && apk del ${PHPIZE_DEPS}

WORKDIR /laravel-test-app

# =========================
# PHP BASE (FPM)
# =========================
FROM mirror.gcr.io/php:8.4-fpm-alpine AS php_fpm_base

ARG PHP_EXTS="ctype fileinfo mbstring pcntl"
ARG PHP_PECL_EXTS="redis"

RUN apk add --no-cache \
        ${PHPIZE_DEPS} \
        oniguruma-dev \
        openssl \
        ca-certificates \
    && docker-php-ext-install -j$(nproc) ${PHP_EXTS} \
    && pecl install ${PHP_PECL_EXTS} \
    && docker-php-ext-enable ${PHP_PECL_EXTS} \
    && apk del ${PHPIZE_DEPS}

WORKDIR /laravel-test-app

# =========================
# VENDOR BUILD (composer)
# =========================
FROM php_cli_base AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN addgroup -S app && adduser -S app -G app \
 && mkdir -p /laravel-test-app \
 && chown -R app:app /laravel-test-app

WORKDIR /laravel-test-app

USER app

COPY --chown=app:app composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

# =========================
# APP BUILD
# =========================
FROM vendor AS app_build

USER app

COPY --chown=app:app . .

# Генерим автолоадер
RUN composer dump-autoload --optimize

# =========================
# PHP-FPM (runtime)
# =========================
FROM php_fpm_base AS fpm

WORKDIR /laravel-test-app

COPY --from=app_build /laravel-test-app .

# Создаём папку storage/app/public на всякий случай
RUN mkdir -p storage/app/public \
    && chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache public \
    && rm -f public/storage \
    && ln -s ../storage/app/public public/storage

#RUN chown -R www-data:www-data storage bootstrap/cache \
# && chmod -R 775 storage bootstrap/cache

USER www-data

CMD ["php-fpm"]

# =========================
# CLI (artisan, jobs)
# =========================
FROM php_cli_base AS cli

WORKDIR /laravel-test-app

COPY --from=app_build /laravel-test-app .

CMD ["php", "artisan"]

# =========================
# CRON
# =========================
FROM cli AS cron

RUN touch laravel.cron && \
    echo "* * * * * cd /laravel-test-app && php artisan schedule:run" >> laravel.cron && \
    crontab laravel.cron

CMD ["crond", "-f", "-l", "2"]

# =========================
# NGINX
# =========================
FROM mirror.gcr.io/nginx:1.20-alpine AS nginx

WORKDIR /laravel-test-app

COPY docker/nginx/default.conf /etc/nginx/templates/default.conf.template
COPY --from=app_build /laravel-test-app/public ./public

HEALTHCHECK CMD wget -qO- http://localhost || exit 1
