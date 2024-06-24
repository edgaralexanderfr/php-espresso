FROM php:8.1.18-zts

RUN apt update && apt install -y --no-install-recommends libffi-dev \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install ffi

WORKDIR /app
COPY . .