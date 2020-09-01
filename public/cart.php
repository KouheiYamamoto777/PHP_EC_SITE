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

// カートのアイテムを削除する処理
if(isset($_POST['delete_item'])) {
    DeleteCartItem::delete_cart_item($_POST);
}

// アイテムの個数を変更する処理
if(isset($_POST['change'])) {
    ChengeQuantity::change_quantity($_POST);
}

// カートの合計価格を取得
$cart_total_price = CartTotalPrice::cart_total_price($login_user);

// カートの合計個数を取得
$cart_total_quantity = CartTotalQuantity::cart_total_quantity($login_user);

// カートの合計在庫を取得
$cart_total_stock = CartTotalStock::cart_total_stock($login_user);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カート</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" integrity="sha384-HzLeBuhoNPvSl5KYnjx0BT+WB0QEEqLprO+NBkkk5gbc67FTaL7XIGa2w1L0Xbgc" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/cart.css">
    <style>
        
    </style>
</head>
<body>
    <h1>カート</h1>

    <div class="login_user">
        <span><i class="fas fa-user"></i>&nbsp;:&nbsp;<?= h($login_user['name']) ?></span></br>
        <span><i class="far fa-envelope"></i>&nbsp;:&nbsp;<?= h($login_user['email']) ?></span></br>
        <form action="./logout.php" method="post">
            <input type="submit" name="logout" class="logout" value="ログアウト">
        </form>
    </div>

    <div class="container">
        <?php
        // カートを表示
        $cart = new CartDisplay($login_user);
        $cart_list = $cart->display();
        foreach($cart_list as $item):
        ?>
        <div class="item">
            <div class="img"><img src="../uploads/<?= h($item['image']) ?>" alt="<?= h($item['item_name']) ?>"></div>
            <div class="info">
                <div class="title">
                    <?= h($item['item_name']) ?>
                    <span class="qty">(
                    <?php if($item['done'] === 1) {
                        echo '只今この商品は購入できません';
                    } else {
                     echo h($item['qty']);
                    }
                    ?>
                    )</span>
                </div>
                <div class="form">
                    <table>
                        <tr>
                            <form action="./cart.php" method="post">
                                <td class="qty">個数&nbsp;:&nbsp;
                                <select name="change_qty">
                                    <?php
                                    for($j = 1; $j <= $item['stock']; $j++):
                                    ?>
                                    <option value="<?= $j ?>"><?= $j ?></option>
                                    <?php
                                    endfor;
                                    ?>
                                </select>
                                </td>
                                <input type="hidden" name="user_name" value="<?= h($item['user_name']) ?>">
                                <input type="hidden" name="item_id" value="<?= h($item['item_id']) ?>">
                                <td><input type="submit" name="change" value="変更する"></td>
                            </form>
                        </tr>
                    </table>
                </div>
                <form action="./cart.php" method="post">
                    <div class="price"><?= number_format(h($item['price'])) ?>円</div>
                    <input type="hidden" name="delete_name" value="<?= h($item['user_name']) ?>">
                    <input type="hidden" name="delete_id" value="<?= h($item['item_id']) ?>">
                    <div class="delete"><input type="submit" name="delete_item" value="削除する"></div>
                </form>
            </div>
        </div>
        <?php
        endforeach;
        ?>
    </div>

    <div class="total">
        <?php
        // カートが空か判定する
        // 空だったら、購入ボタンを表示しない
        // さらに、カートの個数合計が、商品の在庫合計を超過していたら、
        // 購入ボタンを表示しない
        if(ExistCart::exist_cart($login_user)) :
            if($cart_total_quantity['total_qty'] > $cart_total_stock['total_stock']) :
        ?>
        <h3>在庫を超過している</br>商品があります</h3>
        <?php
            elseif($cart_total_quantity['total_qty'] <= $cart_total_stock['total_stock']):
        ?>
        <h2>合計<?= h($cart_total_quantity['total_qty']) ?>点&nbsp;:&nbsp;<?= number_format(h($cart_total_price['total_price'])) ?>円</h2>
        <form action="./result.php" method="post">
            <input type="submit" value="購入する">
        </form>
        <?php
            endif;
        else:
        ?>
        <h2>カートは空です</h2>
        <?php
        endif;
        ?>
    </div>

    <div class="back_my_page">
        <a href="./mypage.php">マイページへ戻る</a>
    </div>
</body>
</html>