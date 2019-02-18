<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$itemID = !empty($_GET["itemID"]) ? intval($_GET["itemID"]) : false;
$item   = array();
$items  = array();
$item_title = $itemID ? getValueFromDB(COLORS_TABLE, "title", "WHERE `id`=$itemID") : "";

$arrPageData['itemID']      = $itemID;
$arrPageData['current_url'] = $arrPageData['admin_url'].($itemID ? "&itemID={$itemID}" : "");
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headScripts'][] = '<script src="/js/libs/jscolor/jscolor.min.js" type="text/javascript"></script>';
$arrPageData['headScripts'][] = '<script src="/js/admin/colors.js" type="text/javascript"></script>';
// Delete item
if ($itemID and $task=="deleteItem") {
    $result = deleteDBLangsSync(COLORS_TABLE, "WHERE `id`=$itemID");
    if ($result) {
        foreach(SystemComponent::getAcceptLangs() as $key => $arLang) {
            ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item_title.'"', $key, $item_title, 0, $arrPageData['module']);
        } setSessionMessage("Цвет \"{$arItem['title']}\" удален!");
    } else {
        setSessionErrors("Цвет \"{$arItem['title']}\" не был удален!");
    } Redirect($arrPageData["admin_url"]);
}
// Add item
if (!empty($_POST) and $task=="addItem") {
    $Validator->validateGeneral($_POST["title"], "Не указано название цвета!");
    $Validator->validateByPattern("/[A-z0-9]{6}/", $_POST["hex"], "Не указан hex-код цвета!");
    if (!$Validator->foundErrors()) {
        $cnt = getValueFromDB(COLORS_TABLE, "COUNT(*)", "WHERE `title`='{$_POST["title"]}'", "cnt");
        if ($cnt > 0) $Validator->addError ("Цвет с названием \"{$_POST["title"]}\" уже существует");
        $cnt = getValueFromDB(COLORS_TABLE, "COUNT(*)", "WHERE `hex`='{$_POST["hex"]}'", "cnt");
        if ($cnt > 0) $Validator->addError ("Цвет с кодом \"{$_POST["hex"]}\" уже существует");
    }
    if ($Validator->foundErrors()) {
        $arrPageData["errors"][] = $Validator->getListedErrors();
        $item = array_merge($item, $_POST);
    } else {
        $arItem = $_POST;
        $arItem["order"] = getMaxPosition(0, "order", false, COLORS_TABLE);
        $result = $DB->postToDB($arItem, COLORS_TABLE, array(), array(), "insert", true);
        if ($result) {
            foreach(SystemComponent::getAcceptLangs() as $key => $arLang) {
                ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Добавлено "'.$arItem['title'].'"', $key, $arItem['title'], 0, $arrPageData['module']);
            } setSessionMessage("Цвет \"{$arItem['title']}\" успешно добавлен!");
        }
    } 
}
// update items
if (!empty($_POST) and $task=="reorderItems" and !empty($_POST["arItems"])) {
    $i=0;
    foreach ($_POST["arItems"] as $id=>$arItem) {
        $_Validator   = new Validator();
        $exists       = !empty($arItem['id']) ? getItemRow(COLORS_TABLE, "*", "WHERE `id`={$arItem['id']}") : array();
        $query_type   = !empty($exists) ? "update" : "insert";
        $whereOptions = !empty($exists) ? "WHERE `id`={$exists["id"]}" : "";        
        $_Validator->validateGeneral($arItem["title"], "Не указано название цвета!");
        $_Validator->validateGeneral($arItem["hex"], "Не указан hex-код цвета!");
        if (!$_Validator->foundErrors()) {
            $cnt = getValueFromDB(COLORS_TABLE, "COUNT(*)", "WHERE `title`='{$arItem["title"]}'".(!empty($exists) ? " AND `id`!={$exists["id"]}" : ""), "cnt");
            if ($cnt > 0) $_Validator->addError ("Цвет с названием \"{$arItem["title"]}\" уже существует");
            $cnt = getValueFromDB(COLORS_TABLE, "COUNT(*)", "WHERE `hex`='{$arItem["hex"]}'".(!empty($exists) ? " AND `id`!={$exists["id"]}" : ""), "cnt");
            if ($cnt > 0) $_Validator->addError ("Цвет с кодом \"{$arItem["hex"]}\" уже существует");
        }
        if ($_Validator->founderrors()) {
            $arrpageData['errors'][] = $_Validator->getlistedErrors();
        } else {
            // SEO path manipulation
            if (($arItem["seo_path"] = trim($arItem["seo_path"]))!=='' and ($arItem["seo_path"] = Url::stringToUrl($arItem["seo_path"]))!=='') {
                $arItem['seo_path']  = $UrlWL->strToUniqueUrl($DB, $arItem['seo_path'], "color", COLORS_TABLE, (empty($exists) ? 0 : $exists['id']), empty($exists));
            }
            $arItem["order"] = ++$i;
            $result = $DB->postToDB($arItem, COLORS_TABLE, $whereOptions, array("id"), $query_type, (!empty($exists) ? false : true));
            if ($result) {
                $arrPageData["messages"][] = "Цвет \"{$arItem['title']}\" успешно сохранен!";
                foreach(SystemComponent::getAcceptLangs() as $key => $arLang) {
                    ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Изменено "'.$arItem['title'].'"', $key, $arItem['title'], 0, $arrPageData['module']);
                }
            }
        }
    }
}
// Select items
$query  = "SELECT t.*, IF((SELECT COUNT(id) FROM ".CATALOG_TABLE." WHERE color_id=t.id)>0,0,1) AS `edit` FROM `".COLORS_TABLE."` t ORDER BY t.`order`";
$result = mysql_query($query);
if ($result and mysql_num_rows($result)>0) {
    while ($row = mysql_fetch_assoc($result)) {
        $items[] = $row;
    }
}

$smarty->assign("item",  $item);
$smarty->assign("items", $items);