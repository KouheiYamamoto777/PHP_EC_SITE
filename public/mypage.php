<?php

session_start();
require_once '../classes/UserLogic.php';
require_once '../classes/Display.php';
require_once '../classes/CartLogic.php';
require_once '../functions.php';

// ログインしているか判定する
$result = UserLogic::checkLogin();

if(!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してからログインしてください';
    header('Location: ./signup.php');
    return;
}

$login_user = $_SESSION['login_user'];

// $_POST情報と$login_userを引数にとるメソッドが多いので、$arg配列に代入
$arg = array();
$arg = [$_POST, $login_user];


$user_data = new UserDisplay();
// 商品を一覧で表示する処理
$item_result = $user_data->display();

// `カートに追加`ボタンが押されたら
if(isset($_POST['add_cart'])) {
    // カートに商品が存在するか調べる
    if(AddCart::search_cart(...$arg)) {
        // 商品が存在したら、
        // 個数を1追加する処理
        AddCart::add_stock(...$arg);
    } else {
        // 存在しなかったら、
        // 商品情報をテーブルに登録する処理
        AddCart::add_cart(...$arg);  
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" integrity="sha384-HzLeBuhoNPvSl5KYnjx0BT+WB0QEEqLprO+NBkkk5gbc67FTaL7XIGa2w1L0Xbgc" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/mypage.css">
    <title>マイページ</title>
</head>
<body>
    <header>
            <h1>最終提出課題&nbsp;:&nbsp;ECサイト</h1>
    </header>

    <div class="login_user">
        <span><i class="fas fa-user"></i>&nbsp;:&nbsp;<?= h($login_user['name']) ?></span></br>
        <span><i class="far fa-envelope"></i>&nbsp;:&nbsp;<?= h($login_user['email']) ?></span></br>
        <form action="./logout.php" method="post">
            <input type="submit" name="logout" class="logout" value="ログアウト">
        </form>
    </div>

    
    <div class="cart">
        <a href="./cart.php"><i class="fas fa-shopping-cart icon"></i></a>
    </div>

    <article>
        <div class="container">
            <?php
            foreach($item_result as $item):
            ?>
            <div class="item">
                <img class="image" src="../uploads/<?= h($item['image']) ?>" alt="<?= h($item['name']) ?>">
                <div class="image_title"><span><?= h($item['name']) ?></span></div>
                <div class="price"><span><?= number_format(h((string)$item['price'])) ?>円</span></div>
                <div class="form">
                    <form action="" method="post">
                        <input type="hidden" name="item_id" value="<?= h($item['item_id']) ?>">
                        <input type="hidden" name="add_name" value="<?= h($item['name']) ?>">
                        <input type="hidden" name="add_price" value="<?= h((string)$item['price']) ?>">
                        <input type="hidden" name="item_image" value="<?= h($item['image']) ?>">
                        <input type="hidden" name="item_stock" value="<?= h($item['stock']) ?>">
                        <input type="hidden" name="add_qty" value="1">
                        <?php
                        if($item['stock'] === 0) {
                            echo '<span class="non_stock">在庫切れ</span>';
                        } else {
                            echo '<input type="submit" name="add_cart" value="カートに追加">';
                        }
                        ?>
                    </form>
                </div>
            </div>
            <?php
            endforeach;
            ?>
        </div>
    </article>
</body>
</html>