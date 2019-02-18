<?php defined('WEBlife') or die('Restricted access'); // no direct access

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$item = array();
$itemID = !empty($_GET["itemID"]) ? intval($_GET["itemID"]) : 0;
$arrPageData["itemID"] = $itemID;

$arrPageData['headCss'][]     = '<link href="/js/jquery/themes/smoothness/jquery.ui.all.css" type="text/css" rel="stylesheet"/>';
$arrPageData['headScripts'][] = '<script src="/js/admin/seo_filters.js" type="text/javascript"></script>';

if ($itemID and ($item = getSimpleItemRow($itemID, SEO_FILTERS_TABLE) and !empty($item))) {
    if (!empty($_POST) and $task=="editItem") {
        $arPost = $_POST;
        $result = $DB->postToDB($arPost, SEO_FILTERS_TABLE, "WHERE `id`=$itemID", array("submit"), "update");
        if ($result) $arrPageData["messages"][] = "Запись успешно сохранена!";
        else $arrPageData["errors"][] = "Запись не удалось сохранить! Возможная причина: ".mysql_error();
        $item = array_merge($item, $_POST);
    }
} else $arrPageData["errors"][] = "Такой записи не существует!";

$smarty->assign("item", $item);