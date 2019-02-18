<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// MODULE DATA VERIFICATION \\\\\\\\\\\\\\\\\\\\\\\\\\\
foreach($arAcceptLangs as $ln) {
    $dbTable = replaceLang($ln, MODELS_TABLE);
    if(!$DB->isSetDBTable($dbTable)){
        $arrPageData['errors'][]    = sprintf(ADMIN_MODULE_TABLE_ERROR, CATALOG, $arrPageData['module'], $dbTable);
        $arrPageData['module']      = 'module_messages';
        $arrPageData['moduleTitle'] = CATALOG;
        return;
    }
}
// /////////////////////// END MODULE DATA VERIFICATION \\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$itemID        = (isset($_GET['itemID']) AND intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$cid           = (isset($_GET['cid']) and intval($_GET['cid']))       ? intval($_GET['cid'])    : 0;
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$filters       = !empty($_GET['filters'])? $_GET['filters']: array();
$hasAccess     = $UserAccess->getAccessToModule($arrPageData['module']); 
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['items_on_page'] = 20;
$arrPageData['itemID']        = $itemID;
$arrPageData['category_id']   = $cid;
$arrPageData['filters']       = $filters;
$arrPageData['headTitle']     = 'Каталог моделей'.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['arBrands']      = getRowItems(BRANDS_TABLE, '*', '`active`>0', '`order`, `title`');
$arrPageData['arSeries']      = getRowItems(SERIES_TABLE, '*', empty($filters['brand_id']) ? '' : 'brand_id='.$filters['brand_id'], '`order`, `title`');
$arrPageData['current_url']   = $arrPageData['admin_url'].($filters ? '&'.http_build_query(array('filters' => $filters)) : '').($cid ? '&cid='.$cid : '');
$arrPageData['arCategoryTree'] = getCategoriesTree($lang, 0, 0, false, 'catalog', '', '', array(), MODELS_TABLE, 'category_id');
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
// SET Reorder
if($task=='reorderItems' && !empty($_POST['arOrder']) && empty($_POST['arCheckedItems'])) {
    if($hasAccess) {
        $result = reorderItems($_POST['arOrder'], 'order', 'id', MODELS_TABLE, array('action'=>ActionsLog::ACTION_EDIT, 'comment'=>'Изменена сортировка', 'lang'=>$lang, 'module'=>$arrPageData['module']));
        if($result===true) {
            setSessionMessage('Новая сортировка елементов успешно сохранена!');
        } elseif($result) {
            setSessionErrors($result);
        }
        Redirect($arrPageData['current_url']);
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Delete Item
elseif($itemID && $task=='deleteItem') {
    if($hasAccess){
        if(PHPHelper::deleteModel($itemID)) {
            setSessionMessage('Модель и все ее товары удалены!');            
        } else {
            setSessionErrors('Ошибка удаления модели или модель удалена, но удалены не все товары!');
        }
        Redirect($arrPageData['current_url']);
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Set Active Status Item
elseif($itemID && $task=='publishItem' && isset($_GET['status'])) {
    if($hasAccess) {
        $result = updateRecords(MODELS_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
        if($result===false) {
            setSessionErrors('Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error());
        } elseif($result) {
            setSessionMessage('Новое состояние успешно сохранено!');
            ActionsLog::getInstance()->save(ActionsLog::ACTION_PUBLICATION, 'Изменена публикация страницы на "'.($_GET['status']==1 ? 'Опубликована' : 'Неопубликована' ).'"', $lang, getValueFromDB(MODELS_TABLE, 'title', 'WHERE `id`='.$itemID), $itemID, $arrPageData['module']);
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
                if(PHPHelper::deleteModel($itemID) === false) {
                    $error = true;
                }
            } 
            setSessionMessage($error ? 'Ошибка удаления, не все данные удалены!' : 'Модели успешно удалены!');
        } 
        else if($_POST['allitems'] == 'publish' || $_POST['allitems'] == 'unpublish') {
            //переопределяем значения на 0 для отключения
            if($_POST['allitems'] == 'unpublish') {
                $arItems = array_fill_keys(array_keys($arItems), 0);
            }            
            $result = updateItems(array('active'=>$arItems), $arItems, 'id', MODELS_TABLE, array('action'=>ActionsLog::ACTION_EDIT, 'comment'=>'Изменена публикация на '.($_POST['allitems']=='publish' ? '"Опубликовано"' : '"Неопубликовано"'), 'lang'=>$lang, 'module'=>$arrPageData['module']));
            if($result === true) setSessionMessage('Новое состояние успешно сохранено!');
            elseif($result === false) setSessionMessage('Не нуждается в сохранении!');
            else  setSessionErrors($result);
        }        
        Redirect($arrPageData['current_url']);
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Insert Or Update Item in Database
elseif (!empty($_POST) AND ($task=='addItem' OR $task=='editItem')) {
    if ($hasAccess) {
        $arUnusedKeys = array('id');
        $query_type   = $itemID ? 'update'            : 'insert';
        $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';

        //валидация данных
        $Validator->validateGeneral($_POST['title'], 'Вы не ввели названия страницы!!!');
        $Validator->validateGeneral($_POST['pcode'], 'Вы не ввели код модели!!!');
        if(empty($_POST['category_id']) || $_POST['category_id'] == 0) {
            $Validator->addError('Вы не выбрали категорию!!!');
        }
        if(empty($_POST['arColors'])) {
            $Validator->addError('Вы не выбрали цвет!!!');
        }
        //проверка на уникальность бренд+серия
        if($task=='addItem') {
            /* серию обобщили, поэтому это условие стало неверным
			if (getValueFromDB(MODELS_TABLE, 'COUNT(id)', 'WHERE brand_id='.(empty($_POST['brand_id']) ? 0 : $_POST['brand_id']).' AND series_id='.(empty($_POST['series_id']) ? 0 : $_POST['series_id']))) {
                $Validator->addError('Товар с таким набором Бренд + Серия уже создан!!!');
            }*/
            if(!is_numeric($_POST['price'])) {
                $Validator->addError('Введите корректно цену!!!');
            }
        }

        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            // copy post data
            $arPostData = $_POST;
            if (empty($arPostData['createdDate'])) $arPostData['createdDate'] = date('Y-m-d');
            if (empty($arPostData['createdTime'])) $arPostData['createdTime'] = date('H:i:s');
            $arPostData['created'] = "{$arPostData['createdDate']} {$arPostData['createdTime']}";
            // сборка цветов и размеров
            $arPostData['sizes'] = empty($arPostData['arSizes']) ? '' : implode(',',$arPostData['arSizes']);
            $arPostData['colors'] = implode(',',$arPostData['arColors']);
            $result = $DB->postToDB($arPostData, MODELS_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
            if ($result){
                if (!$itemID AND $result AND is_int($result)) $itemID = $result;
                if (mysql_affected_rows()) {
                    if ($task=='addItem'){
                        ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "'.$arPostData['title'].'"', SystemComponent::getAcceptLangs(), $arPostData['title'], $itemID, $arrPageData['module']);
                    } else {
                        ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$arPostData['title'].'"', $lang, $arPostData['title'], $itemID, $arrPageData['module']);
                    }  
                } 
                setSessionMessage('Запись успешно сохранена!');
                // сохранение атрибутов
                PHPHelper::saveAttributes($itemID, $DB, MODEL_ATTRIBUTES_TABLE, 'mid', $arPostData);
                // если модель только что создана, то создаем автоматически товары выбранных цветов
                if($task=='addItem') {
                    $printTypes = isset($arPostData["arPrintTypes"]) ? implode(",", $arPostData["arPrintTypes"]) : '';
                    $arColors = getRowItemsInKeyValue('id', 'title', COLORS_TABLE, 'id,title', 'WHERE id IN('.$arPostData['colors'].')');
                    foreach($arColors as $colorID => $title) {
                        $product = array(
                            'title' => $arPostData['title'].' '.$title,     
                            'pcode' => '',
                            'model_id' => $itemID,
                            'color_id' => $colorID,
                            'price'    => $arPostData['price'],
                            'print_types' => $printTypes,
                            'order' => getMaxPosition(0, 'order', 'id', CATALOG_TABLE),
                            'created' => date('Y-m-d H:i:s'),
                        );
                        $product['seo_path'] = $UrlWL->strToUniqueUrl($DB, $product['title'], 'catalog', CATALOG_TABLE);
                        if(($productID = $DB->postToDB($product, CATALOG_TABLE))) {
                            if($arPostData['sizes']) {
                                $arSizes = getRowItems(SIZES_TABLE, '*', 'id IN('.$arPostData['sizes'].')');
                                foreach ($arSizes as $size) {                                                       
                                    $DB->postToDB(array('pid' => $productID, 'size' => $size['title'], 'created' => date('Y-m-d H:i:s')), PRODUCT_SIZES_TABLE);                                
                                }     
                            }
                        }
                    }
                }
                // сохранение кодов цветов
                $arModelColorCodes = getRowItemsInKeyValue('color_id', 'code', MODEL_COLORCODES_TABLE, 'color_id,code', 'WHERE model_id='.$itemID);
                $arColorCodes = array();
                if(!empty($_POST['arColorCodes'])) {
                    foreach($_POST['arColorCodes'] as $color_id => $code) {
                        if($code) {
                            $arPost = array(
                                'model_id' => $itemID,
                                'color_id' => $color_id,
                                'code' => $code
                            );
                            $exists = array_key_exists($color_id, $arModelColorCodes);
                            if(!$exists || $arModelColorCodes[$color_id] != $code) {
                                $exists = $DB->postToDB($arPost, MODEL_COLORCODES_TABLE, $exists ? 'WHERE model_id='.$itemID.' AND color_id='.$color_id : '', array(), $exists ? 'update' : 'insert');                                
                            } 
                            if($exists) {                                
                                $arColorCodes[] = $color_id;
                            } 
                        }
                    }
                }                
                deleteRecords(MODEL_COLORCODES_TABLE, 'WHERE model_id='.$itemID.($arColorCodes ? ' AND color_id NOT IN('.implode(',', $arColorCodes).')' : ''));
                // обновление кода товара
                updateRecords(CATALOG_TABLE.' c LEFT JOIN '.MODELS_TABLE.' m ON c.model_id=m.id LEFT JOIN '.MODEL_COLORCODES_TABLE.' mc ON mc.model_id=c.model_id AND mc.color_id=c.color_id', 'c.pcode=CONCAT(m.pcode, IF(mc.id IS NOT NULL, CONCAT("'.CATALOG_PRODUCT_PCODE_SEPARATOR.'", mc.code), ""))', 'WHERE c.model_id='.$itemID);
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
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headCss'][]     = '<link href="/js/jquery/themes/smoothness/jquery.ui.all.css" type="text/css" rel="stylesheet" />';
$arrPageData['headScripts'][] = '<script src="/js/jquery/ui/jquery.ui.core.js" type="text/javascript"></script>';

if ($task=='addItem' || $task=='editItem'){
    if (!$hasAccess) {
        setSessionErrors($UserAccess->getAccessError()); 
        Redirect($arrPageData['current_url']);
    }
    
    $arrPageData['headCss'][]     = '<link href="/js/libs/select2/select2.css" type="text/css" rel="stylesheet"/>';
    $arrPageData['headScripts'][] = '<script src="/js/libs/select2/select2.js" type="text/javascript"></script>';
    $arrPageData['headScripts'][] = '<script src="/js/libs/select2/select2_locale_ru.js" type="text/javascript"></script>';
    
    $arrPageData['arPrintTypes']  = getRowItems(PRINT_TYPES_TABLE);
    $arrPageData['arSizes']       = getRowItems(SIZES_TABLE);
    $arrPageData['arSizeGrids']   = getRowItems(SIZE_GRIDS_TABLE);
    $arrPageData['arColors']      = getRowItems(COLORS_TABLE.' c', 'c.*, (SELECT COUNT(id) FROM '.CATALOG_TABLE.' WHERE model_id='.$itemID.' AND color_id=c.id) disabled');
    $arrPageData['arArticles']    = getRowItems(NEWS_TABLE, '*', '`active`>0');  
    
    PHPHelper::prepareAttrGroups($arrPageData);

    if(!$itemID){
        $item = array_combine_multi($DB->getTableColumnsNames(MODELS_TABLE), '');
        $item['attributes'] = $item['attrGroups'] = $item['arSizes'] = $item['arColors'] = $item['arHistory'] = $item['arColorCodes'] = array();
        $item['order']  = getMaxPosition(0, 'order', 'id', MODELS_TABLE);
        $item['active'] = 1;
        $item['price'] = 0;
        $item['category_id'] = 0;
        $item['createdDate'] = date('Y-m-d');
        $item['createdTime'] = date('H:i:s');        
    } elseif($itemID) {
        $query = "SELECT * FROM ".MODELS_TABLE." WHERE id = $itemID LIMIT 1";
        $result = mysql_query($query);
        if(!$result) {
            setSessionErrors("SELECT OPERATIONS: " . mysql_error());
            Redirect($arrPageData['admin_url']);
        } elseif(!mysql_num_rows($result)) {
            setSessionErrors('Запись не найдена!');
            Redirect($arrPageData['admin_url']);
        } else {
            $item = mysql_fetch_assoc($result);
            $item['price'] = 0;
            $item['createdDate'] = date('Y-m-d', strtotime($item['created']));
            $item['createdTime'] = date('H:i:s', strtotime($item['created']));
            $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
            // sizes and colors
            $item['arSizes'] = $item['sizes'] ? explode(',',$item['sizes']) : array();
            $item['arColors'] = explode(',',$item['colors']);
            $item['arColorCodes'] = getRowItemsInKeyValue('color_id', 'code', MODEL_COLORCODES_TABLE, 'color_id,code', 'WHERE model_id='.$itemID);
            // item attributes
            PHPHelper::prepareItemAttributes($item, MODEL_ATTRIBUTES_TABLE, 'mid', $arrPageData['attrGroups']);
        }
    }
    if (!empty($_POST)) $item = array_merge($item, $_POST);
    
// items list
} else {    
    $arrPageData['headScripts'][] = '<script src="/js/jquery/ui/jquery.ui.autocomplete.js" type="text/javascript"></script>';
    
    $where = $cid ? 'category_id='.$cid : '';
    if (!empty($filters)) {
        if(!empty($filters['title'])) {
            $search = PHPHelper::prepareSearchText($filters['title'], true);
            $where .= ($where ? ' AND ' : '').' (m.`title` LIKE "%'.$search.'%" OR m.`pcode` LIKE "%'.$search.'%") ';            
        }
        if(!empty($filters['brand_id'])) {
            $where .= ($where ? ' AND ' : '').' m.brand_id= '.$filters['brand_id'];
        }
        if(!empty($filters['series_id'])) {
            $where .= ($where ? ' AND ' : '').' m.series_id= '.$filters['series_id'];
        }
    }
    $where = ($where ? 'WHERE ' : '').$where;
    // Total pages and Pager
    if(empty($filters['show_all'])) {
        $arrPageData['total_items'] = getValueFromDB(MODELS_TABLE.' m', 'COUNT(m.id)', $where, 'cnt');
        $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['current_url']);
        $arrPageData['total_pages'] = $arrPageData['pager']['count'];
        $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    }
    // END Total pages and Pager
    $order = "ORDER BY m.`order` ASC";
    $limit = empty($filters['show_all']) ? "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}" : '';
    $query = 'SELECT m.*, b.`title` `brand_title`, s.`title` `series_title`, IFNULL(COUNT(c.`id`),0) `products_cnt` 
              FROM '.MODELS_TABLE.' m 
              LEFT JOIN '.BRANDS_TABLE.' b ON b.`id`=m.`brand_id` 
              LEFT JOIN '.SERIES_TABLE.' s ON s.`id`=m.`series_id` 
              LEFT JOIN '.CATALOG_TABLE.' c ON c.`model_id`=m.`id` '.
              $where.' GROUP BY m.id '.$order.' '.$limit;
    $result = mysql_query($query);
    if ($result) {
        while (($row = mysql_fetch_assoc($result))) {
            $imageData = getItemRow(CATALOG_TABLE.' c LEFT JOIN '.CATALOGFILES_TABLE.' cf ON cf.`pid`=c.`id` AND cf.`isdefault`=1', 'cf.filename, c.id', 'WHERE c.model_id='.$row['id'].' ORDER BY c.`order` ASC');
            $row['default_image'] = UPLOAD_URL_DIR.'catalog'.DS.($imageData['filename'] ? $imageData['id'].DS.$imageData['filename'] : 'noimage.jpg');
            $items[] = $row;
        }
    } else $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();    
}
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item',          $item);
$smarty->assign('items',         $items);
// \\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES ////////////////////////////
# ##############################################################################