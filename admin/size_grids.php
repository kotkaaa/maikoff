<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

// SET from $_GET Global Array Item ID Var = integer
$itemID        = (isset($_GET['itemID']) and intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$copyID        = (isset($_GET['copyID']) and intval($_GET['copyID'])) ? intval($_GET['copyID']) : 0;
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$arCidCntItems = array();
$hasAccess     = $UserAccess->getAccessToModule($arrPageData['module']);
$item_title    = $itemID ? getValueFromDB(SIZE_GRIDS_TABLE, 'title', 'WHERE `id`='.$itemID) : '';

$arrPageData['itemID']        = $itemID;
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['category_url'].$arrPageData['page_url'];
$arrPageData['arrBreadCrumb'] = array();
$arrPageData['arrParent']     = array();
$arrPageData['headTitle']     = SIZE_GRIDS_TABLE.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url'], true);
$arrPageData['items_on_page'] = 20;
// SET Reorder
if ($task=='reorderItems' and !empty($_POST)) {
    if ($hasAccess) {
        $result = reorderItems($_POST['arOrder'], 'order', 'id', SIZE_GRIDS_TABLE,  array('action'=>ActionsLog::ACTION_EDIT, 'comment'=>'Изменена сортировка', 'lang'=>$lang, 'module'=>$arrPageData['module']));
        if ($result===true) $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
        elseif ($result)    $arrPageData['errors'][] = $result;
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Delete Item
elseif ($itemID and $task=='deleteItem') {
    if ($hasAccess) {
        $result = deleteDBLangsSync(SIZE_GRIDS_TABLE, ' WHERE id='.$itemID);
        if (!$result) $arrPageData['errors'][] = 'Данные не удалось удалить. Возможная причина - <p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
        elseif ($result) {
            foreach (SystemComponent::getAcceptLangs() as $key => $arLang)
                ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item_title.'"', $key, $item_title, 0, $arrPageData['module']);
            Redirect($arrPageData['current_url']);
        }
    } else $arrPageData['errors'][] = $UserAccess->getAccessError();
}
// Set Active Status Item
elseif ($itemID and $task=='publishItem' and isset($_GET['status'])) {
    if ($hasAccess) {
        $result = updateRecords(SIZE_GRIDS_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
        if ($result === false) $arrPageData['errors'][] = 'Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error();
        elseif ($result) {
            $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
            ActionsLog::getInstance()->save(ActionsLog::ACTION_PUBLICATION, 'Изменена публикация на "'.($_GET['status']==1 ? 'Опубликовано' : 'Неопубликовано' ).'"', $lang, $item_title, $itemID, $arrPageData['module']);
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
//Copy item
elseif($copyID and $task=='addItem'){
    if ($hasAccess) {
        $arrPageData['messages'][] = 'Запись успешно скопирована!';
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Insert Or Update Item in Database
elseif (!empty($_POST) and ($task=='addItem' or $task=='editItem')) {
    if ($hasAccess) {
        $arUnusedKeys = array();
        $query_type   = $itemID ? 'update'              : 'insert';
        $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';
        $Validator->validateGeneral($_POST['title'], 'Вы не ввели названия страницы!!!');
        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            // copy post data
            $arPostData = $_POST;
            $result = $DB->postToDB($arPostData, SIZE_GRIDS_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
            if ($result){
                if (!$itemID and $result and is_int($result)) $itemID = $result;
                if (mysql_affected_rows()) {
                    $item_title = getValueFromDB(SIZE_GRIDS_TABLE, 'title', 'WHERE `id`='.$itemID);
                    if ($task=='addItem') {
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "'.$item_title.'"', $key, $item_title, $itemID, $arrPageData['module']);
                    } else {
                         ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$item_title.'"', $lang, $item_title, $itemID, $arrPageData['module']);
                    }  
                }
                setSessionMessage('Запись успешно сохранена!');
                Redirect($arrPageData['current_url'].(isset($_POST['submit_add']) ? '&task=addItem' : ((isset($_POST['submit_apply']) and $itemID) ? '&task=editItem&itemID='.$itemID : '')) );
            } else {
                $arrPageData['errors'][] = 'Запись <font color="red">НЕ была сохранена</font>!';
            }
        }
    } else $arrPageData['errors'][] = $UserAccess->getAccessError();
}
// Sorts and Filters block
$arrOrder = getOrdersByKeyExplodeFilteredArray($_GET, 'pageorder', '_');
$arrPageData['filter_url'] = !empty($arrOrder['url']) ? '&'.implode('&', $arrOrder['url']) : '';

if ($task=='addItem' or $task=='editItem'){
    if (!$itemID){
        if ($hasAccess) {
            if ($copyID){
                $item = getSimpleItemRow($copyID, SIZE_GRIDS_TABLE);
                $item = array_merge($item, array('id'=>''));
            } else $item = array_combine_multi($DB->getTableColumnsNames(SIZE_GRIDS_TABLE), '');
            $item['arHistory'] = array();
        } else {
            setSessionErrors($UserAccess->getAccessError()); 
            Redirect($arrPageData['current_url']);
        }
    } elseif ($itemID) {
        $query = "SELECT * FROM ".SIZE_GRIDS_TABLE." WHERE id = $itemID LIMIT 1";
        $result = mysql_query($query);
        if (!$result) $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
        elseif (!mysql_num_rows($result)) $arrPageData['errors'][] = "SELECT OPERATIONS: No this Item in DataBase";
        else {
            $item = mysql_fetch_assoc($result);
            $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
        }
    }
    if (!empty($_POST)) $item = array_merge($item, $_POST);

    $arrPageData['arrBreadCrumb'][] = array('title'=>($task=='addItem' ? ADMIN_ADD_NEW_PAGE : ADMIN_EDIT_PAGE));
} else {
    // Display Items List Data
    $where = "";
    // Total pages and Pager
    $arrPageData['total_items'] = intval(getValueFromDB(SIZE_GRIDS_TABLE." t", 'COUNT(*)', $where, 'count'));
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['category_url'].$arrPageData['filter_url']);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager
    $order  = "";
    $limit  = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";
    $query  = "SELECT t.* FROM `".SIZE_GRIDS_TABLE."` t $where $order $limit";
    $result = mysql_query($query);
    if (!$result) $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
    else {
        while ($row = mysql_fetch_assoc($result)) {
            $items[] = $row;
        }
    }
}

$smarty->assign('item',          $item);
$smarty->assign('items',         $items);
$smarty->assign('arCidCntItems', $arCidCntItems);
/*
DROP TABLE IF EXISTS `ru_size_grids`;
CREATE TABLE `ru_size_grids` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
 */