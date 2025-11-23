FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy public/ folder contents into /var/www/html/
COPY public/ /var/www/html/

# Ensure uploads folder exists
RUN mkdir -p /var/www/html/uploads && chmod -R 777 /var/www/html/uploads

# Set DocumentRoot to /var/www/html
RUN sed -i 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]
RUN touch /var/log/apache2/error.log && chmod 666 /var/log/apache2/error.log

