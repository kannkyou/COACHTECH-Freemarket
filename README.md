# COACHTECHフリマ(フリーマーケットアプリ)

## 環境構築
**Dockerビルド**
1. `git clone git@github.com:kannkyou/COACHTECH-Freemarket.git`
2. DockerDesktopアプリを立ち上げる
3. `docker compose up -d --build`

**Laravel環境構築**
1. `docker compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
4. Stripeの決済サンドボックスを使うためにStripeのアカウントを作成・サインイン。ダッシュボードからAPIキーを取得し、
<br>ダッシュボードからAPIキーを取得し、envファイルの「pk_test_51xxxxx」と「sk_test_51xxxxx」を置き換える
5. アプリケーションキーの作成
``` bash
php artisan key:generate
```

6. マイグレーションとシーディングの実行、ストレージのリンク
``` bash
php artisan migrate:fresh --seed
php artisan storage:link
```

## 使用技術(実行環境)
- PHP8.3.0
- Laravel8.83.27
- MySQL8.0.26

## ER図
<img width="891" height="1051" alt="Freemarket drawio" src="https://github.com/user-attachments/assets/eb2876e4-db6a-4e15-bc7a-1570d6776ce2" />

## URL
- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/


## 注意事項
- 会員登録のメール認証の際は、http://localhost:8025/ に認証メールが届くためそちらを参照すること。
- Stripe決済でカードを利用する際は公式に指定されているダミーナンバー （https://docs.stripe.com/testing#cards） を参照すること。
