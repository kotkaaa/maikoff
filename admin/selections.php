<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$itemID    = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$item      = array(); // Items Array of items Info arrays  
$items     = array(); // Items Array of items Info arrays  
$hasAccess = $UserAccess->getAccessToModule($arrPageData['module']);
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['headTitle']     = SELECTIONS.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['items_on_page'] = 20;
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headCss'][]     = '<link href="/js/jquery/themes/smoothness/jquery.ui.all.css" type="text/css" rel="stylesheet" />';
$arrPageData['headScripts'][] = '<script src="/js/jquery/ui/jquery.ui.core.js" type="text/javascript"></script>';
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
if ($itemID && !empty($_POST) && $task=='editItem') {
    if ($hasAccess) {
        $arUnusedKeys = array('id');
        $query_type   = $itemID ? 'update'            : 'insert';
        $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';

        //валидация данных
        $Validator->validateGeneral($_POST['title'], 'Вы не ввели название!!!');
        $Validator->validateGeneral($_POST['descr'], 'Вы не ввели второй заголовок!!!');
        if($_POST['type'] == Selections::SELECTION_TYPE_PRODUCTS && empty($_POST['arProducts'])) {
            $Validator->addError('В данном типе выборки обязательно должны быть товары, добавьте товары!');
        }

        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            // copy post data
            $arPostData = $_POST;
            if (empty($arPostData['createdDate'])) $arPostData['createdDate'] = date('Y-m-d');
            if (empty($arPostData['createdTime'])) $arPostData['createdTime'] = date('H:i:s');
            $arPostData['created'] = "{$arPostData['createdDate']} {$arPostData['createdTime']}";
            $result = $DB->postToDB($arPostData, SELECTIONS_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
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
                if (isset($arPostData['arProducts'])) {
                    $products = getArrValueFromDB(SELECTION_PRODUCTS_TABLE, 'product_id', 'WHERE selection_id='.$itemID);
                    foreach($arPostData['arProducts'] as $order => $id) {
                        if (in_array($id, $products)) {
                            updateRecords(SELECTION_PRODUCTS_TABLE, '`order`='.$order, 'WHERE `selection_id`='.$itemID.' AND `product_id`='.$id);
                        } else {
                            $data = array(
                                'selection_id' => $itemID,
                                'product_id' => $id,
                                'order' => $order,
                                'created' => date('Y-m-d H:i:s')
                            );
                            $DB->postToDB($data, SELECTION_PRODUCTS_TABLE);
                        }
                    } deleteRecords(SELECTION_PRODUCTS_TABLE, 'WHERE selection_id='.$itemID.($arPostData['arProducts'] ? ' AND product_id NOT IN ("'.implode('","', $arPostData['arProducts']).'")' : ''));
                }
                setSessionMessage('Запись успешно сохранена!');
                Redirect($arrPageData['current_url'].((isset($_POST['submit_apply']) && $itemID) ? '&task=editItem&itemID='.$itemID : ''));
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
if ($task=='editItem') {
    if (!$itemID || !$hasAccess) {
        setSessionErrors(!$hasAccess ? $UserAccess->getAccessError() : 'Ошибка!');             
        Redirect($arrPageData['current_url']);
    } elseif ($itemID) {
        $query = "SELECT * FROM ".SELECTIONS_TABLE." WHERE id = $itemID LIMIT 1";
        $result = mysql_query($query);
        if (!$result) {
            $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
        } elseif(!mysql_num_rows($result)) {
            $arrPageData['errors'][] = "SELECT OPERATIONS: No this Item in DataBase";
        } else {
            $item = mysql_fetch_assoc($result);
            $item['createdDate'] = date('Y-m-d', strtotime($item['created']));
            $item['createdTime'] = date('H:i:s', strtotime($item['created']));
            $item['arHistory']   = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
            if($item['type'] == Selections::SELECTION_TYPE_CUSTOM) {
                $item['imagesCount'] = getValueFromDB(SELECTIONFILES_TABLE, "COUNT(*)", "WHERE `selection_id`=$itemID", "cnt");
            } else {
                $item['arProducts'] = getRowItems(SELECTION_PRODUCTS_TABLE.' sp LEFT JOIN '.PRINTS_TABLE.' p ON p.id=sp.product_id LEFT JOIN '.MAIN_TABLE.' m ON m.id=p.category_id', 'sp.id, sp.product_id, p.title, m.title category_title', 'sp.selection_id='.$itemID, 'sp.`order` ASC');
            }
        }
    }
    if (!empty($_POST)) $item = array_merge($item, $_POST);
} else {
    $query  = "SELECT * FROM `".SELECTIONS_TABLE."` ";
    $result = mysql_query($query);
    if($result) {
        while ($row = mysql_fetch_assoc($result)) {
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
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################