<?php
require_once '../db_connect.php';

class ShowUser
{
    /**
     * ユーザー一覧を取得する処理
     * @param void
     * @return array|bool $result|false
     */
    public static function user_list()
    {
        $result = false;

        $sql = 'select name, register_date from users';
        $dbh = db_connect();
        try {
            $stmt = $dbh->query($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return $result;
        }
    }
}