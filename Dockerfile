FROM php:8.2-apache

WORKDIR /var/www/html

COPY . /var/www/html

RUN chmod -R 755 /var/www/html
RUN chmod +x swisseph/swetest

RUN docker-php-ext-install mysqli

# Fix Apache MPM conflict
RUN a2dismod mpm_event || true
RUN a2dismod mpm_worker || true
RUN a2enmod mpm_prefork

RUN a2enmod rewrite

# Change port 80 → 8080 for Railway
RUN sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:8080/g' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080