<?php defined('WEBlife') or die( 'Restricted access' );

/* 
 * WebLife CMS
 * Created on 10.07.2018, 14:26:50
 * Developed by http://weblife.ua/
 */
$itemID    = Checkout::getOrderID(!$IS_DEV);
$item      = array();
$purchases = array();
$arrPageData['headCss'][] = '/css/smart/result.css';

if ($itemID and $item=getSimpleItemRow($itemID, ORDERS_TABLE)) {
    if (!empty($item["name"]))
        $arrPageData["headTitle"] = sprintf("%s, поздравляем с покупкой!", $item["name"]);
    else $arrPageData["headTitle"] = "Поздравляем с покупкой!";
    // payment
    $item['arPayment'] = getSimpleItemRow($item['payment_id'], PAYMENT_TYPES_TABLE);    
    // Get purchases
    $DB->Query("SELECT * FROM `".ORDER_PRODUCTS_TABLE."` WHERE `order_id`=$itemID");
    while ($row = $DB->fetchAssoc()) {
        $purchases[]  = $row;
    } $DB->Free();
} else Redirect ("/");

$smarty->assign("item",      $item);
$smarty->assign("purchases", $purchases);