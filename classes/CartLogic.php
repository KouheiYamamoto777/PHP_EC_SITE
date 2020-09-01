<?php

require_once '../functions.php';

class AddCart
{
    /**
     * カートに追加する処理
     * @param array $item_data
     * @param array $login_user
     * @return bool $result
     */
    public static function add_cart($item_data, $login_user)
    {
        $result = false;
        $dbh = db_connect();
        $sql = 'insert into cart(item_id, user_name, item_name, qty, price, image, stock) values (?, ?, ?, ?, ?, ?, ?)';

        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $item_data['item_id'],
                $login_user['name'],
                $item_data['add_name'],
                $item_data['add_qty'],
                $item_data['add_price'],
                $item_data['item_image'],
                $item_data['item_stock']
            ));
            return $result = true;
        } catch (PDOException $e) {
            return $result;
            echo $e->getMessage();
        }
    }

    /**
     * カートに追加する前に、テーブルにアイテム情報が存在するか調べる
     * @param array $item_data
     * @param array $login_user
     * @return bool $result
     */
    public static function search_cart($item_data, $login_user)
    {
        $result = false;

        $dbh = db_connect();
        $sql = 'select user_name, item_id from cart where user_name = ? and item_id = ?';

        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $login_user['name'],
                $item_data['item_id']
            ));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return $result;
        }
    }

    /**
     * カートの数量を増やす処理
     * @param array $item_data
     * @param array $login_user
     * @return bool $result
     */
    public static function add_stock($item_data, $login_user)
    {
        $result = false;

        $dbh = db_connect();
        $sql = 'update cart set qty = qty + 1 where user_name = ? and item_id = ?';

        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $login_user['name'],
                $item_data['item_id']   
            ));
            return $result = true;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}

class ExistCart
{
    /**
     * ユーザーのカート情報が存在するか調べる処理
     * @param array $login_user
     * @return array|bool $result|false
     */
    public static function exist_cart($login_user)
    {
        $result = false;

        $dbh = db_connect();
        $sql = 'select user_name, item_id from cart where user_name = ?';

        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $login_user['name']
            ));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return $result;
        }
    }
}

class ChengeQuantity 
{
    /**
     * カートページで数量を変更する処理
     * @param array $item_data
     * @return bool $result
     */
    public static function change_quantity($item_data)
    {
        $result = false;

        $dbh = db_connect();
        $sql = 'update cart set qty = ? where item_id = ? and user_name = ?';
        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $item_data['change_qty'],
                $item_data['item_id'],
                $item_data['user_name']
            ));
            return $result = true;
        } catch (PDOException $e) {
            return $result;
        }
    }

}

class DeleteCartItem
{
    /**
     * カートの商品を削除する処理
     * @param array $item_data
     * @return bool $result
     */
    public static function delete_cart_item($item_data)
    {
        $result = false;
    
        $dbh = db_connect();
        $sql = 'delete from cart where item_id = ? and user_name = ?';

        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $item_data['delete_id'],
                $item_data['delete_name']
            ));
            return $result = true;
        } catch (PDOException $e) {
            return $result;
        }
    }
}

class CartTotalPrice
{
    /**
     * カートの商品価格合計を取得する処理
     * @param array $login_user
     * @return int $result
     */
    public static function cart_total_price($login_user)
    {
        $result = false;

        $dbh = db_connect();
        $sql = 'select sum(qty * price) as total_price from cart where user_name = ?';
        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $login_user['name']
            ));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return $result;
        }
    }
}

class CartTotalQuantity
{
    /**
     * カートの商品個数合計を取得する処理
     * @param array $login_user
     * @return int|bool $result|false
     */
    public static function cart_total_quantity($login_user)
    {
        $result = false;

        $dbh = db_connect();
        $sql = 'select sum(qty) as total_qty from cart where user_name = ?';
        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $login_user['name']
            ));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return $result;
        }
    }
}

class CartTotalStock
{
    /**
     * カートの商品在庫合計を取得する処理
     * @param array $login_user
     * @return int|bool $result|false
     */ 
    public static function cart_total_stock($login_user)
    {
        $result = false;
        
        $dbh = db_connect();
        $sql = 'select sum(stock) as total_stock from cart where user_name = ?';
        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $login_user['name']
                ));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return $result;
        }
    }
}

class CartTotalDone
{
    /**
     * カートの公開ステータス合計を取得する処理
     * @param array $login_user
     * @return int|bool $result|false
     */ 
    public static function cart_total_done($login_user)
    {
        $result = false;
        $dbh = db_connect();
        
        $sql = 'select sum(done) as total_done from cart where user_name = ?';
        
        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $login_user['name']
                ));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return $result;
        }
    }
}

class CartTotalRecord
{
    /**
     * カートテーブルのレコード数を取得する処理
     * @param array $login_user
     * @return int|bool $result|false
     */ 
    public static function cart_total_record($login_user)
    {
        $result = false;
        $dbh = db_connect();
        
        $sql = 'select count(user_name) as total_record from cart where user_name = ?';
        
        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $login_user['name']
                ));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            return $result;
        }
    }
}

class ClearCart
{
    /**
     * 購入確定時にログインユーザーのカートを空にする処理
     * @param array $login_user
     * @return bool $result
     */
    public static function clear_cart($login_user)
    {
        $result = false;

        $dbh = db_connect();
        $sql = 'delete from cart where user_name = ?';
        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array(
                $login_user['name']
            ));
            return $result = true;
        } catch (PDOException $e) {
            return $result;
        }
    }
}

class ReduceStock
{
    /**
     * 購入確定時に商品の在庫を減らす処理
     * @param array $cart_result
     * @return bool $result
     */
    public static function reduce_stock($cart_result)
    {

        $dbh = db_connect();
        $sql = 'update item_stock set stock = stock - ? where item_id = ?';

        try {
            foreach($cart_result as $item) {
                $stmt = $dbh->prepare($sql);
                $stmt->execute(array(
                    $item['qty'],
                    $item['item_id']
                ));
            }
            return $result = true;
        } catch (PDOException $e) {
            return $result;
        }
    }
}