<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// MODULE DATA VERIFICATION \\\\\\\\\\\\\\\\\\\\\\\\\\\
if (!$arrPageData['moduleRootID']) {
    $arrPageData['errors'][]    = sprintf(ADMIN_MODULE_ID_ERROR, CATALOG, $arrPageData['module']);
    $arrPageData['module']      = 'module_messages';
    $arrPageData['moduleTitle'] = CATALOG;
    return;
} else {
    foreach($arAcceptLangs as $ln) {
        $dbTable = replaceLang($ln, CATALOG_TABLE);
        if(!$DB->isSetDBTable($dbTable)){
            $arrPageData['errors'][]    = sprintf(ADMIN_MODULE_TABLE_ERROR, CATALOG, $arrPageData['module'], $dbTable);
            $arrPageData['module']      = 'module_messages';
            $arrPageData['moduleTitle'] = CATALOG;
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
$itemID        = (isset($_GET['itemID']) AND intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$modelID       = (isset($_GET['modelID']) AND intval($_GET['modelID'])) ? intval($_GET['modelID']) : 0;
$filters       = !empty($_GET['filters'])? $_GET['filters']: array();
$hasAccess     = $UserAccess->getAccessToModule($arrPageData['module']); 
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['items_on_page'] = 20;
$arrPageData['itemID']        = $itemID;
$arrPageData['modelID']       = $modelID;
$arrPageData['filters']       = $filters;
$arrPageData['arModel']       = getSimpleItemRow($modelID, MODELS_TABLE);
$arrPageData['headTitle']     = CATALOGS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['admin_url']     = $arrPageData['admin_url'].('&modelID='.$modelID);
$arrPageData['current_url']   = $arrPageData['admin_url'].($filters ? '&'.http_build_query(array('filters' => $filters)) : '');
if(!$modelID || empty($arrPageData['arModel'])) {
    setSessionErrors('Ошибка! Модель не найдена!');
    Redirect ('/admin.php?module=models');
}
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
// SET Reorder
if($task=='reorderItems' && !empty($_POST['arItems']) && empty($_POST['arCheckedItems'])) {
    if($hasAccess) {
        $error = false;
        $order = 1;
        foreach($_POST['arItems'] as $pid => $value) {
            $result = updateRecords(CATALOG_TABLE, '`order`='.$order.', `price`="'.$value.'"', 'WHERE `id`='.$pid);
            if($result === false) {
                $error = true;
                break;
            } else {
                ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Обновление сортировки и цены товара ['.$pid.']', $lang, '', 0, $module);  
            }
            $order++;
        }        
        setSessionMessage($error ? 'Ошибка смены сортировки и цены!' : 'Новая сортировка и цена элементов успешно сохранена!');
        Redirect($arrPageData['current_url']);
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Delete Item
elseif($itemID && $task=='deleteItem') {
    if($hasAccess){
        $result = PHPHelper::deleteProduct($itemID, prepareDirPath(UPLOAD_URL_DIR.$module.'/'.$itemID.'/', true));
        if(!$result) setSessionErrors('Данные не удалось удалить. Возможная причина - <p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>');
        Redirect($arrPageData['current_url']);
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Set Active Status Item
elseif($itemID && $task=='publishItem' && isset($_GET['status'])) {
    if($hasAccess) {
        $result = updateRecords(CATALOG_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
        if($result===false) {
            setSessionErrors('Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error());
        } elseif($result) {
            setSessionMessage('Новое состояние успешно сохранено!');
            ActionsLog::getInstance()->save(ActionsLog::ACTION_PUBLICATION, 'Изменена публикация страницы на "'.($_GET['status']==1 ? 'Опубликована' : 'Неопубликована' ).'"', $lang, getValueFromDB(CATALOG_TABLE, 'title', 'WHERE `id`='.$itemID), $itemID, $arrPageData['module']);
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
                if(!PHPHelper::deleteProduct($itemID, prepareDirPath(UPLOAD_URL_DIR.$module.'/'.$itemID.'/', true))) {
                    $error = true;
                    break;
                }
            } setSessionMessage($error ? 'Ошибка удаления, не все товары удалены!' : 'Товары успешно удалены!');
        } 
        else if($_POST['allitems'] == 'publish' || $_POST['allitems'] == 'unpublish') {
            //переопределяем значения на 0 для отключения
            if($_POST['allitems'] == 'unpublish') {
                $arItems = array_fill_keys(array_keys($arItems), 0);
            }            
            $result = updateItems(array('active'=>$arItems), $arItems, 'id', CATALOG_TABLE, array('action'=>ActionsLog::ACTION_EDIT, 'comment'=>'Изменена публикация на '.($_POST['allitems']=='publish' ? '"Опубликовано"' : '"Неопубликовано"'), 'lang'=>$lang, 'module'=>$arrPageData['module']));
            if($result === true) setSessionMessage('Новое состояние успешно сохранено!');
            elseif($result === false) setSessionMessage('Не нуждается в сохранении!');
            else  setSessionErrors($result);
        }        
        Redirect($arrPageData['current_url']);
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
//maikoff.crm.loc/admin.php?module=catalog&modelID=47&task=updateUrls
elseif($task == 'updateUrls') {
    updateRecords(CATALOG_TABLE, 'seo_path=""');
    $updated = 0;
    $query = 'SELECT * FROM '.CATALOG_TABLE;
    $result = mysql_query($query);
    if($result && mysql_num_rows($result)) {
        while(($row = mysql_fetch_assoc($result))) {
            var_dump($row['title']);            
            $seo_path = $UrlWL->strToUniqueUrl($DB, $row['title'], 'catalog', CATALOG_TABLE);
            var_dump($seo_path);
            if($seo_path && $seo_path != $row['seo_path'] && updateRecords(CATALOG_TABLE, 'seo_path="'.$seo_path.'"', 'WHERE id='.$row['id'])) {
                $updated++; 
            }
        }
    }
    var_dump($updated);
    die();
}

// Insert Or Update Item in Database
elseif (!empty($_POST) AND ($task=='addItem' OR $task=='editItem')) {
    if ($hasAccess) {
        $arUnusedKeys = array('id');
        $query_type   = $itemID ? 'update'            : 'insert';
        $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';

        //валидация данных
        $Validator->validateGeneral($_POST['title'], 'Вы не ввели название товара!!!');
        $Validator->validateGeneral($_POST['price'], 'Вы не ввели цену товара!!!');
        $Validator->validateGeneral($_POST['color_id'], 'Вы не выбрали цвет!!!');
        //проверка кода товара на наличие и уникальность
        if(empty($_POST['pcode']) || getValueFromDB(CATALOG_TABLE, 'COUNT(id)', 'WHERE pcode="'.$_POST['pcode'].'"'.($itemID ? ' AND id<>'.$itemID : ''), 'cnt')>0) {
            $Validator->addError(empty($_POST['pcode']) ? 'Вы не ввели код товара!' : 'Код "'.$_POST['pcode'].'" уже используется в товарах!');
        }

        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            // SEO path manipulation
            $_POST['seo_path'] = $UrlWL->strToUniqueUrl($DB, (empty($_POST['seo_path']) ? $_POST['title'] : $_POST['seo_path']), $module, CATALOG_TABLE, $itemID, empty($itemID));
            // copy post data
            $arPostData = $_POST;
            if (isset($arPostData["arPrintTypes"])) $arPostData["print_types"] = implode(",", $arPostData["arPrintTypes"]);
            else $arPostData["print_types"] = "";
            if (empty($arPostData['createdDate']))  $arPostData['createdDate'] = date('Y-m-d');
            if (empty($arPostData['createdTime']))  $arPostData['createdTime'] = date('H:i:s');
            $arPostData['created'] = "{$arPostData['createdDate']} {$arPostData['createdTime']}";
            $arPostData['model_id'] = $modelID;
            $result = $DB->postToDB($arPostData, CATALOG_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
            if ($result){
                if (!$itemID AND $result AND is_int($result)) $itemID = $result;
                if (mysql_affected_rows()) {
                    if ($task=='addItem'){
                        ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "'.$arPostData['title'].'"', SystemComponent::getAcceptLangs(), $arPostData['title'], $itemID, $arrPageData['module']);
                    } else {
                        ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$arPostData['title'].'"', $lang, $arPostData['title'], $itemID, $arrPageData['module']);
                    }  
                } 
                //сохранение размеров
                if (!empty($arPostData['arSizes'])) {
                    $productSizes = getArrValueFromDB(PRODUCT_SIZES_TABLE, 'size', 'WHERE pid='.$itemID);                    
                    foreach($arPostData['arSizes'] as $size) {
                        if(!in_array($size, $productSizes)) {
                            $DB->postToDB(array('pid' => $itemID, 'size' => $size, 'created' => date('Y-m-d H:i:s')), PRODUCT_SIZES_TABLE);
                        }
                    } deleteRecords(PRODUCT_SIZES_TABLE, 'WHERE pid='.$itemID.' AND size NOT IN ("'.implode('","', $arPostData['arSizes']).'")');
                } else {
                    deleteRecords(PRODUCT_SIZES_TABLE, 'WHERE pid='.$itemID);
                }
                setSessionMessage('Запись успешно сохранена!');
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
    if(!$itemID){
        $item = array_combine_multi($DB->getTableColumnsNames(CATALOG_TABLE), '');     
        $item['title'] = $arrPageData['arModel']['title'];
        $item['order']  = getMaxPosition(0, 'order', 'id', CATALOG_TABLE);
        $item['active'] = 1;
        $item['createdDate'] = date('Y-m-d');
        $item['createdTime'] = date('H:i:s');
        $item['imagesCount'] = 0;
        $item['arSizes'] = $item['arHistory'] = $item['print_types'] = array();
    } elseif($itemID) {
        $query = "SELECT * FROM ".CATALOG_TABLE." WHERE id = $itemID LIMIT 1";
        $result = mysql_query($query);
        if(!$result) {
            setSessionErrors("SELECT OPERATIONS: " . mysql_error());
            Redirect($arrPageData['admin_url']);
        } elseif(!mysql_num_rows($result)) {
            setSessionErrors('Запись не найдена!');
            Redirect($arrPageData['admin_url']);
        } else {
            $item = mysql_fetch_assoc($result);
            $item['arSizes'] = getArrValueFromDB(PRODUCT_SIZES_TABLE, 'size', 'WHERE pid='.$itemID);
            $item['print_types']  = explode(",", $item['print_types']);
            $item['createdDate'] = date('Y-m-d', strtotime($item['created']));
            $item['createdTime'] = date('H:i:s', strtotime($item['created']));
            $item['imagesCount'] = getValueFromDB(CATALOGFILES_TABLE, "COUNT(*)", "WHERE `pid`=$itemID", "cnt");
            $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
        }
    }
    $item['arImagesSettings'] = getRowItems(IMAGES_PARAMS_TABLE, '*', '`module`="'.$arrPageData['module'].'"');
    
    if (!empty($_POST)) $item = array_merge($item, $_POST);
    
    //выборка размеров исходя из настроек модели и выбранных размеров в товаре, делаем объединение, так как в модели размер, который назначен в товаре может быть отключен
    $where = '';
    if($arrPageData['arModel']['sizes'] && $item['arSizes']) {
        $where = 'id IN('.$arrPageData['arModel']['sizes'].') OR title IN("'.implode('","', $item['arSizes']).'")';
    } else if ($arrPageData['arModel']['sizes']) {
        $where = 'id IN('.$arrPageData['arModel']['sizes'].')';
    } else if ($item['arSizes']) {
        $where = 'title IN("'.implode('","', $item['arSizes']).'")';
    }
    $arrPageData['arSizes']       = $where ? getRowItems(SIZES_TABLE, '*', $where) : array();
    $arrPageData['arColors']      = getRowItems(COLORS_TABLE.' c LEFT JOIN '.MODEL_COLORCODES_TABLE.' mc ON mc.color_id=c.id AND mc.model_id='.$modelID, 'c.*, IFNULL(mc.code, "") color_code, (SELECT COUNT(id) FROM '.CATALOG_TABLE.' WHERE model_id='.$modelID.' AND color_id=c.id AND id<>'.$itemID.') disabled', 'c.id IN('.$arrPageData['arModel']['colors'].')');
    $arrPageData['arPrintTypes']  = getRowItems(PRINT_TYPES_TABLE);
   
// items list
} else {
    $arrPageData['headCss'][] = '<link href="/js/jquery/themes/smoothness/jquery.ui.all.css" type="text/css" rel="stylesheet" />';
    
    // Create Order Links
    $where = 'WHERE c.`model_id`='.$modelID;
    
    if (!empty($filters) && !empty($filters['title'])) {
        $search = PHPHelper::prepareSearchText($filters['title'], true);
        $where .= ' AND (c.`title` LIKE "%'.$search.'%" OR c.`pcode` LIKE "%'.$search.'%") ';           
    }

    $query = 'SELECT c.*, IF(cf.`filename` IS NOT NULL AND cf.`filename`<>"", CONCAT("/uploaded/catalog/", c.`id`, "/thumb_", cf.`filename`), "") `default_image` 
              FROM '.CATALOG_TABLE.' c                  
              LEFT JOIN '.CATALOGFILES_TABLE.' cf ON cf.`pid`=c.`id` AND cf.`isdefault`=1 
              '.$where.' ORDER BY c.`order` ASC';
    if (($result = mysql_query($query))) {
        while (($row = mysql_fetch_assoc($result))) {
            $items[] = $row;
        }
        $arrPageData['total_items'] = count($items);
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