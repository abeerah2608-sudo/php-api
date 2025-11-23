FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Copy all files including public/ content
COPY public/ /var/www/html/

# Enable mod_rewrite
RUN a2enmod rewrite

# Ensure uploads folder exists
RUN mkdir -p /var/www/html/uploads && chmod -R 777 /var/www/html/uploads

EXPOSE 80
CMD ["apache2-foreground"]
