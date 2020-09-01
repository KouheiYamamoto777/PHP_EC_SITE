<?php

session_start();
require_once('../classes/UserLogic.php');

$err = [];

// 入力情報バリデーション
if(!$email = filter_input(INPUT_POST, 'login_email')) {
    $err['email'] = 'メールアドレスが入力されていません';
}

if(!$password = filter_input(INPUT_POST, 'login_pass')) {
    $err['password'] = 'パスワードが入力されていません';
}

if(count($err) !== 0) {
    // エラーがあった場合は、画面を戻す
    $_SESSION = $err;
    header('Location: ./login_form.php');
    return;
}
// ログイン成功時の処理
$result = UserLogic::login($email, $password);

// ログイン失敗時の処理
if(!$result) {
    header('Location: ./login_form.php');
    return;
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン完了</title>
</head>
<body>
    <h2>ログイン完了</h2>
    <p>ログインしました</p>
    <a href="./mypage.php">マイページへ</a>
</body>
</html>