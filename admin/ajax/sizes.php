<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

$itemID = !empty($_GET["itemID"]) ? intval($_GET["itemID"]) : false;
$item   = array();
$items  = array();
$item_title = $itemID ? getValueFromDB(SIZES_TABLE, "title", "WHERE `id`=$itemID") : "";

$arrPageData['itemID']        = $itemID;
$arrPageData['current_url']   = $arrPageData['admin_url'].($itemID ? "&itemID={$itemID}" : "");
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headScripts'][] = '<script src="/js/admin/sizes.js" type="text/javascript"></script>';
// Delete item
if ($itemID and $task=="deleteItem") {
    $result = deleteDBLangsSync(SIZES_TABLE, "WHERE `id`=$itemID");
    if ($result) {
        foreach(SystemComponent::getAcceptLangs() as $key => $arLang) {
            ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item_title.'"', $key, $item_title, 0, $arrPageData['module']);
        } setSessionMessage("Размер \"{$arItem['title']}\" удален!");
    } else {
        setSessionErrors("Размер \"{$arItem['title']}\" не был удален!");
    } Redirect($arrPageData["admin_url"]);
}
// Add item
if (!empty($_POST) and $task=="addItem") {
    $Validator->validateGeneral($_POST["title"], "Не указано название цвета!");
    if (!$Validator->foundErrors()) {
        $cnt = getValueFromDB(SIZES_TABLE, "COUNT(*)", "WHERE `title`='{$_POST["title"]}'", "cnt");
        if ($cnt > 0) $Validator->addError ("Размер с названием \"{$_POST["title"]}\" уже существует");
    }
    if ($Validator->foundErrors()) {
        $arrPageData["errors"][] = $Validator->getListedErrors();
        $item = array_merge($item, $_POST);
    } else {
        $arItem = $_POST;
        $arItem["order"] = getMaxPosition(0, "order", false, SIZES_TABLE);
        $result = $DB->postToDB($arItem, SIZES_TABLE, array(), array(), "insert", true);
        if ($result) {
            foreach (SystemComponent::getAcceptLangs() as $key => $arLang) {
                ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Добавлено "'.$arItem['title'].'"', $key, $arItem['title'], 0, $arrPageData['module']);
            } setSessionMessage("Размер \"{$arItem['title']}\" успешно добавлен!");
        }
    } 
}
// update items
if (!empty($_POST) and $task=="reorderItems" and !empty($_POST["arItems"])) {
    $i=0;
    foreach ($_POST["arItems"] as $id=>$arItem) {
        $_Validator   = new Validator();
        $exists       = !empty($arItem['id']) ? getItemRow(SIZES_TABLE, "*", "WHERE `id`={$arItem['id']}") : array();
        $query_type   = !empty($exists) ? "update" : "insert";
        $whereOptions = !empty($exists) ? "WHERE `id`={$exists["id"]}" : "";        
        $_Validator->validateGeneral($arItem["title"], "Не указано название цвета!");
        if (!$_Validator->foundErrors()) {
            $cnt = getValueFromDB(SIZES_TABLE, "COUNT(*)", "WHERE `title`='{$arItem["title"]}'".(!empty($exists) ? " AND `id`!={$exists["id"]}" : ""), "cnt");
            if ($cnt > 0) $_Validator->addError ("Размер с названием \"{$arItem["title"]}\" уже существует");
        }
        if ($_Validator->foundErrors()) {
            $arrpageData['errors'][] = $_Validator->getlistedErrors();
        } else {
            $arItem["order"] = ++$i;
            $result = $DB->postToDB($arItem, SIZES_TABLE, $whereOptions, array("id"), $query_type, (!empty($exists) ? false : true));
            if ($result) {
                $arrPageData["messages"][] = "Размер \"{$arItem['title']}\" успешно сохранен!";
                foreach (SystemComponent::getAcceptLangs() as $key => $arLang) {
                    ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Изменено "'.$arItem['title'].'"', $key, $arItem['title'], 0, $arrPageData['module']);
                }
            }
        }
    }
}
// Select items
$query  = "SELECT t.*, IF((SELECT COUNT(id) FROM ".PRODUCT_SIZES_TABLE." WHERE size=t.title)>0,0,1) AS `edit` FROM `".SIZES_TABLE."` t ORDER BY t.`order`";
$result = mysql_query($query);
if ($result and mysql_num_rows($result)>0) {
    while ($row = mysql_fetch_assoc($result)) {
        $items[] = $row;
    }
}

$smarty->assign("item",  $item);
$smarty->assign("items", $items);