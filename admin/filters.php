<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access


# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$itemID        = (isset($_GET['itemID']) and intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$copyID        = (isset($_GET['copyID']) and intval($_GET['copyID'])) ? intval($_GET['copyID']) : 0;
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$hasAccess     = $UserAccess->getAccessToModule($arrPageData['module']); 
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['copyID']        = $copyID;
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['page_url'];
$arrPageData['headTitle']     = FILTERS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['items_on_page'] = 20;
$arrPageData['arTypes']       = getComplexRowItems(FILTER_TYPES_TABLE, '*', 'WHERE `active`>0', '`order` ASC');
$arrPageData['attrGroups']    = getComplexRowItems(ATTRIBUTE_GROUPS_TABLE, '*', '', '`order`');
if(!empty($arrPageData['attrGroups'])) {
    foreach ($arrPageData['attrGroups'] as $key => $value) {
        $arrPageData['attrGroups'][$key]['attributes'] = getComplexRowItems(ATTRIBUTES_TABLE, '*', 'WHERE `gid`='.$arrPageData['attrGroups'][$key]['id'], '`order`');
    }
}
// SET Reorder
$item_title = $itemID ?  getValueFromDB(FILTERS_TABLE, 'title', 'WHERE `id`='.$itemID) : '';
if ($task=='reorderItems' and !empty($_POST)) {
    if($hasAccess) {
        $result = reorderItems($_POST['arOrder'], 'order', 'id', FILTERS_TABLE, array('action'=>ActionsLog::ACTION_EDIT, 'comment'=>'Изменена сортировка', 'lang'=>$lang, 'module'=>$arrPageData['module']));
        if($result===true) {
            $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
        } elseif($result) {
            $arrPageData['errors'][] = $result;
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Delete Item
elseif($itemID and $task=='deleteItem') {
    if($hasAccess) {
        $result = deleteDBLangsSync(FILTERS_TABLE, ' WHERE id='.$itemID);
        if (!$result) {
            $arrPageData['errors'][] = 'Данные не удалось удалить. Возможная причина - <p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
        } elseif($result) {
            foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item_title.'"', $key, $item_title, 0, $arrPageData['module']);
            deleteDBLangsSync(RANGES_TABLE, 'WHERE `fid`='.$itemID);
            Redirect($arrPageData['current_url']);
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Set Active Status Item
elseif ($itemID and $task=='publishItem' and isset($_GET['status'])) {
    if ($hasAccess) {
        $result = updateRecords(FILTERS_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
        if ($result===false) {
            $arrPageData['errors'][]   = 'Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error();
        } elseif($result) {
            ActionsLog::getInstance()->save(ActionsLog::ACTION_PUBLICATION, 'Изменена публикация на "'.($_GET['status']==1 ? 'Опубликовано' : 'Неопубликовано' ).'"', $lang, $item_title, $itemID, $arrPageData['module']);
            $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
//Copy item
elseif ($copyID and $task=='addItem'){
    if ($hasAccess) {
        $arrPageData['messages'][] = 'Запись успешно скопирована!';
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Insert Or Update Item in Database
elseif (!empty($_POST) and ($task=='addItem' OR $task=='editItem')) {
    if ($hasAccess) {
        $arUnusedKeys = array();
        $query_type   = $itemID ? 'update'            : 'insert';
        $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';
        $Validator->validateGeneral($_POST['title'], 'Вы не ввели названия страницы!!!');
        $Validator->validateGeneral($_POST['order'], 'Вы не ввели порядковый номер страницы!!!');
        // operation with filter types
        if(UrlFilters::isIndependentFilter($_POST['tid'])) {
            $_POST['aid'] = 0;
        }
        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            $arPostData = $_POST;
            $result = $DB->postToDB($arPostData, FILTERS_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
            if($result){
                if(!$itemID and $result and is_int($result)) {
                    $itemID = $result;
                }
                if(mysql_affected_rows()) {
                    $item_title = getValueFromDB(FILTERS_TABLE, 'title', 'WHERE `id`='.$itemID);
                    if($task=='addItem'){
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "'.$item_title.'"', $key, $item_title, $itemID, $arrPageData['module']);
                    } else {
                         ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$item_title.'"', $lang, $item_title, $itemID, $arrPageData['module']);
                    }  
                }
                // operation with ranges
                $rangesIDX = array();
                if (isset($arPostData['is_range']) AND ($arPostData['tid']==UrlFilters::TYPE_PRICE OR $arPostData['tid']==UrlFilters::TYPE_NUMBER)) {
                    if (!empty($arPostData['arRanges'])) {
                        foreach ($arPostData['arRanges'] as $arRange) {
                            if(!empty($arRange['title']) and (!empty($arRange['vmin']) OR !empty($arRange['vmax']))) {
                                $arRange['fid'] = $itemID;
                                $qtype = !empty($arRange['id']) ? "update" : "insert";
                                $where = !empty($arRange['id']) ? "WHERE `id`={$arRange['id']}" : "";
                                $resID = $DB->postToDB($arRange, RANGES_TABLE, $where, array("id"), $qtype);
                                if ($resID AND is_int($resID)) $arRange['id'] = $resID;
                            }
                            if (!empty($arRange['id'])) $rangesIDX[] = $arRange['id'];
                        }
                    }
                }
                deleteDBLangsSync(RANGES_TABLE, 'WHERE `fid`='.$itemID.(!empty($rangesIDX) ? ' AND `id` NOT IN('.implode(",", $rangesIDX).')' : ""));
                setSessionMessage('Запись успешно сохранена!');
                Redirect($arrPageData['current_url'].(isset($_POST['submit_add']) ? '&task=addItem' : ((isset($_POST['submit_apply']) and $itemID) ? '&task=editItem&itemID='.$itemID : '')) );
            } else {
                $arrPageData['errors'][] = 'Запись <font color="red">НЕ была сохранена</font>!';
            }
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Sorts and Filters block
$arrOrder = getOrdersByKeyExplodeFilteredArray($_GET, 'pageorder', '_');
$arrPageData['filter_url'] = !empty($arrOrder['url']) ? '&'.implode('&', $arrOrder['url']) : '';
if ($task=='addItem' OR $task=='editItem'){
    if (!$itemID){
        if ($hasAccess) {
            if ($copyID){
                $item = getSimpleItemRow($copyID, FILTERS_TABLE);
                $item = array_merge($item, array('id'=>''));
                $item['arRanges'] = getComplexRowItems(RANGES_TABLE.' r', 'r.*', 'WHERE r.`fid`='.$copyID, 'r.`order`');
            } else {    
                $item = array_combine_multi($DB->getTableColumnsNames(FILTERS_TABLE), '');
                $item['arRanges'] = array();
                $item['tid'] = UrlFilters::TYPE_BRAND;
            }
            $item['order']  = getMaxPosition(null, 'order', 'cid', FILTERS_TABLE);
            $item['active'] = 1;
            $item['arHistory'] = array();
        } else {
            setSessionErrors($UserAccess->getAccessError()); 
            Redirect($arrPageData['current_url']);
        }
    } elseif($itemID) {
        $query = "SELECT * FROM ".FILTERS_TABLE." WHERE id = $itemID LIMIT 1";
        $result = mysql_query($query);
        if(!$result) {
            $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
        } elseif(!mysql_num_rows($result)) {
            $arrPageData['errors'][] = "SELECT OPERATIONS: No this Item in DataBase";
        } else {
            $item = mysql_fetch_assoc($result);           
            $item['arRanges'] = getComplexRowItems(RANGES_TABLE.' r', 'r.*', 'WHERE r.`fid`='.$itemID, 'r.`order`');
            $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
        }
    } if (!empty($_POST)) $item = array_merge($item, $_POST);

} else {
    // Create Order Links
    $arrPageData['arrOrderLinks'] = getOrdersLinks(
            array('default'=>HEAD_LINK_SORT_DEFAULT, 'title'=>HEAD_LINK_SORT_TITLE, 'created'=>HEAD_LINK_SORTDATEADD),
            $arrOrder['get'], $arrPageData['admin_url'], 'pageorder', '_');
    // Display Items List Data
    $where = "";
    // Total pages and Pager
    $arrPageData['total_items'] = intval(getValueFromDB(FILTERS_TABLE." t", 'COUNT(*)', $where, 'count'));
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['filter_url']);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager
    $order  = "ORDER BY ".(!empty($arrOrder['mysql']) ? 't.'.implode(', t.', $arrOrder['mysql']) : "t.order, t.id");
    $limit  = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";
    $query  = "SELECT t.*, (SELECT `title` FROM `".FILTER_TYPES_TABLE."` WHERE `id`=t.`tid`) AS `typename` "
            . "FROM `".FILTERS_TABLE."` t $where $order $limit";
    $result = mysql_query($query);
    if (!$result) $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
    else {
        while ($row = mysql_fetch_assoc($result)) {
            $items[]           = $row;
        }
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


/*
DROP TABLE IF EXISTS `ru_homeslider`;
CREATE TABLE IF NOT EXISTS `ru_homeslider` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `descr` tinytext NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `order` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_title` (`title`),
  KEY `idx_order` (`order`),
  KEY `idx_active` (`active`),
  KEY `idx_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
 */