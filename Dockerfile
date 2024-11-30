
# Use the official PHP image with Apache support
FROM php:8.2-apache

# Install system dependencies and PHP extensions (needed for your project)
# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
@@ -14,20 +14,23 @@
# Enable mod_rewrite for Apache (for URL routing if needed)
RUN a2enmod rewrite

# Install Composer (dependency manager for PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Set the working directory to /var/www/html (Apache's default web directory)
# Set the working directory to Apache's default web directory
WORKDIR /var/www/html

# Copy all project files into the container’s /var/www/html directory
COPY . .

# Install Composer (dependency manager for PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Install PHP dependencies via Composer (if any)
RUN composer install --no-dev --optimize-autoloader

# Set the DirectoryIndex to login.php to handle root requests
RUN echo 'DirectoryIndex login.php index.php index.html' >> /etc/apache2/apache2.conf
# Expose the container’s port 80 (Apache default port)
EXPOSE 80

# Start Apache server when the container is run
CMD ["apache2-foreground"]