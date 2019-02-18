<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// MODULE DATA VERIFICATION \\\\\\\\\\\\\\\\\\\\\\\\\\\
if(!$arrPageData['moduleRootID']) {
    $arrPageData['errors'][]    = sprintf(ADMIN_MODULE_ID_ERROR, 'Принты', $arrPageData['module']);
    $arrPageData['module']      = 'module_messages';
    $arrPageData['moduleTitle'] = 'Принты';
    return;
} else {
    foreach($arAcceptLangs as $ln) {
        $dbTable = replaceLang($ln, PRINTS_TABLE);
        if(!$DB->isSetDBTable($dbTable)){
            $arrPageData['errors'][]    = sprintf(ADMIN_MODULE_TABLE_ERROR, 'Принты', $arrPageData['module'], $dbTable);
            $arrPageData['module']      = 'module_messages';
            $arrPageData['moduleTitle'] = 'Принты';
            return;
        }
    }
}
// /////////////////////// END MODULE DATA VERIFICATION \\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$category_id   = (isset($_GET['cid']) AND intval($_GET['cid']))      ? intval($_GET['cid'])    : 0;
$itemID        = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$filters       = !empty($_GET['filters'])? $_GET['filters']: array();
$hasAccess     = $UserAccess->getAccessToModule($arrPageData['module']); 
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['items_on_page'] = 20;
$arrPageData['category_id']   = $category_id;
$arrPageData['itemID']        = $itemID;
$arrPageData['filters']       = $filters;
$arrPageData['headTitle']     = 'Принты'.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['current_url']   = $arrPageData['admin_url'].($category_id ? '&cid='.$category_id : '').($filters ? '&'.http_build_query(array('filters' => $filters)) : '');
$arrPageData['categoryTree']  = getCategoriesTree($lang, $arrModules['prints']['pid'], 0, false, $module, '', '', array(), PRINTS_TABLE, 'category_id');
$arrPageData['arSubstrates']  = getRowItemsInKey('id', SUBSTRATES_TABLE, '*');
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
require_once 'include/classes/product/PrintProduct.php';
PrintProduct::initSpool();
// SET Reorder
if($task=='reorderItems' && !empty($_POST['arItems']) && empty($_POST['arCheckedItems'])) {
    if($hasAccess) {
        $error = false;
        foreach($_POST['arItems'] as $pid => $value) {
            $result = updateRecords(PRINTS_TABLE, '`order`='.$value, 'WHERE `id`='.$pid);
            if($result === false) {
                $error = true;
                break;
            } else {
                ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Обновление сортировки принта ['.$pid.']', $lang, '', 0, $module);  
            }
        }        
        setSessionMessage($error ? 'Ошибка смены сортировки!' : 'Новая сортировка успешно сохранена!');
        Redirect($arrPageData['current_url']);
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Delete Item
elseif($itemID && $task=='deleteItem') {
    if($hasAccess){
        if(PHPHelper::deletePrint($itemID)) {
            PrintProduct::deleteSpoolByItem($itemID);
            setSessionMessage('Принт удален!');            
        } else {
            setSessionErrors('Ошибка удаления принта!');
        }
        Redirect($arrPageData['current_url']);
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Set Active Status Item
elseif($itemID && $task=='publishItem' && isset($_GET['status'])) {
    if($hasAccess) {
        $result = updateRecords(PRINTS_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
        if($result===false) {
            setSessionErrors('Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error());
        } elseif($result) {
            setSessionMessage('Новое состояние успешно сохранено!');
            ActionsLog::getInstance()->save(ActionsLog::ACTION_PUBLICATION, 'Изменена публикация страницы на "'.($_GET['status']==1 ? 'Опубликована' : 'Неопубликована' ).'"', $lang, getValueFromDB(PRINTS_TABLE, 'title', 'WHERE `id`='.$itemID), $itemID, $arrPageData['module']);
        }
        Redirect($arrPageData['current_url']);
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
//массовое изменение
elseif($task=='reorderItems' && !empty($_POST['arCheckedItems'])) {
    if($hasAccess) {
        $arItems = $_POST['arCheckedItems'];     
        if($_POST['allitems'] == 'delete'){
            $error = false;
            foreach($arItems as $itemID => $val) {
                if(PHPHelper::deletePrint($itemID)) {
                    PrintProduct::deleteSpoolByItem($itemID);
                } else {
                    $error = true;
                    break;
                }
            } 
            setSessionMessage($error ? 'Ошибка удаления, не все принты удалены!' : 'Принты успешно удалены!');
        } 
        else if($_POST['allitems'] == 'publish' || $_POST['allitems'] == 'unpublish') {
            //переопределяем значения на 0 для отключения
            if($_POST['allitems'] == 'unpublish') {
                $arItems = array_fill_keys(array_keys($arItems), 0);
            }            
            $result = updateItems(array('active'=>$arItems), $arItems, 'id', PRINTS_TABLE, array('action'=>ActionsLog::ACTION_EDIT, 'comment'=>'Изменена публикация на '.($_POST['allitems']=='publish' ? '"Опубликовано"' : '"Неопубликовано"'), 'lang'=>$lang, 'module'=>$arrPageData['module']));
            if($result === true) setSessionMessage('Новое состояние успешно сохранено!');
            elseif($result === false) setSessionMessage('Не нуждается в сохранении!');
            else  setSessionErrors($result);
        }        
        Redirect($arrPageData['current_url']);
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
else if($itemID && $task == 'getLogos') {
    $item = getSimpleItemRow($itemID, PRINTS_TABLE);
    $item['arLogos'] = getRowItems(PRINTFILES_TABLE, '*, IF(`id`="'.$item['file_id'].'", 1, 0) `isdefault`', '`print_id`='.$itemID);
    $smarty->assign('item', $item);   
    $smarty->assign('arrPageData', $arrPageData); 
    $json['output'] = $smarty->fetch('ajax/logos.tpl');
    echo json_encode($json);
    exit();
}
else if($itemID && $task == 'updateAssortment') {
    $json = array();
    $substrateID = isset($_GET['substrateID']) && intval($_GET['substrateID']) ? $_GET['substrateID'] : 0;
    $assortID = isset($_GET['assortID']) && intval($_GET['assortID']) ? $_GET['assortID'] : 0;
    if(!empty($_POST['arAssort'])) {         
        $arAssort = $_POST['arAssort'];
        $arAssort[$substrateID]['isdefault'] = isset($_POST['substrate_id']) && $_POST['substrate_id'] == $substrateID ? 1 : 0;
        PrintProduct::saveActiveAssortments($itemID, $arAssort, $arrPageData['arSubstrates'], getValueFromDB(PRINTS_TABLE, 'seo_path', 'WHERE id='.$itemID));
        $json['seo_path'] = getValueFromDB(PRINT_ASSORTMENT_TABLE, 'seo_path', 'WHERE `print_id`='.$itemID.' AND `substrate_id`='.$substrateID);
    //отключаем подложку и все ее данные
    } else if ($assortID) {        
        updateRecords(PRINT_ASSORTMENT_TABLE, '`active`=0', 'WHERE `id`='.$assortID);
        //updateRecords(PRINT_ASSORTMENT_COLORS_TABLE, '`active`=0', 'WHERE `assortment_id`='.$assortID);
        //updateRecords(PRINT_ASSORTMENT_SETTINGS_TABLE, '`active`=0', 'WHERE `assortment_id`='.$assortID);
    } 
    echo json_encode($json);
    exit();
}
// Insert Or Update Item in Database
elseif (!empty($_POST) AND ($task=='addItem' OR $task=='editItem')) {
    if ($hasAccess) {
        $arUnusedKeys = array('id', 'old_seo_path');
        $query_type   = $itemID ? 'update'            : 'insert';
        $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';

        //валидация данных
        $Validator->validateGeneral($_POST['title'], 'Вы не ввели название принта!!!');
        //проверка кода товара на наличие и уникальность
        if(empty($_POST['pcode']) || getValueFromDB(PRINTS_TABLE, 'COUNT(id)', 'WHERE `pcode`="'.$_POST['pcode'].'"'.($itemID ? ' AND `id`<>'.$itemID : ''), 'cnt')>0) {
            $Validator->addError(empty($_POST['pcode']) ? 'Вы не ввели код принта!' : 'Код "'.$_POST['pcode'].'" уже используется в принтах!');
        }
        //валидация сеопути
        $_POST['seo_path'] = $UrlWL->strToUrl(empty($_POST['seo_path']) ? $_POST['title'] : $_POST['seo_path']);
        if($Validator->validateGeneral($_POST['title'], 'Вы не ввели сеопуть принта!') && getValueFromDB(PRINTS_TABLE, 'COUNT(id)', 'WHERE `seo_path`="'.$_POST['seo_path'].'"'.($itemID ? ' AND `id`<>'.$itemID : ''), 'cnt')>0) {
            $Validator->addError('Сеопуть "'.$_POST['seo_path'].'" уже используется в принтах!');
        }
        //проверка на выбранный хотя бы 1 подложку
        if(empty($_POST['arAssort'])) {
            $Validator->addError('Выберите хотя бы 1 подложку товаров!');
        } 
        //дефолтная подложка 
        if(empty($_POST['substrate_id'])) {
            $Validator->addError('Установите дефолтную подложку для товаров!');
        }
        
        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            // copy post data
            $arPostData = $_POST;
            if (empty($arPostData['createdDate'])) $arPostData['createdDate'] = date('Y-m-d');
            if (empty($arPostData['createdTime'])) $arPostData['createdTime'] = date('H:i:s');
            $arPostData['created'] = "{$arPostData['createdDate']} {$arPostData['createdTime']}";
            $arPostData["old_seo_path"] = $itemID ? getValueFromDB(PRINTS_TABLE, 'seo_path', 'WHERE `id`='.$itemID) : '';
            $result = $DB->postToDB($arPostData, PRINTS_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
            if ($result){
                if (!$itemID AND $result AND is_int($result)) $itemID = $result;
                if (mysql_affected_rows()) {
                    if ($arrPageData['task'] == 'addItem'){
                        ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "'.$arPostData['title'].'"', SystemComponent::getAcceptLangs(), $arPostData['title'], $itemID, $arrPageData['module']);
                    } else {
                        ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$arPostData['title'].'"', $lang, $arPostData['title'], $itemID, $arrPageData['module']);
                    }
                    if($arPostData["old_seo_path"] && $arPostData["old_seo_path"] != $arPostData['seo_path']){
                        PrintProduct::updateAssortmentSeoPathes(0, $itemID);
                    }
                } 
                setSessionMessage('Запись успешно сохранена!');
                // сохранение атрибутов
                PHPHelper::saveAttributes($itemID, $DB, PRINT_ATTRIBUTES_TABLE, 'pid', $arPostData);     
                // сохранение ярлыков
                $arValuesIDX = array(0);
                if(!empty($arPostData['categories'])) {
                    foreach ($arPostData['categories'] as $catID) {
                        if($catID != $arPostData['category_id']) {
                            if(!($shortcutID = (int)getValueFromDB(SHORTCUTS_TABLE, "id", "WHERE `pid`='{$itemID}' AND `cid`='{$catID}'"))) {
                                $resID = $DB->postToDB(array(
                                    'pid'     => $itemID,
                                    'cid'     => $catID,                                
                                    'lang'    => $lang,
                                    'module'  => $module,
                                    'active'  => 1,
                                    'order'   => getMaxPosition(0, 'order', 'id', SHORTCUTS_TABLE),
                                    'created' => date('Y-m-d H:i:s'),                                
                                ), SHORTCUTS_TABLE);
                                if ($resID && is_int($resID)) {
                                    $shortcutID = $resID;                                
                                }
                            }
                            if($shortcutID) $arValuesIDX[] = $shortcutID;
                        }
                    }
                }
                deleteDBLangsSync(SHORTCUTS_TABLE, "WHERE `pid`={$itemID} AND `id` NOT IN(".implode(",", $arValuesIDX).")");
                //сохранение подложек и цен                
                PrintProduct::saveActiveAssortments($itemID, $arPostData['arAssort'], $arrPageData['arSubstrates'], $arPostData['seo_path'], $arPostData['substrate_id']);
                
                Redirect($arrPageData['current_url'].(isset($_POST['submit_add']) ? '&task=addItem' : ((isset($_POST['submit_apply']) AND $itemID) ? '&task=editItem&itemID='.$itemID : '')) );
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
if ($task=='addItem' OR $task=='editItem'){
    if (!$hasAccess) {
        setSessionErrors($UserAccess->getAccessError()); 
        Redirect($arrPageData['current_url']);
    }       
        
    $arrPageData['headCss'][]     = '<link href="/js/jquery/themes/smoothness/jquery.ui.all.css" type="text/css" rel="stylesheet" />';
    $arrPageData['headScripts'][] = '<script src="/js/jquery/ui/jquery.ui.core.js" type="text/javascript"></script>';    
    // select2 for attributes
    $arrPageData['headCss'][]     = '<link href="/js/libs/select2/select2.css" type="text/css" rel="stylesheet"/>';
    $arrPageData['headScripts'][] = '<script src="/js/libs/select2/select2.js" type="text/javascript"></script>';
    $arrPageData['headScripts'][] = '<script src="/js/libs/select2/select2_locale_ru.js" type="text/javascript"></script>';        
    
    $arrPageData['arCategories']  = getRowItems(MAIN_TABLE, '*', 'module="'.$module.'" AND id<>'.$arrModules['prints']['id']);
    $arrPageData['arCidCntItems'] = getItemsCountInCategories('category_id', 'count', PRINTS_TABLE, '`category_id`,COUNT(`category_id`) as `count`', 'WHERE `active`=1 GROUP BY `category_id`');  
    $arrPageData['arSides']       = PrintProduct::getSides();
    
    PHPHelper::prepareAttrGroups($arrPageData);   

    if(!$itemID){
        $item = array_combine_multi($DB->getTableColumnsNames(PRINTS_TABLE), '');  
        $item['active'] = 1;
        $item['editableSide'] = true;
        $item['category_id'] = 0;
        $item['order']  = getMaxPosition(0, 'order', 'id', PRINTS_TABLE);        
        $item['createdDate'] = date('Y-m-d');
        $item['createdTime'] = date('H:i:s');
        $item['attributes'] = $item['categories'] = $item['arLogos'] = $item['attrGroups'] = $item['arHistory'] = array();
    } elseif($itemID) {
        $query = "SELECT p.*, "
                . "(SELECT IF(COUNT(`id`)>0, 0, 1) FROM ".PRINT_ASSORTMENT_TABLE." WHERE `print_id`=p.`id`) `editableSide` "
                . "FROM ".PRINTS_TABLE." p WHERE p.`id` = $itemID LIMIT 1";
        $result = mysql_query($query);
        if(!$result) {
            setSessionErrors("SELECT OPERATIONS: " . mysql_error());
            Redirect($arrPageData['admin_url']);
        } elseif(!mysql_num_rows($result)) {
            setSessionErrors('Запись не найдена!');
            Redirect($arrPageData['admin_url']);
        } else {
            $item = mysql_fetch_assoc($result);         
            $item['createdDate'] = date('Y-m-d', strtotime($item['created']));
            $item['createdTime'] = date('H:i:s', strtotime($item['created']));
            $item['categories'] = getArrValueFromDB(SHORTCUTS_TABLE, 'cid', 'WHERE pid='.$item['id']);
            $item['placement_title'] = ($side = PrintProduct::getSides($item['placement'])) ? $side['title'] : '';
            // prints
            $item['arLogos'] = getRowItems(PRINTFILES_TABLE, '*, IF(`id`="'.$item['file_id'].'", 1, 0) `isdefault`', '`print_id`='.$itemID);
            // item attributes
            PHPHelper::prepareItemAttributes($item, PRINT_ATTRIBUTES_TABLE, 'pid', $arrPageData['attrGroups']);
            $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
        }
    }
    
    if (!empty($_POST)) $item = array_merge($item, $_POST);
    
    //product assort
    $select = 'st.`id` `substrate_id`, st.`active` `substrate_active`, IFNULL(pa.`id`, 0) `id`, IFNULL(pa.`price`, st.`price`) `price`, IFNULL(pa.`seo_path`, "") `seo_path`, IFNULL(pa.`order`, 0) `order`, IFNULL(pa.`isdefault`, 0) `isdefault`, IFNULL(pa.`active`, 0) `active`';
    $item['arAssort'] = getRowItemsInKey('substrate_id', SUBSTRATES_TABLE.' st LEFT JOIN '.PRINT_ASSORTMENT_TABLE.' pa ON pa.`substrate_id`=st.`id` AND pa.`print_id`='.$itemID, $select);   
    if(!empty($_POST) && !empty($_POST['arAssort'])) {
        foreach($_POST['arAssort'] as $substrateID => $assort) {
            $item['arAssort'][$substrateID] = array_merge($item['arAssort'][$substrateID], $assort);
        }
    }
   
// items list
} else {
    $arrPageData['headCss'][] = '<link href="/js/jquery/themes/smoothness/jquery.ui.all.css" type="text/css" rel="stylesheet" />';
    
    // Create Order Links
    $where = 'WHERE p.id>0';
    
    if($category_id) {
        $arCids = getChildrensIDs($category_id);
        $arCids[] = $category_id;
        $where .= ' AND p.category_id IN ('.implode(',', $arCids).')';
    }    
    if (!empty($filters) && !empty($filters['title'])) {
        $search = PHPHelper::prepareSearchText($filters['title'], true);
        $where .= ' AND (p.`title` LIKE "%'.$search.'%" OR p.`pcode` LIKE "%'.$search.'%") ';           
    }
    
    // Total pages and Pager
    $arrPageData['total_items'] = intval(getValueFromDB(PRINTS_TABLE." p", 'COUNT(*)', $where, 'count'));
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['current_url'].$arrPageData['filter_url']);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager

    $order  = "ORDER BY ".(!empty($arrOrder['mysql']) ? implode(', ', $arrOrder['mysql']) : "p.order");
    $limit  = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";
    
    $query = 'SELECT p.*, m.`title` `category`, CONCAT("'.UPLOAD_URL_DIR.$module.'/", pf.`filename`) `default_image` 
              FROM '.PRINTS_TABLE.' p             
              LEFT JOIN '.PRINTFILES_TABLE.' pf ON pf.`print_id`=p.`id` AND pf.`id`=p.`file_id` 
              LEFT JOIN '.MAIN_TABLE.' m ON p.`category_id`=m.`id` 
              '.$where.' '.$order.' '.$limit;
    if (($result = mysql_query($query))) {
        while (($row = mysql_fetch_assoc($result))) {
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