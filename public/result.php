<?php

session_start();
require_once '../classes/UserLogic.php';
require_once '../classes/CartLogic.php';
require_once '../classes/Display.php';
require_once '../functions.php';

// ログインしているか判定する
$result = UserLogic::checkLogin();

if(!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してからログインしてください';
    header('Location: ./signup.php');
    return;
}

$login_user = $_SESSION['login_user'];


// カートの価格を表示する処理
$cart_total_price = CartTotalPrice::cart_total_price($login_user);
// カートの個数を表示する処理
$cart_total_quantity = CartTotalQuantity::cart_total_quantity($login_user);
// カートの公開ステータスの合計を取得
$cart_total_done = CartTotalDone::cart_total_done($login_user);
// カートのレコード数合計を取得
$cart_total_record = CartTotalRecord::cart_total_record($login_user);

$cart = new CartDisplay($login_user);
$cart = $cart->display();
$cart_list = $cart;

if($cart_total_record['total_record'] - $cart_total_done['total_done'] === $cart_total_record['total_record']) {
    $err = null;
} else {
    $err = '商品の購入に失敗しました';
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" integrity="sha384-HzLeBuhoNPvSl5KYnjx0BT+WB0QEEqLprO+NBkkk5gbc67FTaL7XIGa2w1L0Xbgc" crossorigin="anonymous">
    <title>購入完了ページ</title>
    <style>
        body {
            position: relative;
        }
        .login_user {
            position: fixed;
            top: 35px;
            right: 0;
            width: auto;
            height: 80px;
            background: #fff;
            text-align: center;
            padding: 3px;
            border-radius: 15px 0 0 15px;
            border-left: solid 2px tomato;
            border-top: solid 2px tomato;
            border-bottom: solid 2px tomato;
        }
        table tr img {
            width: 150px;
            height: 90px;
        }
        .back_my_page {
            position: fixed;
            line-height: 80px;
            bottom: 35px;
            right: 0;
            width: 200px;
            height: 80px;
            background: #fff;
            text-align: center;
            padding: 3px;
            border-radius: 15px 0 0 15px;
            border-left: solid 2px tomato;
            border-top: solid 2px tomato;
            border-bottom: solid 2px tomato;
        }
        .back_my_page a {
            display: block;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>

    <h1><?= h($login_user['name']) ?>様</h1>
    <?php
    if(!empty($err)):
    ?>
    <h2><?= $err ?></h2>
    <?php
    elseif(empty($err)):
    if(ExistCart::exist_cart($login_user)):
        ReduceStock::reduce_stock($cart);
    ?>
    <h2>ご購入ありがとうございました！</h2>
    <h3>合計<?= h($cart_total_quantity['total_qty']) ?>点&nbsp;:&nbsp;<?= number_format(h($cart_total_price['total_price'])) ?>円</h3>
    <table>
        <tr>
            <th>商品画像</th>
            <th>商品名称</th>
            <th>商品価格</th>
            <th>商品数</th>
        </tr>
        <?php
        foreach($cart_list as $item):
        ?>
        <tr>
            <td class="image"><img src="../uploads/<?= h($item['image']) ?>" alt="<?= h($item['item_name']) ?>"></td>
            <td class="item_name"><?= h($item['item_name']) ?></td>
            <td class="price"><?= (string)number_format(h($item['price'])) ?>円</td>
            <td class="qty"><?= (string)number_format(h($item['qty'])) ?>個</td>
        </tr>
        <?php
        endforeach;
        ?>
    </table>
    <?php
    else:
    ?>
    <h2>引き続きお買い物をお楽しみください</h2>
    <?php
    endif;
    ClearCart::clear_cart($login_user);
    endif;
    ?>

    <div class="login_user">
        <span><i class="fas fa-user"></i>&nbsp;:&nbsp;<?= h($login_user['name']) ?></span></br>
        <span><i class="far fa-envelope"></i>&nbsp;:&nbsp;<?= h($login_user['email']) ?></span></br>
        <form action="./logout.php" method="post">
            <input type="submit" name="logout" class="logout" value="ログアウト">
        </form>
    </div>

    <div class="back_my_page">
        <a href="./mypage.php">マイページに戻る</a>
    </div>
</body>
</html>