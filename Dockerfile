# Use the official PHP image with CLI and web server support
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory inside the container
WORKDIR /app

# Copy project files to the working directory
COPY . .

# Install PHP dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

# Expose Render's default port
EXPOSE 8080

# Start the built-in PHP server
CMD ["php", "-S", "0.0.0.0:8080", "login.php"]

# Copy the .env file into the container
COPY database/.env /app/config/.env

