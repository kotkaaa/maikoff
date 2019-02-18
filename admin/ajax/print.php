<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$itemID     = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$itemModule = isset($_GET['itemModule']) ? $_GET['itemModule'] : 0;
$item       = array(); // Item Info Array
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['page_url'];
$arrPageData['headTitle']     = ORDERS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
if($itemID && $itemModule == 'orders')  {
    $query  = "SELECT t.*, CONCAT_WS(' ', c.`firstname`, c.`surname`) `user_title`, 
                      s.`title` `shipping_title`, os.`title` `status_title`, os.`color_hex` 
               FROM `".ORDERS_TABLE."` t 
               LEFT JOIN `".USERS_TABLE."` c ON c.`id`=t.`user_id` 
               LEFT JOIN `".ORDER_STATUS_TABLE."` os ON os.`id`=t.`status_id` 
               LEFT JOIN `".SHIPPING_TYPES_TABLE."` s ON s.`id`=t.`shipping_id`  
               WHERE t.`id`={$itemID} LIMIT 1";
    $result = mysql_query($query);
    if(!$result || !mysql_num_rows($result)) {
        setSessionErrors('Ошибка! Заказ не найден!');
        Redirect($arrPageData['admin_url']);
    } else { 
        $item = mysql_fetch_assoc($result);
        $item['arProducts'] = getRowItems(ORDER_PRODUCTS_TABLE, '*', 'order_id='.$itemID);  
        foreach($item['arProducts'] as &$product) {
            // если по какой то причине изображение не попало в базу - подставляем дефолтное
            if(empty($product['product_image'])) {
                $product['product_image'] = MAIN_CATEGORIES_URL_DIR . "noimage.jpg";
            }
            $product['total_price'] = OrderHelper::getProductTotalPrice($product);
        } 
    }
}
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\   
$smarty->assign('itemModule',    $itemModule);
$smarty->assign('item',          $item);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################