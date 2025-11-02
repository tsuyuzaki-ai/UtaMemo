#!/bin/bash

# GitHub Actions用デプロイスクリプト
# 本番サーバー上で実行される

set -e

echo "=== デプロイ開始 ==="

# デプロイ先のディレクトリに移動
DEPLOY_PATH="${DEPLOY_PATH:-/var/www/utamemo}"
cd "$DEPLOY_PATH"

echo "現在のディレクトリ: $(pwd)"

# Gitから最新のコードを取得
echo "Gitから最新のコードを取得中..."
git fetch origin
git reset --hard origin/main

# 依存関係のインストール
echo "PHP依存関係をインストール中..."
composer install --no-dev --optimize-autoloader

echo "Node.js依存関係をインストール中..."
npm ci

# フロントエンドのビルド
echo "フロントエンドをビルド中..."
npm run build

# Laravelの設定
echo "Laravel設定をキャッシュ中..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# マイグレーション実行（必要に応じてコメントアウトを外す）
# echo "データベースマイグレーション実行中..."
# php artisan migrate --force

# Dockerコンテナの再起動（Docker環境の場合）
if [ -f docker-compose.yml ]; then
    echo "Dockerコンテナを再起動中..."
    docker-compose down
    docker-compose up -d --build
fi

# 権限の設定
echo "権限を設定中..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

echo "=== デプロイ完了 ==="

