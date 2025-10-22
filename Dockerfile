# Laravel + Vue.js アプリケーション用のDockerfile
# マルチステージビルドを使用して最適化

# 第1ステージ: Node.js環境でフロントエンド資産をビルド
FROM node:20-alpine AS frontend-builder

# 作業ディレクトリを設定
WORKDIR /app

# package.jsonとpackage-lock.jsonをコピー（依存関係のキャッシュ最適化）
COPY package*.json ./

# Node.jsの依存関係をインストール（開発依存関係も含める）
RUN npm ci

# フロントエンドのソースコードをコピー
COPY resources/ ./resources/
COPY vite.config.js ./

# フロントエンド資産をビルド
RUN npm run build

# 第2ステージ: PHP環境でLaravelアプリケーションを実行
FROM php:8.2-fpm-alpine

# システムの依存関係をインストール
RUN apk add --no-cache \
    nginx \
    mysql-client \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    icu-dev

# PHP拡張機能をインストール（jsonは標準で含まれているため除外）
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        xml \
        ctype \
        bcmath \
        gd \
        zip \
        intl

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www/html

# Laravelアプリケーションのファイルをコピー
COPY . .

# フロントエンドビルド結果をコピー
COPY --from=frontend-builder /app/public/build ./public/build

# Composerの依存関係をインストール（本番環境用）
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 適切な権限を設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Nginxの設定ファイルをコピー
COPY docker/nginx.conf /etc/nginx/nginx.conf

# ポート80を公開
EXPOSE 80

# 起動コマンドを直接実行
CMD ["sh", "-c", "php artisan key:generate --force && php artisan migrate --force && php artisan storage:link && php-fpm -D && nginx -g 'daemon off;'"]
