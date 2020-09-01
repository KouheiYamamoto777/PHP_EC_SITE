<?php

require_once '../db_connect.php';

class DbInsert
{
    private $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * 商品を登録する際、item_idの重複がないか調べる処理
     * @param void
     * @return array|bool $check_result|false
     */
    public function ckeck_item_id()
    {
        $check_result = false;

        $dbh = db_connect();
        $sql = 'select item_id from item_data where item_id = ?';
        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $this->result['item_id']
            ));
            $check_result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $check_result;
        } catch (PDOException $e) {
            return $check_result;
        }
    }

    /**
     * 2つのテーブルにinsertする関数
     * @param void
     * @return bool $insert_result
     */
    public function register_items()
    {
        $insert_result = false;

        $dbh = db_connect();
        $item_data_insert_sql = 'insert into item_data(item_id, name, category, price, image, done, created_date, updated_date)
                values (?, ?, ?, ?, ?, ?, now(), now())';
        $item_stock_insert_sql = 'insert into item_stock(item_id, stock, created_date, updated_date) values (?, ?, now(), now())';

        try {
            $stmt = $dbh->prepare($item_data_insert_sql);
            $stmt->execute([
                $this->result['item_id'],
                $this->result['item_name'],
                $this->result['item_cate'],
                $this->result['item_price'],
                $this->result['image_name'],
                $this->result['status']
            ]);
            $stmt = $dbh->prepare($item_stock_insert_sql);
            $stmt->execute([
                $this->result['item_id'],
                $this->result['item_qty']
            ]);
            header('Location: ./admin.php');
            return $insert_result = true;
        } catch (PDOException $e) {
            return $insert_result;
        }
    }
}


