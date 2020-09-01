<?php

session_start();
require_once '../classes/UserLogic.php';

// エラーメッセージ
$err = [];

$token = filter_input(INPUT_POST, 'csrf_token');
if(!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
    exit('不正なリクエストです');
}

unset($_SESSION['csrf_token']);

// バリデーション
if(!$user_name = filter_input(INPUT_POST, 'username')) {
    $err[] = 'ユーザー名を入力してください';
}
if(trim($user_name) === '') {
    $err[] = 'ユーザー名を入力してください';
} else if(!preg_match("/\A[a-z\d]{6,20}+\z/i", $user_name)) {
    $err[] = 'ユーザー名は英数字6文字以上20文字以内で入力してください';
}

if(!$email = filter_input(INPUT_POST, 'email')) {
    $err[] = 'メールアドレスを入力してください';
}

$password = filter_input(INPUT_POST, 'password');
$password_conf = filter_input(INPUT_POST, 'password_conf');

if(!preg_match("/\A[a-z\d]{6,100}+\z/i", $password)) {
    $err[] = 'パスワードは英数字6文字以上100文字以内で入力してください';
}

if($password !== $password_conf) {
    $err[] = '確認用パスワードが一致しません';
}

if(count($err) === 0) {
    // 同じユーザー名のユーザーがいるか調べる
    if(UserLogic::check_user($_POST)) {
        $err[] = 'このユーザー名は既に登録されているため、使用できません';
    } else {
        // ユーザーを登録する処理
        $has_created = UserLogic::create_user($_POST);
        if(!$has_created) {
            $err[] = '登録に失敗しました';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録完了画面</title>
</head>
<body>
    <ul>
        <?php
            if(count($err) !== 0) {
                foreach($err as $e) {
                    echo '<li>' . $e . '</li>';
                }
            } else {
                echo '<h2>ユーザー登録が完了しました</h2>';
                echo '<h2><a href="./login_form.php">ログイン画面</a>からログインすることができるようになりました</h2>';
                echo '';
            }
        ?>
    </ul>
    
    <a href="./signup.php">ユーザー登録画面へ戻る</a>
</body>
</html>