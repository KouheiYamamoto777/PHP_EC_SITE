# ECサイト

## `フォルダとファイルの各機能`

### classesファイル(クラス専用フォルダ)
1. AdminLogic.php  
    管理者についての処理まとめ
1. CartLogic.php  
    カート処理まとめ
1. DbInsert.php  
    商品をテーブルに登録する際の処理まとめ
1. Display.php  
    商品を表示する処理まとめ
1. FormValidate.php  
    バリデーションに関する処理まとめ
1. ItemCustom.php  
    商品情報変更に関する処理まとめ
1. ShowUser.php  
    ユーザー一覧を取得する処理まとめ
1. UserLogic.php  
    ユーザー登録やログインに関する処理まとめ
### cssファイル(css専用フォルダ)
1. admin.css  
    管理者ページのcss
1. cart.css  
    カートページのcss
1. mypage.css  
    マイページのcss
### publicファイル(ユーザーが訪れるフォルダ)
1. ad_login.php  
    管理者ログイン成功ページ
1. admin.php  
    管理者作業ページ
1. cart.php  
    カートページ
1. login_form.php  
    ユーザーログインページ
1. login.php  
    ユーザーログイン成功ページ
1. logout.php  
    ログアウトページ
1. mypage.php  
    マイページ
1. register.php  
    ユーザー登録完了ページ
1. result.php  
    商品購入完了ページ
1. show_user.php  
    ユーザー一覧表示ページ
1. signup.php  
    ユーザー新規登録ページ
### uploads(画像ファイル専用フォルダ)
### db_connect.php
1. データベース接続に関する処理
### env.php  
1. 定数宣言
### functions.php  
1. 使い回す関数を定義するページ


## `データベース`
### データベース名：`ec_site`
### テーブル
1. `admin_data`テーブル
```
create table admin_data
(
    admin_id char(5),
    admin_password varchar(255)
)
```
2. `cart`テーブル
```
create table cart
(
    id int(11) primary key auto_increment,
    item_id char(10),
    user_name varchar(32),
    item_name varchar(32),
    qty int(11),
    price int(11),
    image varchar(255)
)
```
3. `item_data`テーブル
```
create table item_data
(
    item_id char(10) primary key,
    name varchar(40),
    category varchar(30),
    price int(11),
    image varchar(255),
    done int(11),
    created_date datetime,
    updated_date datetime
)
```
4. `item_stock`テーブル
```
create table item_stock
(
    id int(11) primary key auto_increment,
    item_id char(10) not null,
    stock int(11) not null,
    created_date datetime,
    updated_date datetime
)
```
5. `users`テーブル
```
create table users
(
    id int(11) primary key auto_increment,
    name varchar(64),
    email varchar(191),
    password varchar(191),
    register_date datetime
)
```