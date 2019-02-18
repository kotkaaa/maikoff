<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

include_once 'include/classes/product/PrintProduct.php';
# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$itemID    = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$orderID   = (isset($_GET['orderID']) && intval($_GET['orderID'])) ? intval($_GET['orderID']) : 0;
$hasAccess = $UserAccess->getAccessToModule($arrPageData['module']);
$item      = array(); // Item Info Array
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['orderID']       = $orderID;
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['page_url'];
$arrPageData['headTitle']     = ORDERS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['close']         = isset($_GET['close']) ? intval($_GET['close']) : 0;
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
if ($itemID and $task == 'deleteItem') {
    $productTitle = getValueFromDB(ORDER_PRODUCTS_TABLE, 'title', 'WHERE id='.$itemID);
    if(deleteRecords(ORDER_PRODUCTS_TABLE, 'WHERE id='.$itemID)) {
        OrderHelper::updateTotals($orderID);
        setSessionMessage('Товар удален!');
        ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удален товар "'.$productTitle.'"', $lang, 'Заказ №'.$orderID, $orderID, 'orders');                
    } else {
        setSessionMessage('Ошибка! Не удалось удалить товар "'.$productTitle.'"!');
    }
    Redirect('/admin.php?module=orders&task=editItem&itemID='.$orderID);
} elseif (!empty($_POST) and ($task=='addItem' or $task=='editItem')) {
    if (!$orderID) {
        $arrPageData['errors'][] = 'Ошибка! Не переданы данные заказа!';
    } else {
        $arUnusedKeys = array();
        $query_type   = $itemID ? 'update'            : 'insert';
        $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';
        $Validator->validateGeneral($_POST['substrate_id'], 'Выберите подложку товара!!!');
        $Validator->validateGeneral($_POST['color_id'], 'Выберите цвет товара!!!');
        $Validator->validateGeneral($_POST['size_id'], 'Выберите размер товара!!!');
        $Validator->validateNumber($_POST['qty'], 'Введите корректное кол-во товара!!!');
        $Validator->validateNumber($_POST['price'], 'Введите корректную стоимость товара!!!');
        if (!empty($_POST['discount_value'])) {
            $Validator->validateNumber($_POST['discount_value'], 'Введите корректный размер скидки!!!');
        }
        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, исправьте следующие ошибки:  </font>".$Validator->getListedErrors();
        } else {
            $arPostData = $_POST;
            $query = 'SELECT p.`id`, p.`pcode`, p.`id` `print_id`, p.`title`, p.`category_id`, p.`placement`, "'.$arPostData['size_id'].'" `size_id`,
                      m.`title` ctitle, pa.`substrate_id` `default_substrate_id`, "'.$arPostData['color_id'].'" `color_id`, pa.`id` `assortment_id`  
                      FROM `'.PRINTS_TABLE.'` p 
                      LEFT JOIN `'.PRINT_ASSORTMENT_TABLE.'` pa ON pa.`print_id` = p.`id` AND pa.`substrate_id`='.$arPostData['substrate_id'].'  
                      LEFT JOIN `'.MAIN_TABLE.'` m ON m.`id` = p.`category_id` 
                      WHERE p.`id`='.$arPostData['print_id'].' GROUP BY p.`id` ORDER BY p.`order`';
            $result = mysql_query($query);
            if ($result and mysql_num_rows($result)) {
                $product = mysql_fetch_assoc($result);   
                $product = PrintProduct::getSimpleItem($product, true, true);
                $arPostData['product_idkey'] = $product['idKey'];
                if (getValueFromDB(ORDER_PRODUCTS_TABLE, 'product_idkey', 'WHERE order_id='.$orderID.' AND product_idkey="'.$arPostData['product_idkey'].'" '.($itemID ? ' AND id<>'.$itemID : '')) === false) {
                    $arPostData['order_id'] = $orderID;
                    $arPostData['product_id'] = $product['id'];                
                    $arPostData['module'] = 'prints';
                    $arPostData['pcode'] = $product['pcode'];
                    $arPostData['title'] = $product['title'];
                    $arPostData['substrate_title'] = $product['substrate_title'];
                    $arPostData['color_title'] = $product['color_title'];
                    $arPostData['size_title'] = getValueFromDB(SIZES_TABLE, 'title', 'WHERE id='.$arPostData['size_id']);
                    $arPostData['color_hex'] = $product["color_hex"];
                    $arPostData['product_url'] = $UrlWL->buildItemUrl($UrlWL->getCategoryById($product['category_id']), $product); 
                    $arPostData['product_image'] = "";            
                    if ($product["middle_image"]) {
                        $name = basename($product["middle_image"]);
                        if (strpos($name, '?') !== false) {
                            list($name,) = explode('?', $name);
                        }
                        $imageurl = prepareDirPath(dirname($product["middle_image"]));
                        if(file_exists($imageurl.$name)) {
                            $check    = getimagesize($imageurl.$name);
                            $arPostData['product_image'] = "data:{$check["mime"]};base64," . base64_encode(file_get_contents($imageurl.$name));
                        }
                    }  
                    $result = $DB->postToDB($arPostData, ORDER_PRODUCTS_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
                    if ($result) {
                        if (!$itemID && $result && is_int($result)) $itemID = $result;
                        if (mysql_affected_rows()) {                
                            if ($task == 'addItem') {
                                foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                                    ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Добавлен товар "'.$product['title'].'"', $key, 'Заказ №'.$orderID, $orderID, 'orders');
                            } else {
                                ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактирован товар "'.$product['title'].'"', $lang, 'Заказ №'.$orderID, $orderID, 'orders');
                            }
                        }   
                        //update order total_qty and total_price
                        OrderHelper::updateTotals($orderID);
                        setSessionMessage('Запись успешно сохранена!');
                        Redirect($arrPageData['current_url'].'&orderID='.$orderID.'&task=editItem&itemID='.$itemID.(isset($arPostData['save_close']) ? '&close=1' : ''));
                    } else {
                        $arrPageData['errors'][] = 'Запись <font color="red">НЕ была сохранена</font>!';
                    }
                } else {
                    $arrPageData['errors'][] = 'Ошибка! В заказе '.$orderID.' уже есть товар c такой же подложкой, цветом и размером!';
                }
            } else {
                $arrPageData['errors'][] = 'Ошибка! Товар не найден!';
            }
        }
    }
} 
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\    
if (!empty($_POST)) {
    $item = $_POST;
    $item['default_substrate_id'] = $_POST['substrate_id'];
    $item = PrintProduct::getSimpleItem($item, false, false, $item['color_id'], $item['price']);
    $item['total_price'] = OrderHelper::getProductTotalPrice($item);
    $item['product_image'] = $item['middle_image'];
} else {
    if(!$itemID) {
        $item = array_combine_multi($DB->getTableColumnsNames(ORDER_PRODUCTS_TABLE), ''); 
        $item['total_price'] = $item['print_id'] = $item['default_substrate_id'] = $item['size_cost'] = 0;
        $item['arSubstrates'] = $item['arSizes'] = $item['arColors'] = array();
    } else {
        $query  = "SELECT t.*, t.`product_id` AS `print_id`, t.`substrate_id` AS `default_substrate_id`, " . PHP_EOL
                . "t.`product_id` AS `print_id`, IF(s.`cost` IS NOT NULL, s.`cost`, '0') AS `size_cost` " . PHP_EOL
                . "FROM ".ORDER_PRODUCTS_TABLE." t " . PHP_EOL
                . "LEFT JOIN `".SIZES_TABLE."` s ON(s.`id` = t.`size_id`) " . PHP_EOL
                . "WHERE t.`order_id`={$orderID} AND t.`id`={$itemID} " . PHP_EOL
                . "GROUP BY t.`id` " . PHP_EOL
                . "LIMIT 1";
        $result = mysql_query($query);
        if (!$result || !mysql_num_rows($result)) {
            $arrPageData['errors'][] = 'Ошибка! Товар не найден!';
        } else {
            $item = mysql_fetch_assoc($result);
            $item = PrintProduct::getSimpleItem($item, false, false, $item['color_id'], $item['price']);
            $item['total_price'] = OrderHelper::getProductTotalPrice($item);
        }
    }
}

// если по какой то причине изображение не попало в базу - подставляем дефолтное
if(empty($item['product_image'])) {
    $item['product_image'] = MAIN_CATEGORIES_URL_DIR . "noimage.jpg";
}
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\   
$smarty->assign('item',          $item);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################