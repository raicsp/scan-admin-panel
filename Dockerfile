# Use the official PHP image with Apache support
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxpm-dev \
    libicu-dev \
    libxml2-dev \
    && apt-get clean

# Debug: Verify if the libraries are installed (Check if jpeg and png directories exist)
RUN ls -l /usr/include/freetype2 /usr/include/jpeg /usr/include/png

# Configure GD extension with FreeType and JPEG support
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/freetype2 --with-jpeg-dir=/usr/include/jpeg

# Install required PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip gd mysqli

# Enable the GD extension
RUN docker-php-ext-enable gd

# Enable mod_rewrite for Apache (for URL routing if needed)
RUN a2enmod rewrite

# Set the working directory to Apache's default web directory
WORKDIR /var/www/html

# Copy all project files into the container’s /var/www/html directory
COPY . ./

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
