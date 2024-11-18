FROM php:8.1-apache

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Update apt and install necessary packages
RUN apt-get update && apt-get install -y \
    libcurl4 libcurl4-openssl-dev libzip-dev libpq-dev libpng-dev \
    libfreetype6-dev libjpeg62-turbo-dev libxml2-dev git unzip wget\
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN wget https://getcomposer.org/installer -O /tmp/composer-setup.php \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm /tmp/composer-setup.php

# Verify Composer installation
RUN ls -la /usr/local/bin/composer
RUN /usr/local/bin/composer --version

# Ensure /usr/local/bin is in PATH
ENV PATH="/usr/local/bin:$PATH"

# Install PHP extensions
RUN docker-php-ext-install pcntl opcache soap zip pdo_pgsql pdo_mysql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Update PECL channel and install Redis extension
RUN pecl channel-update pecl.php.net \
    && pecl install redis-5.3.4 \
    && docker-php-ext-enable redis

# Copy custom php.ini configuration
COPY ./docker/php.ini /usr/local/etc/php/conf.d/

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy Apache site configuration
COPY ./docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy the startup script and make it executable
# COPY startup.sh /usr/local/bin/startup.sh
# Make the startup script executable
# RUN chmod +x /usr/local/bin/startup.sh

# Install composer dependencies without dev packages
RUN composer install --optimize-autoloader --no-dev

# Dump autoload for composer
RUN composer dump-autoload

# Set permissions for storage folder
RUN chown -R www-data:www-data storage && chmod -R 755 storage

# Expose port 80
EXPOSE 80

# Set the default command to run the Apache server
# CMD ["sh", "/usr/local/bin/startup.sh"]
