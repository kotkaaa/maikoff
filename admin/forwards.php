<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

require_once('include/classes/ForwardsCached.php'); //14. Include ForwardsCached class
# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$item          = array(); // Item Array of item
$items         = array(); // Items Array of items Info arrays
$itemID        = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['headTitle']     = 'Переадресации'.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['current_url']   = $arrPageData['admin_url'].$arrPageData['page_url'];
$arrPageData['itemID']        = $itemID;
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
/*
// import
elseif($task=='import') {    
    if (empty($_FILES['filename']) OR $_FILES['filename']['size']==0) {
        $arrPageData['errors'][] = 'Файл не выбран!';
    } else {        
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        require_once('include/classes/PHPExcel.php');        
        $objPHPExcel = PHPExcel_IOFactory::load($_FILES['filename']['tmp_name']);        
        if (!empty($objPHPExcel)) {       
            $arrErrors = array();
            if(isset($_POST['submit_update'])) {
                truncateTable(FORWARDS_TABLE);
            }
            $objPHPExcel->setActiveSheetIndex(0);
            $aSheet = $objPHPExcel->getActiveSheet();    
            foreach($aSheet->getRowIterator() as $key=>$row){
                // заголовки колонок
                if ($key == 1) {
                    continue;
                } else {
                    $arPostData = array();
                    foreach($row->getCellIterator() as $k=>$cell) {
                        $cell = !empty($cell) ? PHPHelper::dataConv(trim($cell), 'utf-8', 'windows-1251', true) : '';
                        $arPostData[($k == 0 ? 'urifrom' : 'urito')] = $cell;
                    }
                    if(!$arPostData['urifrom'] || !$arPostData['urito'] || ForwardsCached::existUri($arPostData['urifrom'])) {
                        $arrErrors[] = 'Ошибка в ряде <b>'.$key.'</b>:<br/> ссылка "'.$arPostData['urifrom'].'" уже есть в списке переадресаций или данные не заполнены';                        
                    } else {                        
                        $arPostData['urifrom'] = ForwardsCached::prepareUri($arPostData['urifrom'], true);
                        $arPostData['urito'] = ForwardsCached::prepareUri($arPostData['urito'], true);
                        $arPostData['created'] = date('Y-m-d').' '.date('H:i:s');
                        if(!$DB->postToDB($arPostData, FORWARDS_TABLE)) {
                            $arrErrors[] = 'Ошибка в ряде <b>'.$key.'</b>:<br/> ссылка "'.$arPostData['urifrom'].'" не записалась в БД';  
                        }
                    }
                }
            }
            $aSheet->disconnectCells();
            $objPHPExcel->disconnectWorksheets();
            setSessionMessage('Данные обновлены!'.(!empty($arrErrors) ? '<br/>Возникли ошибки:<br/>'.implode('<br/>', $arrErrors) : ''));           
            Redirect('/admin.php?module=forwards');
        } else {
            $arrPageData['errors'][] = 'Ошибка считывания данных!';
        }
    }
}
 * 
 */
// Delete Item
if($itemID && $task=='deleteItem') {
    $redirectItem = getSimpleItemRow($itemID, FORWARDS_TABLE);
    $result = deleteDBLangsSync(FORWARDS_TABLE, ' WHERE id='.$itemID);
    if(!$result)    $arrPageData['errors'][] = 'Данные не удалось удалить. Возможная причина - <p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
    elseif($result) {
        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
            ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалена переадресация с "'.$redirectItem['urifrom'].'" на "'.$redirectItem['urito'].'"', $key, 'Переадресация для "'.$redirectItem['urifrom'].'"', 0, $arrPageData['module']);
        Redirect($arrPageData['current_url']);
    }
}
// Set Active Status Item
elseif($itemID && $task=='publishItem' && isset($_GET['status'])) {
    $redirectItem = getSimpleItemRow($itemID, FORWARDS_TABLE);
    $result = updateRecords(FORWARDS_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
    if($result===false) $arrPageData['errors'][]   = 'Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error();
    elseif($result)      {
        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
            ActionsLog::getInstance()->save(ActionsLog::ACTION_PUBLICATION, 'Изменена публикация на "'.($_GET['status']==1 ? 'Опубликовано' : 'Неопубликовано' ).'"', $key, 'Переадресация для "'.$redirectItem['urifrom'].'"', 0, $arrPageData['module']);
        $arrPageData['messages'][] = 'Новое состояние успешно сохранено!';
    }
}
elseif($task == 'addCategoriesRedirects') {
    //удаляем все новые добавленные
    deleteRecords(FORWARDS_TABLE, 'WHERE old_cid=1 AND old_uri=""');
    //откатываем изменения по старым
    updateRecords(FORWARDS_TABLE, 'urito=old_urito', 'WHERE old_urito<>""');
    updateRecords(FORWARDS_TABLE, 'old_urito="", old_cid=0', 'WHERE old_urito<>""');

    $arErrors = array();    
    $query = 'SELECT * FROM '.MAIN_TABLE.' WHERE module="prints"';
    $result = mysql_query($query);
    if($result && mysql_num_rows($result)) {
        while(($row = mysql_fetch_assoc($result))) {
            $fromurl = '/'.$row['seo_path'].'.html';
            $tourl = '/'.$arrModules['prints']['seo_path'].'/'.$row['seo_path'].'.html';
            if(ForwardsCached::existUri($fromurl)) {
                $query = 'SELECT * FROM '.FORWARDS_TABLE.' WHERE urito="'.$fromurl.'"';
                $res = mysql_query($query);
                if($res && mysql_num_rows($res)>0) {
                    while(($r = mysql_fetch_assoc($res))) {
                        if ($r['urito'] == $fromurl) {
                            $data = array(
                                'urito'     => $tourl,
                                'old_urito' => $r['urito'],
                                'active'    => 1,
                                'old_cid'   => 1,
                            );
                            if(!$DB->postToDB($data, FORWARDS_TABLE, 'WHERE id='.$r['id'], array(), 'update')) {
                                $arErrors[] = $fromurl.' ---> save error!';
                            }
                        }
                    }
                }
            } 
            $res = $DB->postToDB(array(
                'urifrom' => $fromurl,
                'urito'   => $tourl,
                'active'  => 1,
                'created' => date('Y-m-d H:i:s'),
                'old_cid' => 1,
            ), FORWARDS_TABLE);
            if(!$res) $arErrors[] = $fromurl.' ---> save error!';            
        }
    }
    
    if($arErrors) {
        echo 'не сохраненные урлы:';
        var_export($arErrors);
    }
    
    die();
}
// Insert Or Update Item in Database
elseif(!empty($_POST) && ($task=='addItem' || $task=='editItem')) {
    $arUnusedKeys = array();
    $query_type   = $itemID ? 'update'            : 'insert';
    $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';
    
    if(empty($_POST['urifrom']) || empty($_POST['urito'])) {
        $Validator->addError('Заполните все поля!');
    } else if(ForwardsCached::existUri($_POST['urifrom'], $itemID)) {
        $Validator->addError('Указанная ссылка "'.$_POST['urifrom'].'" уже используется!');
    } else if (strpos($_POST['urito'], '.') !== false) {
        $Validator->addError('Указанная ссылка на "'.$_POST['urito'].'" не может вести на файл!');
    }
    
    if ($Validator->foundErrors()) {
        $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
    } else {
        $arPostData = $_POST;
        $arPostData['urifrom'] = ForwardsCached::prepareUri($arPostData['urifrom']);
        $arPostData['urito'] = ForwardsCached::prepareUri($arPostData['urito'], true);
        $result = $DB->postToDB($arPostData, FORWARDS_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
        if($result){
            setSessionMessage('Запись успешно сохранена!');
            if(!$itemID && $result && is_int($result)) $itemID = $result;
            if (mysql_affected_rows()) {
                if ($task=='addItem'){
                    foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                        ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано переадресацию с "'.$arPostData['urifrom'].'" на "'.$arPostData['urito'].'"', $key, 'Переадресация для "'.$arPostData['urifrom'].'"', $itemID, $arrPageData['module']);
                } else {
                     ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано переадресацию с "'.$arPostData['urifrom'].'" на "'.$arPostData['urito'].'"', $lang, 'Переадресация для "'.$arPostData['urifrom'].'"', $itemID, $arrPageData['module']);
                }  
            }
            Redirect($arrPageData['current_url'].(isset($_POST['submit_add']) ? '&task=addItem' : ((isset($_POST['submit_apply']) && $itemID) ? '&task=editItem&itemID='.$itemID : '')) );
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
    if(!$itemID){
        $item = array_combine_multi($DB->getTableColumnsNames(FORWARDS_TABLE), '');
        $item['active'] = 1;
        $item['created'] = date('Y-m-d H:i:s');
    } elseif($itemID) {
        $query = "SELECT * FROM ".FORWARDS_TABLE." WHERE id = $itemID LIMIT 1";
        $result = mysql_query($query);
        if(!$result)
            $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
        elseif(!mysql_num_rows($result))
            $arrPageData['errors'][] = "SELECT OPERATIONS: No this Item in DataBase";
        else {
            $item = mysql_fetch_assoc($result);
            $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
        }
    }

    if(!empty($_POST)) $item = array_merge($item, $_POST);

} else {
    // Total pages and Pager
    $arrPageData['total_items'] = intval(getValueFromDB(FORWARDS_TABLE." t", 'COUNT(*)', '', 'count'));
    $arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['admin_url']);
    $arrPageData['total_pages'] = $arrPageData['pager']['count'];
    $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    // END Total pages and Pager

    $order = "ORDER BY ".(!empty($arrOrder['mysql']) ? implode(', ', $arrOrder['mysql']) : "t.id");
    $limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";

    $query  = "SELECT * FROM `".FORWARDS_TABLE."` t  $order $limit";
    $result = mysql_query($query);
    if(!$result) $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
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