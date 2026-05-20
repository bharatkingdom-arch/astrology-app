FROM php:8.2-cli

WORKDIR /app

COPY . .

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && chmod +x swisseph/swetest

ENV PORT=8080

EXPOSE 8080

CMD php -S 0.0.0.0:$PORT