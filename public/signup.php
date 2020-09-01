<?php
session_start();
require_once '../functions.php';
require_once '../classes/UserLogic.php';

// ログインチェック
$result = UserLogic::checkLogin();
if($result) {
    header('Location: ./mypage.php');
    return;
}

$login_err = isset($_SESSION['login_err']) ? $_SESSION['login_err'] : null;
unset($_SESSION['login_err']);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録</title>
</head>
<body>
    <h2>ユーザー登録フォーム</h2>

    <?php
        if(isset($login_err)) {
            echo '<p>' . $login_err . '</p>';
        }
    ?>

    <fieldset>
        <legend>新規登録</legend>
            <form action="register.php" method="post">
                <label for="username">ユーザー名 : </label></br>    
                <input type="text" name="username"></br>

                <label for="email">メールアドレス : </label></br>    
                <input type="email" name="email"></br>

                <label for="password">パスワード : </label></br>    
                <input type="password" name="password"></br>

                <label for="password_conf">確認用パスワード : </label></br>    
                <input type="password" name="password_conf"></br>

                <input type="hidden" name="csrf_token" value="<?= h(set_token()) ?>">

                <p><input type="submit" value="新規登録"></p>
            </form>
    </fieldset>

    <a href="./login_form.php">ログインはこちら</a>
</body>
</html>