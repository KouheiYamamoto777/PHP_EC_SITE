<?php


require_once '../db_connect.php';

class ItemCustom
{
    private $items;

    public function __construct($items)
    {
        $this->items = $items;
        
        if(!empty($this->items['change_status'])) {
            if($this->items['change_status'] === '公開') {
                $this->items['change_status'] = '0';
            } else if ($this->items['change_status'] === '非公開') {
                $this->items['change_status'] = '1';
            }
        }
    }

    /**
     * 商品情報アップデート処理
     * @param void
     * @return bool|bool $update_result|false
     */
    public function change_item_data()
    {
        $update_result = false;
        $dbh = db_connect();
        $dbh->beginTransaction();
        
        try {
            $item_data_update_sql = 'update item_data set price = ?, done = ?, updated_date = now() where item_id = ?';
            $stmt = $dbh->prepare($item_data_update_sql);
            $stmt->execute([
                $this->items['change_price'],
                $this->items['change_status'],
                $this->items['item_id']
            ]);
            $cart_update_sql = 'update cart set done = ? where item_id = ?';
            $stmt = $dbh->prepare($cart_update_sql);
            $stmt->execute([
                $this->items['change_status'],
                $this->items['item_id']
                ]);
            $item_stock_update_sql = 'update item_stock set stock = ?, updated_date = now() where item_id = ?';
            $stmt = $dbh->prepare($item_stock_update_sql);
            $stmt->execute([
                $this->items['change_stock'],
                $this->items['item_id']
            ]);
            $dbh->commit();
            return $update_result = true;
        } catch (PDOException $e) {
            $dbh->rollBack();
            return $update_result;
        }
    }

    /**
     * 商品削除処理
     * @param void
     * @return bool|bool $delete_result|false
     */
    public function delete_item_data($delete_item)
    {
        $delete_result = false;

        try {
            $dbh = db_connect();

            $item_data_delete_sql = 'delete from item_data where item_id = ?';
            
            $stmt = $dbh->prepare($item_data_delete_sql);
            $stmt->execute([
                $delete_item['delete_id'],
            ]);
            $item_stock_delete_sql = 'delete from item_stock where item_id = ?';
            $stmt = $dbh->prepare($item_stock_delete_sql);
            $stmt->execute([
                $delete_item['delete_id'],
            ]);
            $cart_delete_sql = 'delete from cart where item_id = ?';
            $stmt = $dbh->prepare($cart_delete_sql);
            $stmt->execute([
                $delete_item['delete_id'],
                ]);
            return $delete_result = true;
        } catch (PDOException $e) {
            return $delete_result;
        }
    }
}