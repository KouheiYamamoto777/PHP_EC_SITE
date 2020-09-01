<?php

require_once '../env.php';

// データベース接続関数

function db_connect()
{
    $host = DB_HOST;
    $dbname = DB_NAME;
    $user = DB_USER;
    $pass = DB_PASS;

    try {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $dbh = new PDO(
            $dsn,
            $user,
            $pass,
            [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
        return $dbh;
    } catch (Exception $e) {
        echo 'Error : ' . $e->getMessage();
    }
}
