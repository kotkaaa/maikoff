<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$itemID       = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$selectionID  = (isset($_GET['selectionID']) && intval($_GET['selectionID']))       ? intval($_GET['selectionID'])    : 0;
$items        = array(); // Items Array of items Info arrays

$arrPageData['itemID']        = $itemID;
$arrPageData['selectionID']   = $selectionID;
$arrPageData['current_url']   = $arrPageData['admin_url'].($selectionID ? '&selectionID='.$selectionID : '');
$arrPageData['files_url']     = UPLOAD_URL_DIR.'selections/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url'], true);
$arrPageData['items_on_page'] = 20;
$arrPageData['userfilesmax']  = 100;
// Delete Item
if($itemID && $task=='deleteItem') {
    unlinkImage($itemID, SELECTIONFILES_TABLE, $arrPageData['files_path'], false, true, 'filename', true);
    if(deleteRecords(SELECTIONFILES_TABLE, ' WHERE `id`='.$itemID)) {
        setSessionMessage('Запись удалена!');
        Redirect($arrPageData['current_url']);
    } else $arrPageData['errors'][] = 'Данные не удалось удалить. Возможная причина - <p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>';
}
// Set Active Status Item
elseif($itemID && $task=='publishItem' && isset($_GET['status'])) {
    if(updateRecords(SELECTIONFILES_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID)) {
        setSessionMessage('Новое состояние успешно сохранено!');
        Redirect($arrPageData['current_url']);
    } else $arrPageData['errors'][]   = 'Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error();
} elseif (!empty($_GET['arItems']) && ($arItems = $_GET['arItems']) && $task == 'publishItems'){
    $result = updateItems(array('active'=>$arItems), $arItems, 'id', SELECTIONFILES_TABLE);
    if ($result === true) {
        setSessionMessage('Новое состояние успешно сохранено!');
        Redirect($arrPageData['current_url']);
    } elseif ($result === false) {
        setSessionMessage('Не нуждается в сохранении!');
        Redirect($arrPageData['current_url']);
    } else $arrPageData['errors'][] = $result;
}
elseif(!empty($_GET['arItems']) && ($arItems = $_GET['arItems']) && $task == 'deleteItems'){
    unlinkImages($arrPageData['files_path'], SELECTIONFILES_TABLE, 'filename', "WHERE `id` IN (".implode(',' , array_keys($arItems)).")", '', false, false, false);
    if(deleteRecords(SELECTIONFILES_TABLE, " WHERE `id` IN (".implode(',' , array_keys($arItems)).")")) {
        setSessionMessage('Новое состояние успешно сохранено!');
        Redirect($arrPageData['current_url']);
    } else $arrPageData['errors'][] = 'Ошибка удаления файлов!';
}
// Insert Or Update Item in Database
elseif(!empty($_POST['arData']) && $task=='editItems') {
    foreach($_POST['arData'] as $id => $data) {
        if($DB->postToDB($data, SELECTIONFILES_TABLE, 'WHERE `id`='.$id,  array(), 'update', false)) {
            $arrPageData['messages'][] = 'Запись с ID='.$id.' успешно сохранена!';
        } else {
            $arrPageData['errors'][] = 'Запись с ID='.$id.' <font color="red">НЕ была сохранена</font>!';
        }
    } 
    if(empty($arrPageData['errors'])) {
        setSessionMessage(implode('<br/>', $arrPageData['messages']));
        Redirect($arrPageData['current_url']);
    } 
}
// Upload images via Ajax
elseif ($selectionID and !empty($_POST) and $task == 'ajaxSelectionFilesUpload') {
    $json = array();
    $Validator->validateGeneral($_POST["filename"], "Не выбран файл для загрузки!!!");
    if ($Validator->foundErrors()) {
        $json["errors"] = $Validator->getErrors();
    } else {
        $ext = getFileExt($_POST["filename"]);
        $filename = basename($_POST["filename"], $ext).$ext;
        $tmp_url  = UPLOAD_URL_DIR."temp/";
        $tmp_dir  = prepareDirPath($tmp_url);
        if (file_exists($tmp_dir.$filename)) {
            $dest_url = UPLOAD_URL_DIR."selections/";
            $dest_dir = prepareDirPath($dest_url);
            $new_name = createUniqueFileName($dest_dir, $ext, $filename);
            @copy($tmp_dir.$filename, $dest_dir.$new_name);
            if (file_exists($dest_dir.$new_name)) {
                $arData = array(
                    "selection_id" => $selectionID,
                    "filename"     => $new_name,
                    "order"        => getMaxPosition($selectionID, "order", "selection_id", SELECTIONFILES_TABLE),
                    "active"       => 1,
                    "created"      => date("Y-m-d H:i:s")
                ); $itemID = $DB->postToDB($arData, SELECTIONFILES_TABLE);
                if ($itemID and is_int($itemID)) {
                    $arData["id"] = $itemID;
                    $json["item"] = $arData;
                    @unlinkFile($tmp_dir.$filename);
                    @unlinkFile($tmp_dir.'thumbnail/'.$filename);
                } else $json["errors"][] = "Возникла ошибка при записи в базу данных!!!";
            } else $json["errors"][] = "Возникла ошибка при копировании файла в целевую папку!!!";
        } else $json["errors"][] = "Выбранный файл отсутствует на сервере!!!";
    } die(json_encode($json));
}

// Include Need CSS and Scripts For This Page To Array
$arrPageData['headScripts'][]  = '<script src="/js/libs/blueimp-file-upload/js/jquery.fileupload.min.js" type="text/javascript"></script>';
$arrPageData['headScripts'][]  = '<script src="/js/libs/blueimp-file-upload/js/jquery.iframe-transport.min.js" type="text/javascript"></script>';
// Display Items List Data
$where = "WHERE `selection_id` = $selectionID";
// Total pages and Pager
$arrPageData['total_items'] = intval(getValueFromDB(SELECTIONFILES_TABLE." t", 'COUNT(*)', $where, 'count'));
$arrPageData['pager']       = getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $arrPageData['current_url']);
$arrPageData['total_pages'] = $arrPageData['pager']['count'];
$arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
// END Total pages and Pager
$arrPageData['user_can_upload'] = $arrPageData['userfilesmax']-intval($arrPageData['total_items']);
if ($arrPageData['user_can_upload']<0) $arrPageData['user_can_upload'] = 0;
$order = "ORDER BY `order` ASC";
$limit = "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";
$query = "SELECT * FROM `".SELECTIONFILES_TABLE."` $where $order $limit";
$result = mysql_query($query);
if ($result) {
    while (($row = mysql_fetch_assoc($result))) {
        $items[] = $row;
    }
} else {
    $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
}

$smarty->assign('items',        $items);#######