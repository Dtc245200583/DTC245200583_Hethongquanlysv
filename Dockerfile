# Image ứng dụng Laravel chạy PHP-FPM (không chạy bằng root).
FROM php:8.3-fpm-alpine3.20

RUN apk add --no-cache unzip \
    && docker-php-ext-install pdo_mysql bcmath opcache

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Cài thư viện trước để tận dụng cache của Docker khi chỉ sửa mã nguồn.
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-autoloader

COPY . .

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views \
        storage/logs bootstrap/cache \
    && composer dump-autoload --optimize --no-dev \
    && rm /usr/bin/composer \
    && sed -i 's/\r$//' docker/app/entrypoint.sh \
    && install -m 0755 docker/app/entrypoint.sh /usr/local/bin/entrypoint.sh \
    && cp docker/php/zz-app.ini /usr/local/etc/php/conf.d/zz-app.ini \
    && chown -R www-data:www-data storage bootstrap/cache

USER www-data

EXPOSE 9000

HEALTHCHECK --interval=15s --timeout=5s --start-period=30s --retries=5 \
    CMD php -r 'exit(@fsockopen("127.0.0.1", 9000) ? 0 : 1);'

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
