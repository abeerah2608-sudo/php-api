# Use official PHP image with Apache
FROM php:8.2-apache

# Copy all files to Apache's root
COPY . /var/www/html/

# Set public folder as Apache DocumentRoot
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Enable mod_rewrite (optional, but useful)
RUN a2enmod rewrite

# Make sure uploads folder exists and is writable
RUN mkdir -p /var/www/html/public/uploads && chmod -R 777 /var/www/html/public/uploads

# Expose port
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
