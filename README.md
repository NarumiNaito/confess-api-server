# 懺悔の館（バックエンド）

<br>
<img width="1423" alt="top" src="https://github.com/user-attachments/assets/7d0d0597-59ed-4f64-af06-4defeaf835e2">
<br>

## 概要

<br>

URL ▶ <a href="https://zangenoyakata.com/" target="_blank" rel="noopener noreferrer">https://zangenoyakata.com/</a>
<br>
<br>

このリポジトリは「懺悔の館」のバックエンド として RESTful API を提供しています。<br>
フレームワークは Laravel を使用して構築されており、データの CRUD 操作(作成、読み込み、更新、削除)を行います。
<br>
また、フロントエンドが SPA(シングルページアプリケーション) での実装の為、認証については Laravel Sanctum による SPA 認証を提供しています。
<br>
<br>

## API の提供

-   ユーザーの作成、取得、編集、削除機能(画像含む)
-   投稿の作成、取得、編集、削除機能
-   コメントの作成、取得、削除機能
-   赦すの登録、解除機能
-   ブックマークの登録、解除機能
-   投稿に対するカテゴリー機能
-   コメント、赦すに対する通知機能
    <br>
    <br>

## 使用技術について

### バックエンド

-   言語:PHP 8.2.26
-   フレームワーク: Laravel 10.10
-   パッケージ管理: Composer 2.8.3
-   データベース: Mysql 8.0.33
-   Web サーバー: Nginx 1.25
-   オブジェクトストレージサーバー:MiniO(S3 擬似環境)
-   API 仕様: RESTful API
    <br>
    <br>

### フロントエンド

※ 注釈<br>
SPA 開発における GitHub のリポジトリについて、本アプリはフロントエンドとバックエンドを別々で作成し開発を行っています。
<br>
フロントエンドのリポジトリについては下記を参照ください。
<br>
confess-front 参照 URL ▶ <a href="https://github.com/NarumiNaito/confess-front" target="_blank" rel="noopener noreferrer">https://github.com/NarumiNaito/confess-front</a>
<br>
<br>

## インフラ

-   Docker/Docker-Compose
-   AWS(Route53, CloudFront, S3, ACM, VPC, ALB, ECS, Fargate, ECR, RDS)

### インフラ構成図

<!-- <img width="638" alt="構成図" src="https://user-images.githubusercontent.com/87213148/193205719-19da2de8-806a-49a3-99fb-69c4c07de5fa.png"> -->

<br>
<br>

## ER 図

<img width="638" alt="ER図" src="https://github.com/user-attachments/assets/9b1f0cca-bab7-4fd3-871b-53dd0e94ca85">
