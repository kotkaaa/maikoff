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
$filters   = !empty($_GET['filters'])? $_GET['filters']: array();
$arrOrder  = getOrdersByKeyExplodeFilteredArray($_GET, 'pageorder', '_');
$hasAccess = $UserAccess->getAccessToModule($arrPageData['module']);
$items     = array(); // Items Array of items Info arrays
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['page_url'];
$arrPageData['headTitle']     = ORDERS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['arShippings']   = getRowItems(SHIPPING_TYPES_TABLE, '*');
$arrPageData['arManagers']    = getRowItems(USERS_TABLE, '*', 'type="'.USER_TYPE_MANAGER.'"', 'firstname');
$arrPageData['filters']       = $filters;
$arrPageData['items_on_page'] = 20;
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
if($itemID && $task == 'setAction') {      
    $column = isset($_GET['column']) ? $_GET['column'] : '';
    if($column) {
        $DB->postToDB(array($column => 1), ORDER_PRODUCTS_TABLE, 'WHERE id='.$itemID, array(), 'update', false);
    }
    $product = getSimpleItemRow($itemID, ORDER_PRODUCTS_TABLE);
    $json = array('output' => OrderHelper::getIndustryActions($product), 'bg' => OrderHelper::getProductBG($product));  
    echo json_encode($json);
    exit();
}
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headCss'][] = '<link rel="stylesheet" type="text/css" href="/js/jquery/daterangepicker/daterangepicker.css" />';  
$arrPageData['headScripts'][] = '<script type="text/javascript" src="/js/jquery/momentjs/moment.min.js"></script>';
$arrPageData['headScripts'][] = '<script type="text/javascript" src="/js/jquery/daterangepicker/daterangepicker.min.js"></script>';     

// Display Items List Data
$where = 't.status_id='.OrderHelper::STATUS_INDUSTRY;

PHPHelper::prepareDateFilter($arrPageData['filters'], 't', 'created', $arrPageData['filter_url'], $where);
if(!empty($arrPageData['filters'])) {   
    PHPHelper::prepareDateFilter($arrPageData['filters'], 't', 'planned', $arrPageData['filter_url'], $where);

    if(!empty($arrPageData['filters']['title'])) {
        $str = strtolower(htmlspecialchars($arrPageData['filters']['title']));
        $where .= ($where ? ' AND ' : ' ').' (t.`id` = "'.$str.'" OR  t.`name` LIKE "%'.$str.'%" OR t.`phone` LIKE "%'.$str.'%" OR t.`email` LIKE "%'.$str.'%") ';
        $arrPageData['filter_url'] .= '&filters[titile]='.$arrPageData['filters']['title'];
    }
    if(!empty($arrPageData['filters']['manager_id'])) {
        $where .= ($where ? ' AND ' : ' ').' t.`manager_id` = "'.$arrPageData['filters']['manager_id'].'" ';
        $arrPageData['filter_url'] .= '&filters[manager_id][]='.$arrPageData['filters']['manager_id'];
    }
    if(!empty($arrPageData['filters']['shipping'])) {
        $where .= ($where ? ' AND ' : ' ').' t.`shipping_id` IN( "'.implode('","', $arrPageData['filters']['shipping']).'" ) ';
        $arrPageData['filter_url'] .= '&filters[shipping][]='.implode('&filters[shipping][]=', $arrPageData['filters']['shipping']);
    }
}

// Create Order Links
$arrPageData['arrOrderLinks'] = getOrdersLinks(array(
    'default'     => 'номеру', 
    'manager_id'  => 'менеджер',
    'shipping_id' => 'доставка', 
    'created'     => 'дата создания',
    'planned'     => 'дата планирования',
), $arrOrder['get'], $arrPageData['admin_url'].$arrPageData['filter_url'], 'pageorder', '_');

$arrPageData['filter_url'].= !empty($arrOrder['url']) ? '&'.implode('&', $arrOrder['url']) : '';  
    
$where = ($where ? ' WHERE ' : '').$where;

// Start Total pages and Pager
$arrPageData['total_items'] = intval(getValueFromDB(ORDERS_TABLE." t", 'COUNT(*)', $where, 'count'));
$arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['filter_url']);
$arrPageData['total_pages'] = $arrPageData['pager']['count'];
$arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
// END Total pages and Pager

$order = "ORDER BY ".(!empty($arrOrder['mysql']) ? 't.'.implode(', t.', $arrOrder['mysql']) : "t.`processed` DESC, t.`id`");
$limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";

$query  = "SELECT t.* , s.`title` `shipping_title`, os.`title` `status_title`, os.`color_hex`,  
           CONCAT_WS(' ', c.`firstname`, c.`surname`) user_title, 
           IF(t.shipping_price = 0 AND s.comment != '', s.comment, t.shipping_price) shipping_price, 
           IF(m.id IS NOT NULL, CONCAT_WS(' ', m.`firstname`, m.`surname`), '') manager_title 
           FROM `".ORDERS_TABLE."` t 
           LEFT JOIN `".ORDER_STATUS_TABLE."` os ON os.`id`=t.`status_id` 
           LEFT JOIN `".SHIPPING_TYPES_TABLE."` s ON s.`id`=t.`shipping_id`  
           LEFT JOIN `".USERS_TABLE."` c ON c.`id`=t.`user_id`   
           LEFT JOIN `".USERS_TABLE."` m ON m.`id`=t.`manager_id`   
           $where $order $limit";
$result = mysql_query($query);
if($result) {
    while ($row = mysql_fetch_assoc($result)) {
        $row['arFiles'] = getRowItems(ORDER_FILES_TABLE, '*', 'order_id='.$row['id']);
        $row['arProducts'] = getRowItems(ORDER_PRODUCTS_TABLE, '*', 'order_id='.$row['id']);  
        foreach($row['arProducts'] as &$product) {
            // если по какой то причине изображение не попало в базу - подставляем дефолтное
            if(empty($product['product_image'])) {
                $product['product_image'] = MAIN_CATEGORIES_URL_DIR . "noimage.jpg";
            }
            $product['total_price'] = OrderHelper::getProductTotalPrice($product);
        } 
        $items[] = $row;
    }        
} else {
    $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
}
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\   
$smarty->assign('items',         $items);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################