# PHP CLI with every extension the locked dependencies require, plus Composer.
FROM php:8.3.35-cli-alpine3.24@sha256:8dbeb51c352cf1f70d5d50f3642c9fbed311e914475ea2382f5bc1525bef7bb3
COPY --from=composer:2.10.3@sha256:af98f42dfff7c68ba8d53c2164fd9fde1087b7d449514baa38c418b1f6bc4bac /usr/bin/composer /usr/bin/composer

# PCOV, the code coverage driver for make coverage.
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install pcov-1.0.12 \
    && docker-php-ext-enable pcov \
    && apk del .build-deps
