<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

include_once 'include/classes/AdManager.php';
include_once 'include/classes/product/PrintProduct.php';
# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$itemID    = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$filters   = !empty($_GET['filters'])? $_GET['filters']: array();
$arrOrder  = getOrdersByKeyExplodeFilteredArray($_GET, 'pageorder', '_');
$hasAccess = $UserAccess->getAccessToModule($arrPageData['module']);
$item      = array(); // Item Info Array
$items     = array(); // Items Array of items Info arrays
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['page_url'];
$arrPageData['headTitle']     = ORDERS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['arShippings']   = getRowItems(SHIPPING_TYPES_TABLE, '*');
$arrPageData['arPayments']    = getRowItems(PAYMENT_TYPES_TABLE, '*', 'active>0');
$arrPageData['arStatuses']    = getRowItems(ORDER_STATUS_TABLE, '*');
$arrPageData['arManagers']    = getRowItems(USERS_TABLE, '*', 'type="'.USER_TYPE_MANAGER.'"', 'firstname');
$arrPageData['filters']       = $filters;
$arrPageData['items_on_page'] = 20;
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
if($itemID && $task == 'getProducts') {
    $json = array();    
    $table = ORDERS_TABLE.' o LEFT JOIN '.SHIPPING_TYPES_TABLE.' s ON s.id=o.shipping_id';
    $select = 'o.*, IF(o.`shipping_price`=0 AND s.`comment`<>"", s.`comment`, o.`shipping_price`) `shipping_price_title`';
    if(($item = getItemRow($table, $select, 'WHERE o.id='.$itemID))) {
        $item['arProducts'] = getRowItems(ORDER_PRODUCTS_TABLE, '*', 'order_id='.$itemID);  
        foreach($item['arProducts'] as &$product) {
            // если по какой то причине изображение не попало в базу - подставляем дефолтное
            if(empty($product['product_image'])) {
                $product['product_image'] = MAIN_CATEGORIES_URL_DIR . "noimage.jpg";
            }
            $product['total_price'] = OrderHelper::getProductTotalPrice($product);
        }   
        $arrPageData['isEditable'] = OrderHelper::isEditable($item['status_id']);
        $arrPageData['isEditableProducts'] = OrderHelper::isEditableProducts($item['status_id']);
        $smarty->assign('item', $item);   
        $smarty->assign('arrPageData', $arrPageData); 
        $json['output'] = $smarty->fetch('ajax/order_products.tpl');
        $smarty->assign('arHistoryData', ActionsLog::getInstance()->getHistory(array('modules' => array('orders'), 'oid'=>$itemID, 'langs'=>array($lang))));                            
        $json['history'] = $smarty->fetch('common/object_actions_log.tpl');
    }
    echo json_encode($json);
    exit();
}
else if ($itemID && $task == 'sendSMS') {
    $json = array();
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $text = isset($_GET['text']) ? htmlspecialchars($_GET['text']) : '';    
    if($text && ($phone = getValueFromDB(ORDERS_TABLE, 'phone', 'WHERE id='.$itemID))) {
        require_once('include/classes/TurboSMS.php'); 
        if(TurboSMS::isAvailible()) {
            $TurboSMS = new TurboSMS(); 
            $result = getenv('IS_DEV') ? true : $TurboSMS->sendSMS(array($phone), $text);
            if($result) {
                $date = date('Y-m-d H:i:s');
                $message = "Отправлен СМС на номер {$phone} c текстом: {$text}";                
                updateRecords(ORDERS_TABLE, 'sms_'.$type.'="'.$date.'"', 'WHERE id='.$itemID);
                $json['updated'] = 'отправлено '.date('d.m.Y H:i:s', strtotime($date));
            } else {
                $message = 'Ошибка! Смс не отправлен!';
            }
        } else {           
            $message = 'Ошибка отправки СМС - сервис TurboSMS временно недоступен!';
        }
        ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, $message, $lang, 'Заказ №'.$itemID, $itemID, 'orders'); 
        $json['message'] = $message;

        $smarty->assign('arHistoryData', ActionsLog::getInstance()->getHistory(array('modules' => array('orders'), 'oid'=>$itemID, 'langs'=>array($lang))));                            
        $json['history'] = $smarty->fetch('common/object_actions_log.tpl');
    } else {
        $json['message'] = 'Ошибка! Не указан текст или номер телефона!';
    }    
    echo json_encode($json);
    exit();
}
else if(!empty($_POST) && ($task=='addItem' || $task=='editItem')) {
    $arrPageData['filter_url'] = !empty($arrPageData['filters']) ? '&'.http_build_query(array('filters' => $arrPageData['filters'] )) : '';

    $arUnusedKeys = array();
    $query_type   = $itemID ? 'update'            : 'insert';
    $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';
    if(empty($_POST['status_id']) || $_POST['status_id'] != OrderHelper::STATUS_CANCEL) {
        $Validator->validateGeneral($_POST['shipping_id'], 'Вы не выбрали тип доставки!!!');
    }
    $Validator->validateGeneral($_POST['name'], 'Вы не ввели имя клиента!!!');
    $_POST['phone'] = !empty($_POST['phone']) ? PHPHelper::clearePhone($_POST['phone']) : '';
    $Validator->validatePhone($_POST['phone'], 'Вы не ввели или ввели некорректный номер клиента!!!');
    $statusID = $itemID ? getValueFromDB(ORDERS_TABLE, 'status_id', 'WHERE id='.$itemID) : 0;
    if($task == 'editItem') {
        if(!OrderHelper::checkStatusChange($statusID, $_POST['status_id'])) {
            $Validator->addError('Некорректное изменения статуса');
        }
    }
    if ($Validator->foundErrors()) {
        $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
    } else {
        $arPostData = $_POST;
        if($task == 'addItem') {
            $arPostData['created'] = date('Y-m-d H:i:s');
            $arPostData['manager_id'] = $objUserInfo->id;
            $arPostData['status_id'] = OrderHelper::STATUS_NEW;
            $arPostData['type_id'] = OrderHelper::TYPE_ADMIN;
        }
        if($arPostData['shipping_id'] == OrderHelper::DELIVERY_TYPE_POST || $arPostData['shipping_id'] == OrderHelper::DELIVERY_TYPE_SELF) {
            $arPostData['shipping_price'] = 0;
        }
        // почему то без явного определения это поле при сохранении становилось нулями
        if(!isset($arPostData['planned']) || empty($arPostData['planned'])) $arPostData['planned'] = null;
        else $arPostData['planned'] = date('Y-m-d H:i:s', strtotime($arPostData['planned']));
        $smsText = '';
        if($statusID != $arPostData['status_id']) {
            if($arPostData['status_id'] == OrderHelper::STATUS_INDUSTRY) {
                $arPostData['processed'] = date('Y-m-d H:i:s');
                $smsText = 'Заказ №'.$itemID.' - передан на печать. Maikoff.com.ua';
            } else if ($arPostData['status_id'] == OrderHelper::STATUS_DELIVERY) {
                $smsText = 'Заказ №'.$itemID.' - передан на доставку. Maikoff.com.ua';
            }
        }
        if($smsText) {        
            //отправляем смс что на производстве
            require_once('include/classes/TurboSMS.php'); 
            if(TurboSMS::isAvailible()) {
                $TurboSMS = new TurboSMS(); 
                $result = getenv('IS_DEV') ? true : $TurboSMS->sendSMS(array($arPostData['phone']), $smsText);
                if($result) {
                    $date = date('Y-m-d H:i:s');
                    $message = "Отправлен СМС на номер {$arPostData['phone']} c текстом: {$smsText}";                
                } else {
                    $message = "Ошибка! СМС на номер {$arPostData['phone']} c текстом: {$smsText} - не отправлен!";
                }
            } else {           
                $message = 'Ошибка отправки СМС - сервис TurboSMS временно недоступен!';
            }
            ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, $message, $lang, 'Заказ №'.$itemID, $itemID, 'orders'); 
        }            
        $result = $DB->postToDB($arPostData, ORDERS_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
        if($result){
            if (!$itemID && $result && is_int($result)) $itemID = $result;
            if (mysql_affected_rows()) {                
                if ($task == 'addItem') {
                    foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                        ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создан Заказ №'.$itemID, $key, 'Заказ №'.$itemID, $itemID, $arrPageData['module']);
                } else {
                    ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактирован Заказ №'.$itemID, $lang, 'Заказ №'.$itemID, $itemID, $arrPageData['module']);
                }
            }            
            //если не выбран клиент, то ищем или создаем его по введенным данным
            if(!$arPostData['user_id']) {
                $uid = OrderHelper::getUserID($DB, $arPostData);
                updateRecords(ORDERS_TABLE, 'user_id='.$uid, 'WHERE id='.$itemID);
            }
            setSessionMessage('Запись успешно сохранена!');
            Redirect($arrPageData['current_url'].$arrPageData['filter_url'].'&task=editItem&itemID='.$itemID);
        } else {
            $arrPageData['errors'][] = 'Запись <font color="red">НЕ была сохранена</font>!';
        }
    }
} 
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
if($task=='addItem' || $task=='editItem'){
    $arrPageData['headScripts'][] = '<script src="/js/libs/jquery.inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>';
    $arrPageData['filter_url'] = !empty($arrPageData['filters']) ? '&'.http_build_query(array('filters' => $arrPageData['filters'] )) : '';
    
    if(!$itemID) {
        $item = array_combine_multi($DB->getTableColumnsNames(ORDERS_TABLE), '');         
        $item['manager_id'] = $objUserInfo->id;
        $item['manager_title'] = $objUserInfo->firstname.' '.$objUserInfo->surname;
        $item['status_id'] = OrderHelper::STATUS_NEW;
        $item['status_title'] = getValueFromDB(ORDER_STATUS_TABLE, 'title', 'WHERE id='.$item['status_id']);
        $item['shipping_id'] = $item['payment_id'] = $item['shipping_price'] = $item['total_qty'] = $item['total_price'] = 0;
        $item['channel'] = $item['shipping_title'] = $item['payment_title'] = $item['user_title'] = $item['user_phone'] = $item['descr'] = '';    
        $item['shipping_price_title'] = 0;
        $item['created'] = date('Y-m-d H:i:s');
        $item['arProducts'] = $item['arFiles'] = $item['arHistory'] = array();      
        
        $arrPageData['arrBreadCrumb'][] = array('title' => 'Создание заказа');
    } else {
        $query  = "SELECT t.* , s.`title` `shipping_title`, os.`title` `status_title`, os.`color_hex`, 
                   CONCAT_WS(' ', c.`firstname`, c.`surname`) `user_title`, c.`phone` `user_phone`, c.`descr`, 
                   IF(m.`id` IS NOT NULL, CONCAT_WS(' ', m.`firstname`, m.`surname`), '') `manager_title`, p.`title` `payment_title`, 
                   IF(t.`shipping_price`=0 AND s.`comment`<>'', s.`comment`, t.`shipping_price`) `shipping_price_title`  
                   FROM `".ORDERS_TABLE."` t 
                   LEFT JOIN `".ORDER_STATUS_TABLE."` os ON os.`id`=t.`status_id` 
                   LEFT JOIN `".SHIPPING_TYPES_TABLE."` s ON s.`id`=t.`shipping_id`  
                   LEFT JOIN `".PAYMENT_TYPES_TABLE."` p ON p.`id`=t.`payment_id`  
                   LEFT JOIN `".USERS_TABLE."` c ON c.`id`=t.`user_id`   
                   LEFT JOIN `".USERS_TABLE."` m ON m.`id`=t.`manager_id`   
                   WHERE t.`id`={$itemID} LIMIT 1";
        $result = mysql_query($query);
        if(!$result || !mysql_num_rows($result)) {
            setSessionErrors('Ошибка! Заказ не найден!');
            Redirect($arrPageData['admin_url']);
        } else {
            $item = mysql_fetch_assoc($result);
            $item['channel'] = AdManager::getClientNameByCode($item['channel_code']);     
            $item['arProducts'] = getRowItems(ORDER_PRODUCTS_TABLE, '*', 'order_id='.$itemID);  
            foreach($item['arProducts'] as &$product) {
                // если по какой то причине изображение не попало в базу - подставляем дефолтное
                if(empty($product['product_image'])) {
                    $product['product_image'] = MAIN_CATEGORIES_URL_DIR . "noimage.jpg";
                }
                $product['total_price'] = OrderHelper::getProductTotalPrice($product);
            }        
            $item['arFiles'] = getRowItems(ORDER_FILES_TABLE, '*', 'order_id='.$itemID);
            $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));     
        }   
    }
                    
    $arrPageData['isEditable'] = OrderHelper::isEditable($item['status_id']);
    $arrPageData['isEditableProducts'] = OrderHelper::isEditableProducts($item['status_id']);
    $arrPageData['availableStatuses'] = OrderHelper::getAvailableStatuses($item['status_id']);
    
} else {
    $arrPageData['arTypes'] = getRowItems(ORDER_TYPES_TABLE);
    
    // Include Need CSS and Scripts For This Page To Array
    $arrPageData['headCss'][] = '<link rel="stylesheet" type="text/css" href="/js/jquery/daterangepicker/daterangepicker.css" />';  
    $arrPageData['headScripts'][] = '<script type="text/javascript" src="/js/jquery/momentjs/moment.min.js"></script>';
    $arrPageData['headScripts'][] = '<script type="text/javascript" src="/js/jquery/daterangepicker/daterangepicker.min.js"></script>';     
          
    // Display Items List Data
    $where = "";  
    
    if(!empty($arrPageData['filters'])) { 
        PHPHelper::prepareDateFilter($arrPageData['filters'], 't', 'created', $arrPageData['filter_url'], $where);
        PHPHelper::prepareDateFilter($arrPageData['filters'], 't', 'planned', $arrPageData['filter_url'], $where);
        PHPHelper::prepareDateFilter($arrPageData['filters'], 't', 'closed', $arrPageData['filter_url'], $where);

        if(!empty($arrPageData['filters']['title'])) {
            $str = mb_strtolower(htmlspecialchars($arrPageData['filters']['title']));
            $where .= ($where ? ' AND ' : ' ').' (t.`id` = "'.$str.'" OR  t.`name` LIKE "%'.$str.'%" OR t.`phone` LIKE "%'.$str.'%" OR t.`email` LIKE "%'.$str.'%" OR t.`admin_comment` LIKE "%'.$str.'%") ';
            $arrPageData['filter_url'] .= '&filters[title]='.$arrPageData['filters']['title'];
        }
        if(!empty($arrPageData['filters']['user_id'])) {
            $where .= ($where ? ' AND ' : ' ').' t.`user_id` = "'.$arrPageData['filters']['user_id'].'" ';
            $arrPageData['filter_url'] .= '&filters[user_id]='.$arrPageData['filters']['user_id'];
        }
        if(!empty($arrPageData['filters']['manager_id'])) {
            $where .= ($where ? ' AND ' : ' ').' t.`manager_id` = "'.$arrPageData['filters']['manager_id'].'" ';
            $arrPageData['filter_url'] .= '&filters[manager_id]='.$arrPageData['filters']['manager_id'];
        }
        if(!empty($arrPageData['filters']['channel_code'])) {
            $where .= ($where ? ' AND ' : ' ').' t.`channel_code` = "'.$arrPageData['filters']['channel_code'].'" ';
            $arrPageData['filter_url'] .= '&filters[channel_code]='.$arrPageData['filters']['channel_code'];
        }
        if(!empty($arrPageData['filters']['shipping'])) {
            $where .= ($where ? ' AND ' : ' ').' t.`shipping_id` IN( "'.implode('","', $arrPageData['filters']['shipping']).'" ) ';
            $arrPageData['filter_url'] .= '&filters[shipping][]='.implode('&filters[shipping][]=', $arrPageData['filters']['shipping']);
        }
        if(!empty($arrPageData['filters']['payment'])) {
            $where .= ($where ? ' AND ' : ' ').' t.`payment_id` IN( "'.implode('","', $arrPageData['filters']['payment']).'" ) ';
            $arrPageData['filter_url'] .= '&filters[payment][]='.implode('&filters[payment][]=', $arrPageData['filters']['payment']);
        }
        if(!empty($arrPageData['filters']['status'])) {
            $where .= ($where ? ' AND ' : ' ').' t.`status_id` IN( "'.implode('","', $arrPageData['filters']['status']).'" ) ';
            $arrPageData['filter_url'] .= '&filters[status][]='.implode('&filters[status][]=', $arrPageData['filters']['status']);
        }
        if(!empty($arrPageData['filters']['type'])) {
            $where .= ($where ? ' AND ' : ' ').' t.`type_id` IN( "'.implode('","', $arrPageData['filters']['type']).'" ) ';
            $arrPageData['filter_url'] .= '&filters[type][]='.implode('&filters[type][]=', $arrPageData['filters']['type']);
        }
    } else PHPHelper::prepareDateFilter($arrPageData['filters'], 't', 'created', $arrPageData['filter_url'], $where, true);
    if(empty($arrPageData['filters']) || empty($arrPageData['filters']['type'])) {
        $arrPageData['filters']['type'] = OrderHelper::getRequestTypes();
        $where .= ($where ? ' AND ' : ' ').' t.`type_id` IN( "'.implode('","', $arrPageData['filters']['type']).'" ) ';
        //$arrPageData['filter_url'] .= '&filters[type][]='.implode('&filters[type][]=', $arrPageData['filters']['type']);
    }

    $arrPageData['arClients'] = getRowItems(USERS_TABLE.' u LEFT JOIN '.ORDERS_TABLE.' t ON t.`user_id`=u.`id`', 'DISTINCT u.*', $where, 'u.`firstname`'); 
    
    // Create Order Links
    $arrPageData['arrOrderLinks'] = getOrdersLinks(array(
        'default'     => 'номеру', 
        'user_id'     => 'клиент',
        'manager_id'  => 'менеджер',
        'status_id'   => 'статус', 
        'shipping_id' => 'доставка', 
        'created'     => 'дата создания',
        'closed'      => 'дата выполнения',
        'planned'     => 'дата планирования',
    ), $arrOrder['get'], $arrPageData['admin_url'].$arrPageData['filter_url'], 'pageorder', '_');

    $arrPageData['filter_url'].= !empty($arrOrder['url']) ? '&'.implode('&', $arrOrder['url']) : '';    
        
    $where = ($where ? ' WHERE ' : '').$where;
    
    // Start Total pages and Pager
    $arrPageData['totals']      = getItemRow(ORDERS_TABLE." t", 'IFNULL(COUNT(id),0) cnt, IFNULL(SUM(t.total_price),0) price, IFNULL(SUM(t.total_qty),0) qty', $where);
    $arrPageData['total_items'] = $arrPageData['totals'] ? $arrPageData['totals']['cnt'] : 0;
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['filter_url']);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager

    $order = "ORDER BY ".(!empty($arrOrder['mysql']) ? 't.'.implode(', t.', $arrOrder['mysql']) : "t.`created` DESC, t.`id`");
    $limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";
    
    $query  = "SELECT t.* , s.`title` `shipping_title`, os.`title` `status_title`, os.`color_hex`,  
               CONCAT_WS(' ', c.`firstname`, c.`surname`) user_title, ot.`title` `substrate_title`,
               IF(t.shipping_price = 0 AND s.comment != '', s.comment, t.shipping_price) shipping_price, p.`title` `payment_title`, 
               IF(t.`shipping_price`=0 AND s.`comment`<>'', s.`comment`, t.`shipping_price`) `shipping_price_title`,  
               IF(m.id IS NOT NULL, CONCAT_WS(' ', m.`firstname`, m.`surname`), '') manager_title 
               FROM `".ORDERS_TABLE."` t 
               LEFT JOIN `".ORDER_STATUS_TABLE."` os ON os.`id`=t.`status_id` 
               LEFT JOIN `".ORDER_TYPES_TABLE."` ot ON ot.`id`=t.`type_id` 
               LEFT JOIN `".SHIPPING_TYPES_TABLE."` s ON s.`id`=t.`shipping_id`  
               LEFT JOIN `".PAYMENT_TYPES_TABLE."` p ON p.`id`=t.`payment_id`  
               LEFT JOIN `".USERS_TABLE."` c ON c.`id`=t.`user_id`   
               LEFT JOIN `".USERS_TABLE."` m ON m.`id`=t.`manager_id`   
               $where $order $limit";
    $result = mysql_query($query);
    if($result) {
        while ($row = mysql_fetch_assoc($result)) {
            $items[] = $row;
        }        
    } else {
        $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
    }
}
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\   
$smarty->assign('item',          $item);
$smarty->assign('items',         $items);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################