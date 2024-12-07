# Gunakan image PHP dengan Nginx dan Composer
FROM php:8.1-fpm

# Install dependensi yang dibutuhkan
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory di dalam container
WORKDIR /var/www

# Salin file aplikasi ke dalam container
COPY . .

# Install dependencies Laravel
RUN composer install --optimize-autoloader --no-dev

# Set hak akses
RUN chown -R www-data:www-data /var/www && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose port 9000
EXPOSE 9000

# Jalankan PHP-FPM
CMD ["php-fpm"]
