<?php


require_once '../db_connect.php';

$_SESSION['err'] = array();

class FormValidate
{
    /**
     * ファイルデータ処理
     * $_FILES変数を受け取って画像名を返す
     * @param array $file_data
     * @return string|array $image_name|$err
     */
    public static function check_files($file_data)
    {
        $err = [];
        $result = false;

        try {
            if(!isset($file_data['item_image']['error']) || !is_int($file_data['item_image']['error'])) {
                throw new RuntimeException('パラメーターが不正です');
            }
            switch($file_data['item_image']['error']) {
                case UPLOAD_ERR_OK:
                break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('ファイルが選択されていません');
                case UPLOAD_ERR_INI_SIZE:
                    throw new RuntimeException('ファイルサイズが大きすぎます');
                default:
                    throw new RuntimeException('その他のエラーが発生しました');
            }
            if(!$ext = array_search(
                mime_content_type($file_data['item_image']['tmp_name']),
                array(
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png'
                ),
                true
            )) {
                throw new RuntimeException('ファイル形式が不正です');
            }
            if(!move_uploaded_file(
                $file_data['item_image']['tmp_name'],
                $path = sprintf('../uploads/%s.%s',
                    sha1_file($file_data['item_image']['tmp_name']),
                    $ext)
            )) {
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }
            chmod($path, 0774);
            return $result = basename($path);
        } catch (RuntimeException $e) {
            $err[] = $e->getMessage();
            $_SESSION['err'] = $err;
            return $result;
        }
    }

    /**
     * 商品追加バリデーション
     * $_POST変数を受け取ってバリデーションしたものにcheck_filesの返り値を加えて配列として返す
     * @param array $straight_item 
     * @param array $file_data
     * @return array|bool $item_data|false
     */
    public static function validate_items($straight_item, $file_data)
    {
        $err = array();
        $item_data = array();
        $result = false;

        if(!$item_name = (string)$straight_item['item_name']) {
            $err[] = '商品名を入力してください';
        } else if(mb_strlen($item_name) > 9) {
            $err[] = '商品名が長すぎます(8文字以内)';
        } else if(trim($item_name) === '') {
            $err[] = '商品名を入力してください';
        }

        if(!$item_id = $straight_item['item_id']) {
            $err[] = '商品IDを入力してください';
        } else if (!preg_match('/\w{1}-\d{4}/', $item_id)) {
            $err[] = '商品IDは英字1文字ハイフン数字4桁で入力してください';
        }
        
        $item_qty = $straight_item['item_qty'];

        if(trim($item_qty) === '') {
            $err[] = '在庫数を入力してください';
        } else {
            $item_qty = mb_convert_kana($item_qty, 'n');
            if (!is_numeric($item_qty)) {
                $err[] = '在庫は数値で入力してください';
            } else if(preg_match('/^([1-9]\d*|0)\.(\d+)?$/', $item_qty)) {
                $err[] = '在庫は整数で入力してください';
            }
        }
        
        $item_price = $straight_item['item_price'];

        if(trim($item_price) === '') {
            $err[] = '販売価格を設定してください';
        } else {
            $item_price = mb_convert_kana($item_price, 'n');
            if (!is_numeric($item_price)) {
                $err[] = '価格は数値で入力してください';
            } else if(preg_match('/^([1-9]\d*|0)\.(\d+)?$/', $item_price)) {
                $err[] = '価格は整数で入力してください';
            }
        }

        if(!$image_name = self::check_files($file_data)) {
            $err[] = '画像を選択してください';
        }

        if(!$item_cate = $straight_item['category']) {
            $err[] = 'カテゴリーを選択してください';
        }
        
        $item_status = $straight_item['status'];

        if (count($err) === 0) {
            $item_data['item_name'] = $item_name;
            $item_data['item_id'] = $item_id;
            $item_data['item_qty'] = (int)$item_qty;
            $item_data['item_price'] = (int)$item_price;
            $item_data['image_name'] = $image_name;
            $item_data['item_cate'] = $item_cate;
            $item_data['status'] = $item_status;
            unset($_SESSION['err']);
            return $result = $item_data;
        } else {
            $_SESSION['err'] = $err;
            return false;
        }
    }

    /**
     * 商品情報変更バリデーション
     * @param void
     * @return array|bool $changed_items|false
     */
    public static function modify_items()
    {
        $result = false;

        $err = [];
        $change_price = (int)filter_input(INPUT_POST, 'change_price');
        $change_stock = filter_input(INPUT_POST, 'change_stock');
        $change_status = (string)filter_input(INPUT_POST, 'change_status');
        $item_id = filter_input(INPUT_POST, 'item_id');

        if(!is_numeric($change_price)) {
            $err[] = '商品価格は数値で入力してください';
        } else if ($change_price <= 0) {
            $err[] = '商品価格を0にすることは出来ません';
        }
        
        if(!is_numeric($change_stock)) {
            $err[] = '商品在庫は数値で入力してください';
        }
        if (preg_match('/^([1-9]\d*|0)\.(\d+)?$/', $change_stock)) {
            $err[] = '商品在庫は整数で入力してください';
        }
        if (!in_array($change_status, array(
            '公開',
            '非公開'
        ))){
            $err[] = '公開ステータスは「公開」か「非公開」で入力してください';
        }

        if(count($err) === 0) {
            $changed_items = [
                'change_price' => (string)$change_price,
                'change_stock' => (string)$change_stock,
                'change_status' => $change_status,
                'item_id' => $item_id
            ];
            return $changed_items;
        } else {
            $_SESSION['err'] = $err;
            return $result;
        }
    }
}
