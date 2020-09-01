<?php
session_start();
require_once '../classes/AdminLogic.php';
require_once '../classes/ShowUser.php';
require_once '../functions.php';

$lists = ShowUser::user_list();

$result = AdminLogic::check_ad_Login();

if(!$result) {
    $_SESSION['login_err'] = 'ユーザーを登録してからログインしてください';
    header('Location: ./signup.php');
    return;
}

$admin_data = $_SESSION['login_admin'];

?>
<!DOCTYpE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" integrity="sha384-HzLeBuhoNpvSl5KYnjx0BT+WB0QEEqLprO+NBkkk5gbc67FTaL7XIGa2w1L0Xbgc" crossorigin="anonymous">
    <style>
        body {
            margin: 0;
        }
        h2 {
            text-align: center;
        }
        .login_user {
            position: fixed;
            top: 35px;
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
            display: flex;
            flex-direction: column;
            align-items: space-around;
        }
        table {
            margin: 0 auto;
        }
    </style>
    <title>ユーザー一覧(管理者)</title>
</head>
<body>
    <h2>登録ユーザー一覧</h2>

    <div class="login_user">
        <span><i class="fas fa-user"></i>&nbsp;:&nbsp;<?= h($admin_data['admin_id']) ?></span></br>
        <form action="./logout.php" method="post">
            <input type="submit" name="logout" class="logout" value="ログアウト">
        </form>
    </div>

    <table>
        <tr>
            <th>登録ユーザー</th>
            <th>登録日</th>
        </tr>
        <?php
        for($i = 0; $i < count($lists); $i++):
        ?>
        <tr>
            <td><?= $lists[$i]['name'] ?></td>
            <td><?= $lists[$i]['register_date'] ?></td>
        </tr>
        <?php
        endfor;
        ?>
    </table>

    <a href="./admin.php">商品管理ページに移動</a>
</body>
</html>