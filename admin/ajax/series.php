<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

$itemID = !empty($_GET["itemID"]) ? intval($_GET["itemID"]) : false;
$item   = getSimpleItemRow($itemID, SERIES_TABLE);
$item_title = getValueFromDB(SERIES_TABLE, "title", "WHERE `id`=$itemID") or die("Undefined item id or item not found!");

$arrPageData['itemID']        = $itemID;
$arrPageData['current_url']   = $arrPageData['admin_url']."&itemID={$itemID}";
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headCss'][]     = '<link href="/js/jquery/themes/base/jquery.ui.all.css" type="text/css" rel="stylesheet" />';
$arrPageData['headScripts'][] = '<script src="/js/jquery/ui/jquery.ui.core.js" type="text/javascript"></script>';
$arrPageData['headScripts'][] = '<script src="/js/jquery/ui/jquery.ui.widget.js" type="text/javascript"></script>';

// Edit item
if (!empty($_POST) and $task=="editItem") {
    $arPostData = $_POST;
    $query_type = "update";
    $arUnusedKeys = array();
    $whereOptions = "WHERE `id`=$itemID";
    $result = $DB->postToDB($arPostData, SERIES_TABLE, $whereOptions, $arUnusedKeys, $query_type, false);
    if ($result) {
        $arrPageData["messages"][] = "Запись успешно сохранена!";
        foreach (SystemComponent::getAcceptLangs() as $key => $arLang) {
            ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Изменено "'.$arItem['title'].'"', $key, $arPostData['title'], 0, $arrPageData['module']);
        }
    } else $arrPageData["errors"][] = "Запись не была сохранена! Возможная причина: ".mysql_error();
}

$smarty->assign("item",  $item);