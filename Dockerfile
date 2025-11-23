FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Copy all PHP files (everything is inside public/)
COPY public/ /var/www/html/

# Enable mod_rewrite
RUN a2enmod rewrite

# Ensure uploads folder exists
RUN mkdir -p /var/www/html/uploads && chmod -R 777 /var/www/html/uploads

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
