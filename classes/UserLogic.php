<?php

require_once '../db_connect.php';

class UserLogic
{
    /**
     * ユーザーを登録する処理
     * @param array $user_data
     * @return bool $result
     */
    public static function create_user($user_data)
    {
        $result = false;
        $dbh = db_connect();
        $sql = 'insert into users(name, email, password, register_date) values (?, ?, ?, now())';

        // ユーザーデータを配列に格納
        $arr = [];
        $arr[] = $user_data['username'];
        $arr[] = $user_data['email'];
        $arr[] = password_hash($user_data['password'], PASSWORD_DEFAULT);

        try {
            $stmt = $dbh->prepare($sql);
            $result = $stmt->execute($arr);
            return $result;
        } catch (PDOException $e) {
            return $result;
        }
    }

    /**
     * 同じユーザー名で登録されているか調べる処理
     * @param array $register_user
     * @return array|bool $result|false
     */
    public static function check_user($register_user)
    {
        $result = false;
        $dbh = db_connect();
        $sql = 'select name from users where name = ?';
        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $register_user['username']
            ));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return $result;
        }
    }

    /**
     * ログイン処理
     * @param string $email
     * @param string $password
     * @return bool $result
     */
    public static function login($email, $password)
    {
        // 結果
        $result = false;
        // emailからユーザー情報を取得する
        $user = self::get_user_by_email($email);

        if(!$user) {
            $_SESSION['msg'] = 'emailが一致しません';
            return $result;
        }
        
        // パスワードの照会
        if(password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['login_user'] = $user;
            $result = true;
            return $result;
        }

        $_SESSION['msg'] = 'パスワードが一致しません';
        return $result;
    }

    /**
     * emailからユーザー情報を取得する処理
     * @param string $email
     * @return array|bool $user|false
     */
    public static function get_user_by_email($email)
    {
        $sql = 'select * from users where email = ?';

        $arr = [];
        $arr[] = $email;

        try {
            $dbh = db_connect();
            $stmt = $dbh->prepare($sql);
            $stmt->execute($arr);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * ログインチェック処理
     * @param void
     * @return bool $result
     */
    public static function checkLogin()
    {
        $result = false;

        if(isset($_SESSION['login_user']) && $_SESSION['login_user']['id'] > 0) {
            return $result = true;
        }

        return $result;
    }

    /**
     * ログアウト処理
     * @param void
     * @return void
     */
    public static function logout()
    {
        $_SESSION = array();
        session_destroy();
    }
}