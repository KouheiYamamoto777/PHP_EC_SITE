<?php
session_start();
require_once '../classes/UserLogic.php';
require_once '../classes/AdminLogic.php';

$err = $_SESSION;

$result = UserLogic::checkLogin();
if($result) {
    header('Location: ./mypage.php');
    return;
}

$_SESSION = array();
session_destroy();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
</head>
<body>
    <h2>ログイン画面</h2>
    <?php
        if(isset($err['msg'])) {
            echo '<p>' . $err['msg'] . '</p>';
        }
    ?>

    <fieldset>
        <legend>ログイン</legend>
        <form action="login.php" method="post">
            <label for="login_email">メールアドレス : </label></br>
            <input type="email" name="login_email"></br>
            <?php
            if(isset($err['email'])) {
                echo '<p>' . $err['email'] . '</p>';
            }
            ?>
            
            <label for="login_pass">パスワード : </label></br>
            <input type="password" name="login_pass"></br>
            <?php
            if(isset($err['password'])) {
                echo '<p>' . $err['password'] . '</p>';
            }
            ?>

            <p><input type="submit" value="ログインする"></p>
        </form>
    </fieldset>

    <fieldset>
        <legend>管理者ログイン</legend>
        <form action="ad_login.php" method="post">
            <label for="admin_id">管理者ID : </label></br>
            <input type="text" name="admin_id"></br>
            <?php
            if(isset($err['admin_id'])) {
                echo '<p>' . $err['admin_id'] . '</p>';
            }
            ?>
            <label for="admin_password">管理者パスワード : </label></br>
            <input type="password" name="admin_password"></br>
            <?php
            if(isset($err['ad_password'])) {
                echo '<p>' . $err['ad_password'] . '</p>';
            }
            ?>
            <p><input type="submit" value="管理者ログイン"></p>
        </form>
    </fieldset>

    <a href="./signup.php">新規登録はこちら</a>
</body>
</html>