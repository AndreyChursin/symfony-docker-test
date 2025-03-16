FROM php:8.2-fpm

# Устанавливаем необходимые пакеты
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git curl \
    && docker-php-ext-install pdo_mysql zip

# Устанавливаем PCOV
RUN pecl install pcov \
    && docker-php-ext-enable pcov

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Создаём рабочую директорию
WORKDIR /var/www/html

# Создаём новый проект Symfony (можно поменять `symfony/skeleton` на `symfony/website-skeleton`)
RUN composer create-project symfony/skeleton . --prefer-dist

# Устанавливаем права на кеш и логи (важно для работы контейнера)
RUN chown -R www-data:www-data /var/www/html/var

CMD ["php-fpm"]
