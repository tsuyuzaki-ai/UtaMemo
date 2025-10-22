#!/bin/bash
# Laravelアプリケーション起動スクリプト

# エラーが発生したら即座に終了
set -e

echo "🚀 UtaMemoアプリケーションを起動しています..."

# 環境変数ファイルが存在しない場合は作成
if [ ! -f .env ]; then
    echo "📝 .envファイルを作成しています..."
    cp .env.example .env
fi

# Laravelアプリケーションキーを生成
echo "🔑 アプリケーションキーを生成しています..."
php artisan key:generate --force

# データベースマイグレーションを実行
echo "🗄️ データベースマイグレーションを実行しています..."
php artisan migrate --force

# キャッシュをクリア
echo "🧹 キャッシュをクリアしています..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# ストレージリンクを作成
echo "🔗 ストレージリンクを作成しています..."
php artisan storage:link

# 権限を設定
echo "🔐 ファイル権限を設定しています..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

# PHP-FPMをバックグラウンドで起動
echo "🐘 PHP-FPMを起動しています..."
php-fpm -D

# Nginxを起動
echo "🌐 Nginxを起動しています..."
nginx -g "daemon off;"
