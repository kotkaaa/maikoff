<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

require_once('include/classes/product/PrintProduct.php');

$arrItems = array();
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headCss'][]     = "/css/smart/home.css";
$arrPageData['headScripts'][] = "/js/smart/home".(!$IS_DEV ? ".min" : "").".js";
/*
// -----------------------------------------------------------------------------
// FILL ARRAY*/
if (($arrItems['brands'] = PHPHelper::getMemCache()->get(CacheWL::KEY_HOME_BRANDS)) === false) {
    $arrItems['brands'] = array();
    $files_url  = UPLOAD_URL_DIR.'brands/';
    $files_path = prepareDirPath($files_url);
    $squery = 'SELECT * FROM `'.BRANDS_TABLE.'` WHERE `active`=1 ORDER BY `order`';
    $result = mysql_query($squery);
    while($row = mysql_fetch_assoc($result)){
        $row['title'] = unScreenData($row['title']);
        $row['image'] = (!empty($row['image']) and is_file($files_path.$row['image'])) ? $files_url.$row['image'] : $files_url.'noimage.jpg';
        $arrItems['brands'][] = $row;
    } PHPHelper::getMemCache()->set(CacheWL::KEY_HOME_BRANDS, $arrItems['brands'], 3600 * 4);
}
/*
// -----------------------------------------------------------------------------
// FILL ARRAY*/
if (($arrItems['popular'] = PHPHelper::getMemCache()->get(CacheWL::KEY_HOME_POPULAR)) === false) {
    $arrItems['popular'] = array();
    $query  = " SELECT p.*, t.*, p.`assortment_id` AS `id`, (0) AS `commentsCnt`
                FROM (
                    SELECT ti.*, ti.`product_price` `price`, ti.`product_sequence` `sequence`, IF(ti.`shortcut_id`>0 ,1 ,0) `isshortcut`, IF(ti.`shortcut_id`>0, ti.`category_id`, 0) `scid`
                    FROM `ru_print_index` ti
                    WHERE ti.`is_deleted`=0
                    GROUP BY ti.`assortment_id`
                ) p
                INNER JOIN `ru_prints` t ON (t.`id` = p.`print_id` AND  t.`pcode` IN('2229', '5352', '5218', '6756', '0781', '4935'))
                GROUP BY t.`id`
                ORDER BY RAND()
                LIMIT 10";
    $result = mysql_query($query) or die(strtoupper($module).' SELECT: ' . mysql_error());
    if (mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $arrItems['popular'][] = PrintProduct::getItem($row, $UrlWL, false, false, true, PHPHelper::getDefaultCatalogSort());
        }
    } PHPHelper::getMemCache()->set(CacheWL::KEY_HOME_POPULAR, $arrItems['popular'], 3600 * 4);
}

$smarty->assign('arrItems', $arrItems);