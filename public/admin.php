<?php

session_start();
require_once '../classes/AdminLogic.php';
require_once '../classes/FormValidate.php';
require_once '../classes/DbInsert.php';
require_once '../classes/Display.php';
require_once '../classes/ItemCustom.php';
require_once '../functions.php';

// 管理者ログインチェック
$result = AdminLogic::check_ad_Login();

if(!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してからログインしてください';
    header('Location: ./signup.php');
    return;
}

$admin_data = $_SESSION['login_admin'];


// 追加商品をバリデーションしてから、データベースへ商品を登録する
if(isset($_POST['add_item'])) {
    $_SESSION['suc'] = null;
    $validate_result = FormValidate::validate_items($_POST, $_FILES);
    $db_insert = new DbInsert($validate_result);
    // 追加する商品のIDが存在するか調べる
    if($db_insert->ckeck_item_id()) {
        $_SESSION['err'] = '入力された商品IDは使用されているため、登録できません';
    } else {
        // IDが存在していなかったら、商品を登録する
        if($db_insert->register_items()) {
            $_SESSION['suc'] = '商品の登録が完了しました';
        } else {
            $_SESSION['err']['register_false'] = '商品の登録に失敗しました';
        }
    }
}

// 商品情報変更処理
if(isset($_POST['modify'])) {
    $modi_items = FormValidate::modify_items();
    $modi_result = new ItemCustom($modi_items);
    if($modi_result->change_item_data()) {
        $_SESSION['suc'] = '商品情報の変更が完了しました';
    }
}

// 商品削除処理
if(isset($_POST['delete'])) {
    $deli_items = new ItemCustom($_POST);
    if($deli_items->delete_item_data($_POST)) {
        $_SESSION['suc'] = '商品の削除が完了しました';
    }
}


?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" integrity="sha384-HzLeBuhoNPvSl5KYnjx0BT+WB0QEEqLprO+NBkkk5gbc67FTaL7XIGa2w1L0Xbgc" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/admin.css">
    <title>管理者ページ</title>
</head>
<body>
    <h2>管理者ページ</h2>

    <div class="login_user">
        <span><i class="fas fa-user"></i> : <?= h($admin_data['admin_id']) ?></span></br>
        <form action="./logout.php" method="post">
            <input type="submit" name="logout" class="logout" value="ログアウト">
        </form>
    </div>

    <ul>
        <?php
            // エラーメッセージを表示
            if (!empty($_SESSION['err']) && is_array($_SESSION['err'])) {
                $_SESSION['suc'] = null;
                foreach($_SESSION['err'] as $e) {
                    echo '<li>' . $e . '</li>';
                }
            } else if (!empty($_SESSION['err']) && !is_array($_SESSION['err'])) {
                $_SESSION['suc'] = null;
                echo '<li>' . $_SESSION['err'] . '</li>';
            }
            // サクセスメッセージを表示
            if(!empty($_SESSION['suc'])) {
                echo '<li>' . $_SESSION['suc'] . '</li>';
            }
        ?>
    </ul>

    <fieldset class="add_form">
        <legend>商品を追加</legend>
        <form action="admin.php" method="post" enctype="multipart/form-data">
            <label>商品名&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<input type="text" name="item_name" placeholder="8文字以内"></label></br>
            <label>商品ID&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<input type="text" name="item_id" placeholder="x-0000"></label></br>
            <label>在庫数&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<input type="text" name="item_qty"></label></br>
            <label>商品価格&nbsp;:&nbsp;<input type="text" name="item_price"></label></br>
            <label>商品画像&nbsp;:&nbsp;<input type="file" name="item_image"></label></br>
            <span>カテゴリ&nbsp;:&nbsp; 
            <select name="category">
                <option value="furniture">家具</option>
                <option value="goods">雑貨</option>
                <option value="clothes">衣服</option>
            </select>
            </span></br>
            <span>公開ステータス&nbsp;:&nbsp;
            <select name="status">
                <option value="1">非公開</option>
                <option value="0">公開</option>
            </select>
            </span></br>
            <p><input type="submit" name="add_item" value="-商品を追加-"></p>
        </form>
    </fieldset>

    <fieldset>
        <legend>商品管理</legend>
            <table border="1">
                <tr>
                    <th>商品名</th>
                    <th>商品ID</th>
                    <th>商品画像</th>
                    <th>商品価格</th>
                    <th>商品在庫</th>
                    <th>公開ステータス</th>
                    <th>変更する</th>
                    <th>削除する</th>
                </tr>
                <?php
                // 商品情報表示処理
                $db_select = new AdminDisplay();
                $result_s = $db_select->display();
                foreach($result_s as $item):
                ?>
                <tr align="center">
                    <form action="admin.php" method="post">
                        <td><?= h($item['name']) ?></td>
                        <td><?= h($item['item_id']) ?></td>
                        <td><img src="../uploads/<?= h($item['image']) ?>" alt="<?= h($item['name']) ?>"></td>
                        <td><input type="text" name="change_price" value="<?= h($item['price']) ?>"></td>
                        <td><input type="text" name="change_stock" value="<?= h($item['stock']) ?>"></td>
                        <td><input type="text" name="change_status" value="<?php if($item['done'] === 0) {
                            echo '公開';} else if ($item['done'] === 1){ echo '非公開'; } ?>"></td>
                        <input type="hidden" name="item_id" value="<?= h($item['item_id']) ?>">
                        <td><input type="submit" name="modify" value="-変更する-"></td>
                    </form>
                    <td>
                        <form action="admin.php" method="post">
                            <input type="hidden" name="delete_id" value="<?= h($item['item_id']) ?>">
                            <input type="submit" name="delete" value="-削除する-">
                        </form>
                    </td>
                </tr>
                <?php
                endforeach;
                ?>
            </table>
    </fieldset>
    
    <a href="./show_user.php">ユーザー管理ページに移動</a></br>
    <a href="./login_form.php">ログイン画面へ戻る</a>
</body>
</html>