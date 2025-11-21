FROM php:8.1-apache

# Install PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copy app (assumes index.php and any assets are in app/)
COPY . /var/www/html/

# Ensure correct owner
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
