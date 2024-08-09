# Use the official PHP image with Apache
FROM php:7.4-apache

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the backend application files to the container
COPY backend/ /var/www/html/backend/

# Copy the frontend files to the container
COPY frontend/ /var/www/html/

# Copy the .htaccess file for URL rewriting
COPY frontend/.htaccess /var/www/html/

# Copy custom Apache configuration
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
