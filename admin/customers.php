<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$itemID         = (isset($_GET['itemID']) and intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$item           = array(); // Item Info Array
$items          = array(); // Items Array of items Info arrays
$userType       = USER_TYPE_USER;
$arFilters      = !empty($_GET['arFilters'])? $_GET['arFilters']: false;
$hasAccess      = $UserAccess->getAccessToModule($arrPageData['module']); 
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['headTitle']     = USERS_TITLE.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['filter_url']    = $arrPageData['admin_url'];
$arrPageData['filters']       = $arFilters;
$arrPageData['items_on_page'] = 20;
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
// Delete Item
if($itemID and $task=='deleteItem') {
    if($hasAccess) {
        $user = getItemRow(USERS_TABLE.' u', 'CONCAT_WS(" ", u.`firstname`, u.`surname`) `title`, (SELECT COUNT(`id`) FROM `'.ORDERS_TABLE.'` WHERE `user_id`=u.`id`) `orders_cnt`', 'WHERE u.`id`='.$itemID);
        if($user['orders_cnt'] > 0) {
            $arrPageData['errors'][] = '<font color="red">НЕ удалось удалить пользователя, так как он уже делал заказ на сайте</font>!';
        } else {
            if(deleteRecords(USERS_TABLE, 'WHERE `id`='.$itemID.' LIMIT 1')) {
                foreach(SystemComponent::getAcceptLangs() as $key => $arLang) {
                    ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$user['title'].'"', $key, $user['title'], 0, $arrPageData['module']);
                }
                Redirect($arrPageData['admin_url'].$arrPageData['page_url']);
            } else {
                $arrPageData['errors'][] = '<p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
            }
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}

// Insert Or Update Item in Database
elseif(!empty($_POST) and ($task=='addItem' or $task=='editItem')) {
    if($hasAccess) {
        $arUnusedKeys = array();
        $query_type   = $itemID ? 'update'            : 'insert';
        $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';
        
        // data validation
        $Validator->validateGeneral($_POST['firstname'], 'Firstname');
        if(!empty($_POST['phone']) && ($_POST['phone'] = PHPHelper::clearePhone($_POST['phone'])) && $Validator->validatePhone($_POST['phone'], 'Phone (wrong)')){
            $query = "SELECT `id` FROM `".USERS_TABLE."` WHERE `phone`='{$_POST['phone']}' ".($itemID ? ' AND `id`<>'.$itemID : '')." LIMIT 1";
            $result = mysql_query($query);
            if(mysql_num_rows($result)>0) {
                $Validator->addError("Данный номер телефона <u>{$_POST['email']}</u> уже используется в системе!");
            }
        } else {
            $Validator->addError("Введите корректный номер телефона!");
        }
        if(!empty($_POST['email']) && ($_POST['email'] = trim($_POST['email']))){
            if($Validator->validateEmail($_POST['email'], 'E-mail (wrong)')) {
                $query = "SELECT `email` FROM `".USERS_TABLE."` WHERE `email`='{$_POST['email']}' ".($itemID ? ' AND `id`<>'.$itemID : '')." LIMIT 1";
                $result = mysql_query($query);
                if(mysql_num_rows($result)>0) {
                    $Validator->addError("Данный email <u>{$_POST['email']}</u> уже используется в системе!");
                }
            } else {
                $Validator->addError("Введите корректный email!");
            }
        } else {
            $arUnusedKeys[] = 'email';
        }

        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            $arPostData = $_POST;
            $arPostData['login'] = PHPHelper::createLogin($arPostData['phone']); //?
            
            $result = $DB->postToDB($arPostData, USERS_TABLE, $conditions,  $arUnusedKeys, $query_type);
            if($result){
                if(!$itemID and $result and is_int($result)) {
                    $itemID = $result;
                }
                if(mysql_affected_rows()) {
                    $item_title = $arPostData['firstname'].($arPostData['surname'] ? ' '.$arPostData['surname'] : '');
                    if($task=='addItem'){
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "'.$item_title.'"', $key, $item_title, $itemID, $arrPageData['module']);
                    } else {
                         ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$item_title.'"', $lang, $item_title, $itemID, $arrPageData['module']);
                    }  
                } 
                setSessionMessage('Запись успешно сохранена!');
                Redirect($arrPageData['admin_url'].$arrPageData['page_url'].(isset($_POST['submit_add']) ? '&task=addItem' : ((isset($_POST['submit_apply']) and $itemID) ? '&task=editItem&itemID='.$itemID : '')) );
            } else {
                $arrPageData['errors'][] = 'Запись <font color="red">НЕ была сохранена</font>!';
            }
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
if($task == 'addItem' || $task == 'editItem') {
    $arrPageData['headScripts'][] = '<script src="/js/libs/jquery.inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>';

    if(!$itemID){
        $item = array_combine_multi($DB->getTableColumnsNames(USERS_TABLE), '');
        $item['arHistory'] = $item['orders'] = array();
    } elseif($itemID) {
        $query = "SELECT u.* FROM ".USERS_TABLE." u WHERE u.`id`=$itemID AND u.`type`='{$userType}' LIMIT 1";
        $result = mysql_query($query);
        if(!$result || !mysql_num_rows($result)) {
            setSessionErrors("Ошибка! Пользователь не найден!");
            Redirect($arrPageData['admin_url']);
        } else {
            $item = mysql_fetch_assoc($result);
            $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));

            // Customer orders
            $item['orders'] = array();
            $query = 'SELECT o.*, os.`title` `status_title`, os.`color_hex` FROM `'.ORDERS_TABLE.'` o LEFT JOIN `'.ORDER_STATUS_TABLE.'` os ON os.`id`=o.`status_id` WHERE o.`user_id`='.$itemID.' ORDER BY o.`created` DESC';
            $result = mysql_query($query);
            if($result && mysql_num_rows($result) > 0) {
                while (($row = mysql_fetch_assoc($result))) {
                    $row['products'] = getRowItems(ORDER_PRODUCTS_TABLE, '*', '`order_id`='.$row['id']);
                    $item['orders'][] = $row;
                }
            }
        }
    }

    if(!empty($_POST)) $item = array_merge($item, $_POST);
    
} else {
    $arrPageData['headCss'][] = '<link href="/js/jquery/themes/smoothness/jquery.ui.all.css" type="text/css" rel="stylesheet" />';

    $where = "WHERE u.`type`='{$userType}' ";
    if($arFilters) {
        if(!empty($arFilters['title'])) {
            $searchstr = PHPHelper::prepareSearchText($arFilters['title']);
            $arParts = explode(' ', $searchstr);
            foreach ($arParts as $part) {
                if(($part = trim($part))) {
                    $where .= 'AND (u.`firstname` LIKE "%'.$part.'%" OR u.`surname` LIKE "%'.$part.'%" OR u.`phone` LIKE "%'.$part.'%" OR u.`email` LIKE "%'.$part.'%")';
                }
            }
            $arrPageData['filter_url'] .= '&arFilters[title]='.$searchstr;
        }
    } else {
        $arrPageData['filter_url'] = '';
    }
    
    // Total pages and Pager
    $arrPageData['total_items'] = intval(getValueFromDB(USERS_TABLE." u", 'COUNT(*)', $where, 'count'));
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url']);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager

    $order = "ORDER BY u.`id` DESC";
    $limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";

    $query = "SELECT u.* FROM ".USERS_TABLE." u $where $order $limit";
    $result = mysql_query($query);
    if($result) {
        while (($row = mysql_fetch_assoc($result))) {
            $items[] = $row;
        }
    } else {
        $arrPageData['errors'][] = "SELECT Users OPERATIONS: " . mysql_error();
    }
}
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item',         $item);
$smarty->assign('items',        $items);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################