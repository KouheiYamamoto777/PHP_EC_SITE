<?php
require_once '../db_connect.php';

class AdminLogic
{
    /**
     * 管理者ログイン処理
     * @param string $id
     * @param string $ad_password
     * @return bool $result
     */
    public static function login($id, $ad_password)
    {
        // 結果
        $result = false;
        // idから管理者情報を取得する
        $admin = self::get_admin_from_id($id);

        if(!$admin) {
            $_SESSION['msg'] = 'idが一致しません';
            return $result;
        }
        
        // パスワードの照会
        if(password_verify($ad_password, $admin['admin_password'])) {
            session_regenerate_id(true);
            $_SESSION['login_admin'] = $admin;
            $result = true;
            return $result;
        }

        $_SESSION['msg'] = 'パスワードが一致しません';
        return $result;
    }
    
    /**
     * admin_idから管理者情報を取得
     * @param string $id
     * @return array|bool $admin|false
     */
    public static function get_admin_from_id($id)
    {
        $sql = 'select admin_id, admin_password from admin_data where admin_id = ?';

        $arr = [];
        $arr[] = $id;

        try {
            $dbh = db_connect();
            $stmt = $dbh->prepare($sql);
            $stmt->execute($arr);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            return $admin;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * 管理者ログインチェック
     * @param void
     * @return bool $result
     */
    public static function check_ad_Login()
    {
        $result = false;

        if(isset($_SESSION['login_admin'])) {
            return $result = true;
        }

        return $result;
    }

}