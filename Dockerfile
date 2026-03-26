FROM php:8.2-fpm

# Install mysqli
RUN docker-php-ext-install mysqli

# Install nginx
RUN apt-get update && apt-get install -y nginx

# Copy app
COPY ./html /var/www/html

# Copy nginx config
COPY ./default.conf /etc/nginx/sites-available/default

EXPOSE 80

CMD service nginx start && php-fpm
