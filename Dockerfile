FROM php:8.2-cli

WORKDIR /app

COPY . .

RUN chmod +x swisseph/swetest

ENV PORT=8080

EXPOSE 8080

CMD ["sh","-c","php -S 0.0.0.0:$PORT"]