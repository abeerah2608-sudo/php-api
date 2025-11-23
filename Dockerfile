FROM php:8.2-apache

# Change Apache to listen on port 8080 for Railway
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:8080/g' /etc/apache2/sites-available/000-default.conf

# Install mysqli
RUN docker-php-ext-install mysqli

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy project files
COPY public/ /var/www/html/

# Create uploads folder
RUN mkdir -p /var/www/html/uploads && chmod -R 777 /var/www/html/uploads

EXPOSE 8080

CMD ["apache2-foreground"]
