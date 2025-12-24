FROM php:8.2-fpm

ARG PUID=1000
ARG PGID=1000

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    default-mysql-client \
    zlib1g-dev \
    libzip-dev \
    unzip \
    git \
    curl \
  && docker-php-ext-install pdo_mysql zip \
  && rm -rf /var/lib/apt/lists/*

# Composer（公式イメージ推奨の入れ方：composer公式コンテナからコピー）
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN groupadd -g ${PGID} kannk \
  && useradd -m -u ${PUID} -g ${PGID} -s /bin/bash kannk

WORKDIR /var/www