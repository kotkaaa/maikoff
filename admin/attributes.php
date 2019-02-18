<?php defined('WEBlife') or die( 'Restricted access' );

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$GID           = (isset($_GET['gid'])    AND intval($_GET['gid']))    ? intval($_GET['gid']) : 0;
$itemID        = (isset($_GET['itemID']) AND intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$copyID        = (isset($_GET['copyID']) AND intval($_GET['copyID'])) ? intval($_GET['copyID']) : 0;
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$hasAccess     = $UserAccess->getAccessToModule($arrPageData['module']);
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################

# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['GID']           = $GID;
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['page_url'].($GID ? '&gid='.$GID : '');
$arrPageData['headTitle']     = ATTRIBUTES.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['items_on_page'] = 20;
$arrPageData['arGroups']      = getComplexRowItems(ATTRIBUTE_GROUPS_TABLE, '*', 'WHERE `active`>0', '`order`, `title`');
$arrPageData['arTypes']       = getComplexRowItems(ATTRIBUTE_TYPES_TABLE, '*', "WHERE `active`>0");
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData["files_url"], true);
$categoryTree = getRowItems(ATTRIBUTE_GROUPS_TABLE);
if(!empty($categoryTree)) {
    foreach($categoryTree as $key => $group) {
        $categoryTree[$key]['children'] = getRowItems(ATTRIBUTES_TABLE, '*', '`gid`='.$group['id']);
    }
}
// SET Reorder
$item_title = $itemID ? getValueFromDB(ATTRIBUTES_TABLE, 'title', 'WHERE `id`='.$itemID) : '';
if($task=='reorderItems' AND !empty($_POST)) {
    if($hasAccess) {
        $result = reorderItems($_POST['arOrder'], 'order', 'id', ATTRIBUTES_TABLE, array('action'=>ActionsLog::ACTION_EDIT, 'comment'=>'Изменена сортировка', 'lang'=>$lang, 'module'=>$arrPageData['module']));
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
elseif ($itemID AND $task=='deleteItem') {
    if ($hasAccess) {
        $result = deleteDBLangsSync(ATTRIBUTES_TABLE, ' WHERE id='.$itemID);
        if (!$result) {
            $arrPageData['errors'][] = 'Данные не удалось удалить. Возможная причина - <p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
        } elseif ($result) {
            foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item_title.'"', $key, $item_title, 0, $arrPageData['module']);
            deleteDBLangsSync(MODEL_ATTRIBUTES_TABLE, 'WHERE `aid`='.$itemID);
            deleteDBLangsSync(ATTRIBUTES_VALUES_TABLE, 'WHERE `aid`='.$itemID);
            deleteRecords(CATEGORY_ATTRIBUTES_TABLE, 'WHERE `aid`='.$itemID);
            updateDBLangsSync(FILTERS_TABLE, "`aid`=0", 'WHERE `aid`='.$itemID);
            Redirect($arrPageData['current_url']);
            exit;
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError();
    }
}
// Set Active Status Item
elseif ($itemID AND $task=='publishItem' AND isset($_GET['status'])) {
    if ($hasAccess) {
        $result = updateRecords(ATTRIBUTES_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
        if ($result===false) {
            $arrPageData['errors'][]   = 'Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error();
        } elseif ($result) {
            ActionsLog::getInstance()->save(ActionsLog::ACTION_PUBLICATION, 'Изменена публикация на "'.($_GET['status']==1 ? 'Опубликовано' : 'Неопубликовано' ).'"', $lang, $item_title, $itemID, $arrPageData['module']);
            $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
        }
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError();
    }
}
//Copy item
elseif ($copyID AND $task=='addItem'){
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
        $query_type = $itemID ? 'update' : 'insert';
        $conditions = $itemID ? 'WHERE `id`=' . $itemID : '';
        if (!empty($_POST['tid']) and $_POST['tid'] == 2 and ! empty($_POST['arValues'])) {
            foreach ($_POST['arValues'] as $arValue) {
                if (!$Validator->validateNumber($arValue['title'], 'Не совпадают типы!'))
                    break;
            }
        }
        $Validator->validateGeneral($_POST['title'], 'Вы не ввели названия страницы!!!');
        $Validator->validateGeneral($_POST['order'], 'Вы не ввели порядковый номер страницы!!!');
        $Validator->validateGeneral($_POST['tid'], 'Вы не выбрали тип!!!');
        $Validator->validateGeneral($_POST['gid'], 'Вы не выбрали группу!!!');

        $arValues = array();
        if (!empty($_POST['arValues'])) {
            foreach ($_POST['arValues'] as $key => $arValue) {
                $arValue['title'] = mb_strtolower($arValue['title']);
                if (array_key_exists($arValue['title'], $arValues)) {
                    $Validator->addError('Ошибка! Значение "'.$arValue['title'].'" уже присутствует в атрибуте!');
                } else {
                    if (!isset($arValues[$arValue['title']])) $arValues[$arValue['title']]=0;
                    $arValues[$arValue['title']]++;
                }                   
            }
        }
        
        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>" . $Validator->getListedErrors();
        } else {
            $arPostData = $_POST;
            $result = $DB->postToDB($arPostData, ATTRIBUTES_TABLE, $conditions, $arUnusedKeys, $query_type, ($itemID ? false : true));
            if ($result) {
                if (!$itemID and $result and is_int($result)) $itemID = $result;
                $item_title = getValueFromDB(ATTRIBUTES_TABLE, 'title', 'WHERE `id`=' . $itemID);
                if ($task == 'addItem') {
                    ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "' . $item_title . '"', SystemComponent::getAcceptLangs(), $item_title, $itemID, $arrPageData['module']);
                } else
                    ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "' . $item_title . '"', $lang, $item_title, $itemID, $arrPageData['module']);
                setSessionMessage('Запись успешно сохранена!');
                // Write values
                $arResults = array();
                if (!empty($_POST['arValues'])) {
                    $order = 1;
                    foreach ($_POST['arValues'] as $key => $arValue){
                        $arUnusedKeys = array();
                        $valueItem = !empty($arValue['id']) ? getItemRow(ATTRIBUTES_VALUES_TABLE, '*', 'WHERE `id`='.$arValue['id']) : array();
                        if(!empty($valueItem) && !empty($valueItem['seo_path']) && $valueItem['seo_path'] == $arValue['seo_path']) {
                            $arResults[] = $valueItem['id'];
                            $arUnusedKeys[] = "seo_path";
                        }
                        $new_name = '';
                        if (isset($arValue['delete_image']) AND !empty($valueItem)) {
                            unlinkImage($valueItem['id'], ATTRIBUTES_VALUES_TABLE, $arrPageData['files_url'], false, false);
        //                        ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Удалено изображение для значения аттрибута "'.$arValue['value'].'"', $lang, $arPostData['title'], $itemID, $arrPageData['module']);
                        }
                        if (isset($_FILES['arValues']['tmp_name'][$key])) {
                            $iname        = $_FILES['arValues']['name'][$key]['image']; //имя файла до его отправки на сервер (pict.gif)
                            $itmp_name    = $_FILES['arValues']['tmp_name'][$key]['image']; //содержит имя файла во временном каталоге (/tmp/phpV3b3qY)
                            $arExtAllowed = array('jpeg','jpg','gif','png');
                            if($iname AND $itmp_name) {
                                $file_ext = getFileExt($iname);
                                if (in_array($file_ext, $arExtAllowed)) {
                                    if (!empty($valueItem)) unlinkImage($valueItem['id'], ATTRIBUTES_VALUES_TABLE, $arrPageData['files_url']);
                                    $new_name = createUniqueFileName($arrPageData['files_url'], $file_ext, basename($iname, '.'.$file_ext));
                                    $image = WideImage::load($itmp_name);
                                    $image->saveToFile($arrPageData['files_path'].$new_name);
        //                                ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Добавлено изображение для значения аттрибута "'.$arValue['value'].'"', $lang, $arPostData['title'], $itemID, $arrPageData['module']);
                                }
                            }
                        }
                        // SEO path manipulation
                        if (empty($arValue['seo_path']))
                            $arValue['seo_path'] = $UrlWL->strToUniqueUrl($DB, $arValue['title'], $UrlWL->strToUrl($item_title), ATTRIBUTES_VALUES_TABLE);

                        $arData = array(
                            'aid'          => $itemID,
                            'title'        => $arValue['title'],
                            'title_single' => $arValue['title_single'],
                            'title_multi'  => $arValue['title_multi'],
                            'title_male'   => $arValue['title_male'],
                            'title_female' => $arValue['title_female'],
                            'title_extra'  => $arValue['title_extra'],
                            'seo_path'     => $arValue['seo_path'],
                            'image'        => !empty($new_name) ? $new_name : (!empty($valueItem) ? $valueItem['image'] : ''),
                            'order'        => $order++
                        );
                        $result = $DB->postToDB($arData, ATTRIBUTES_VALUES_TABLE, !empty($valueItem) ? 'WHERE `id`='.$valueItem['id'] : '', $arUnusedKeys, (!empty($valueItem) ? 'update' : 'insert'), (!empty($valueItem) ? false : true));
                        $arResults[] = !empty($valueItem) ? $valueItem['id'] : $result;
                    }
                }
                deleteDBLangsSync(MODEL_ATTRIBUTES_TABLE, "WHERE `aid`=$itemID".(!empty($arResults) ? " AND `value` NOT IN(".implode(",", $arResults).")" : ""));                
                deleteItemsAndFilesFromDB('image', ATTRIBUTES_VALUES_TABLE, 'WHERE `aid`='.$itemID.(!empty($arResults) ? ' AND `id` NOT IN ('.  implode(',', $arResults).')' : ''), $arrPageData['files_url'], true);
                Redirect($arrPageData['current_url'] . (isset($_POST['submit_add']) ? '&task=addItem' : ((isset($_POST['submit_apply']) && $itemID) ? '&task=editItem&itemID=' . $itemID : '')));
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
    if (!$hasAccess) {
        setSessionErrors($UserAccess->getAccessError());
        Redirect($arrPageData['admin_url']);
    }
    if (!$itemID) {
        if ($copyID) {
            $item = getSimpleItemRow($copyID, ATTRIBUTES_TABLE);
            $item['arValues'] = array();//getRowItemsInKey("id", ATTRIBUTES_VALUES_TABLE, '*', 'WHERE `aid`='.$itemID, 'ORDER BY `order`');
            $SQLquery = "SELECT av.*, "
                    . "((SELECT COUNT(*) FROM `".MODEL_ATTRIBUTES_TABLE."` WHERE `aid`=$copyID AND `value`=av.`id`) + "
                    . "(SELECT COUNT(*) FROM `".PRINT_ATTRIBUTES_TABLE."` WHERE `aid`=$copyID AND `value`=av.`id`)) AS `used` "
                    . "FROM `".ATTRIBUTES_VALUES_TABLE."` av "
                    . "WHERE av.`aid`=$copyID "
                    . "ORDER BY `order`";
            $result = mysql_query($SQLquery);
            if ($result and mysql_num_rows($result)>0) {
                while ($row = mysql_fetch_assoc($result)) {
                    $item['arValues'][$row["id"]] = $row;
                }
            }
            $item['arValuesMaxID'] = intval(getValueFromDB(ATTRIBUTES_VALUES_TABLE, 'MAX(id)', 'WHERE `aid`='.$copyID, 'max'));
            $item = array_merge($item, array('id'=>''));
        } else {
            $item = array_combine_multi($DB->getTableColumnsNames(ATTRIBUTES_TABLE), '');
            $item['arValues']  = array();
            $item['arValuesMaxID'] = 0;
        }
        $item['order']  = getMaxPosition(null, 'order', 'cid', ATTRIBUTES_TABLE);
        $item['active'] = 1;
        $item['arHistory'] = array();
    } elseif ($itemID and $item=getSimpleItemRow($itemID, ATTRIBUTES_TABLE)) {
        $item['arValues'] = array();//getRowItemsInKey("id", ATTRIBUTES_VALUES_TABLE, '*', 'WHERE `aid`='.$itemID, 'ORDER BY `order`');
        $SQLquery = "SELECT av.*, "
                . "((SELECT COUNT(*) FROM `".MODEL_ATTRIBUTES_TABLE."` WHERE `aid`=$itemID AND `value`=av.`id`) + "
                . "(SELECT COUNT(*) FROM `".PRINT_ATTRIBUTES_TABLE."` WHERE `aid`=$itemID AND `value`=av.`id`)) AS `used` "
                . "FROM `".ATTRIBUTES_VALUES_TABLE."` av "
                . "WHERE av.`aid`=$itemID "
                . "ORDER BY `order`";
        $result = mysql_query($SQLquery);
        if ($result and mysql_num_rows($result)>0) {
            while ($row = mysql_fetch_assoc($result)) {
                $item['arValues'][$row["id"]] = $row;
            }
        }
        $item['arValuesMaxID'] = intval(getValueFromDB(ATTRIBUTES_VALUES_TABLE, 'MAX(id)', 'WHERE `aid`='.$itemID, 'max'));
        $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
    }
    $item['seo_path'] = $UrlWL->strToUrl($item['title']);
    
    if (!empty($_POST)) $item = array_merge($item, $_POST);
    
} else {
    // Create Order Links
    $arrPageData['arrOrderLinks'] = getOrdersLinks(
        array('default'=>HEAD_LINK_SORT_DEFAULT, 'title'=>HEAD_LINK_SORT_TITLE),
        $arrOrder['get'], $arrPageData['admin_url'], 'pageorder', '_');
    // Display Items List Data
    $where = (!empty($GID))? 'WHERE a.`gid`='.$GID.' ': ' ';
    // Total pages and Pager
    $arrPageData['total_items'] = intval(getValueFromDB(ATTRIBUTES_TABLE." t", 'COUNT(*)', $where, 'count'));
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url'].$arrPageData['filter_url']);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager
    $order  = 'ORDER BY '.(!empty($arrOrder['mysql']) ? 'a.'.implode(', a.', $arrOrder['mysql']).' ' : 'a.order, a.id ');
    $limit  = 'LIMIT '.$arrPageData['offset'].', '.$arrPageData['items_on_page'];
    $query  = 'SELECT a.*, ag.`title` AS `gtitle` FROM `'.ATTRIBUTES_TABLE.'` a LEFT JOIN `'.ATTRIBUTE_GROUPS_TABLE.'` ag ON(ag.`id` = a.`gid`) '.$where.$order.$limit;
    $result = mysql_query($query);
    if (!$result) {
        $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
    } else {
        while ($row = mysql_fetch_assoc($result)) {
            $row['products'] = getComplexRowItems(MODEL_ATTRIBUTES_TABLE, 'id', 'WHERE `aid`='.(int)$row['id']);
            $row['filters']  = getComplexRowItems(FILTERS_TABLE, 'id', 'WHERE `aid`='.(int)$row['id']);
            $items[]         = $row;
        }
    }
}

$smarty->assign('item',         $item);
$smarty->assign('items',        $items);
$smarty->assign('categoryTree', $categoryTree);