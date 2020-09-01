<?php

require_once '../db_connect.php';

interface Display
{
    public function display();
}

class UserDisplay implements Display
{
    /**
     * マイページでの商品表示
     * @param void
     * @return array|bool $result|false
     */
    public function display()
    {
        $result = false;

        $dbh = db_connect();
        $sql = 'select id.item_id, id.name, id.price, id.image, ist.stock 
                from item_data as id join item_stock as ist 
                on id.item_id = ist.item_id
                where id.done = 0';
        try {
            $stmt = $dbh->query($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return $result;
        }
    }
}

class AdminDisplay implements Display
{
    /**
     * 管理者ページでの商品表示
     * @param void
     * @return array|bool $result|false
     */
    public function display()
    {
        $result = false;

        $dbh = db_connect();
        $sql = 'select id.item_id, id.name, id.price, id.image, id.done, ist.stock 
                from item_data as id join item_stock as ist
                on id.item_id = ist.item_id
                order by id.item_id desc';
        try {
            $stmt = $dbh->query($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return $result;
        }
    }
}

class CartDisplay implements Display
{
    private $user_data;

    public function __construct($user_data)
    {
        $this->user_data = $user_data;
    }
    /**
     * カートページでの商品表示
     * @param array $user_data
     * @return array|bool $result|false
     */
    public function display()
    {
        $result = false;
    
        $dbh = db_connect();
        $sql = 'select c.item_id, c.user_name, c.item_name, c.qty, c.price, c.image, c.done, ist.stock
                from cart as c join item_stock as ist
                on c.item_id = ist.item_id
                where user_name = ?';
        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $this->user_data['name']
            ));
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return $result;
        }

    }
}