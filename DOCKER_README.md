# UtaMemo Docker環境

このディレクトリには、UtaMemoアプリケーションをDockerで実行するための設定ファイルが含まれています。

## 📁 ファイル構成

- `Dockerfile` - Laravel + Vue.jsアプリケーション用のDockerイメージ定義
- `docker-compose.yml` - MySQL、Redis、アプリケーションの統合環境設定
- `docker/nginx.conf` - Nginx Webサーバーの設定
- `docker/start.sh` - アプリケーション起動スクリプト
- `.env.example` - 環境変数のサンプルファイル
- `.dockerignore` - Dockerイメージに含めないファイルの指定

## 🚀 使用方法

### 1. 環境の準備

```bash
# 環境変数ファイルをコピー
cp .env.example .env

# 必要に応じて.envファイルを編集
# データベース設定などはdocker-compose.ymlの設定に合わせてください
```

### 2. Docker環境の起動

```bash
# すべてのサービスを起動
docker-compose up -d

# ログを確認
docker-compose logs -f

# 特定のサービスのログを確認
docker-compose logs -f app
```

### 3. アプリケーションへのアクセス

- **Webアプリケーション**: http://localhost:8000
- **MySQL**: localhost:3306
- **Redis**: localhost:6379

### 4. よく使用するコマンド

```bash
# コンテナの停止
docker-compose down

# コンテナの再起動
docker-compose restart

# 特定のサービスの再起動
docker-compose restart app

# コンテナ内でコマンドを実行
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker

# データベースに接続
docker-compose exec mysql mysql -u utamemo_user -p utamemo

# ログの確認
docker-compose logs app
docker-compose logs mysql
```

### 5. 開発時の注意点

- コードの変更は自動的に反映されます（ボリュームマウント）
- データベースのデータは永続化されます
- 初回起動時は自動的にマイグレーションが実行されます

## 🔧 トラブルシューティング

### ポートが既に使用されている場合

```bash
# 使用中のポートを確認
lsof -i :8000
lsof -i :3306

# 別のポートを使用する場合はdocker-compose.ymlを編集
```

### データベース接続エラーの場合

```bash
# MySQLコンテナの状態を確認
docker-compose ps mysql

# MySQLコンテナのログを確認
docker-compose logs mysql

# アプリケーションコンテナを再起動
docker-compose restart app
```

### 権限エラーの場合

```bash
# ストレージディレクトリの権限を修正
docker-compose exec app chmod -R 755 storage
docker-compose exec app chmod -R 755 bootstrap/cache
```

## 📝 カスタマイズ

### データベース設定の変更

`docker-compose.yml`の`mysql`サービスで以下の設定を変更できます：

- `MYSQL_ROOT_PASSWORD`: ルートパスワード
- `MYSQL_DATABASE`: データベース名
- `MYSQL_USER`: ユーザー名
- `MYSQL_PASSWORD`: ユーザーパスワード

### ポートの変更

`docker-compose.yml`の`ports`セクションでポート番号を変更できます。

### 環境変数の追加

`.env`ファイルに新しい環境変数を追加し、`docker-compose.yml`の`environment`セクションに追加してください。
