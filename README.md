# COACHTECHフリマ(フリーマーケットアプリ)

## 環境構築
**Dockerビルド**
1. `git clone git@github.com:kannkyou/COACHTECH-Freemarket.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
4. アプリケーションキーの作成
``` bash
php artisan key:generate
```

5. マイグレーションの実行
``` bash
php artisan migrate
```

6. シーディングの実行
``` bash
php artisan db:seed
```

## 使用技術(実行環境)
- PHP8.3.0
- Laravel8.83.27
- MySQL8.0.26

## ER図
<img width="891" height="1051" alt="FreemarketER drawio" src="https://github.com/user-attachments/assets/e312e2ac-d04b-4523-9889-c1b0a71dad6c" />

## URL
- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/
