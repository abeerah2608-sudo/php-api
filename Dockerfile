FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Copy all files
COPY . /var/www/html/

# Set public folder as DocumentRoot
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Enable mod_rewrite
RUN a2enmod rewrite

# Ensure uploads folder exists
RUN mkdir -p /var/www/html/public/uploads && chmod -R 777 /var/www/html/public/uploads

EXPOSE 80

CMD ["apache2-foreground"]
