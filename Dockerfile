FROM php:8.2-apache

# Enable Apache Rewrite Module for URL routing
RUN a2enmod rewrite

# CRITICAL: Install PHP extensions needed for database connections
# If you use PostgreSQL instead of MySQL, change this to 'pdo_pgsql'
RUN docker-php-ext-install pdo pdo_mysql mysqli

WORKDIR /var/www/html

# Copy project files into the container
COPY . .

# Expose port 80 for web traffic
EXPOSE 80

CMD ["apache2-foreground"]