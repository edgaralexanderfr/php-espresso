FROM php:8.1.18-zts

WORKDIR /app
COPY . .
CMD ["php", "test/docker_server.php"]