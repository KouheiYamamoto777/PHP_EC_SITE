<?php

session_start();
require_once '../classes/AdminLogic.php';

$err = [];

// 入力情報バリデーション
if(!$id = filter_input(INPUT_POST, 'admin_id')) {
    $err['admin_id'] = '管理者IDを入力してください';
}

if(!$ad_password = filter_input(INPUT_POST, 'admin_password')) {
    $err['ad_password'] = '管理者パスワードを入力してください';
}

if(count($err) !== 0) {
    // エラーがあった場合は、画面を戻す
    $_SESSION = $err;
    header('Location: ./login_form.php');
    return;
}

// ログイン成功時の処理
$result = AdminLogic::login($id, $ad_password);

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
    <title>ログイン成功</title>
</head>
<body>
    <h2>管理者ログイン完了</h2>
    <p>管理者としてログインしました</p>
    <a href="./admin.php">管理者ページへ</a>
</body>
</html>