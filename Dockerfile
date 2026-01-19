FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install PHP extensions needed for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy site files
COPY jawad-website/ /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html
