<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$itemID       = $UrlWL->getItemId();
$item         = array(); // Item Info Array
$items        = array(); // Items Array of items Info arrays
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['arPopular']     = getComplexRowItems(NEWS_TABLE, "*", "WHERE `active`>0", "`created` DESC", 4);

// Item Detailed View
if ($itemID and ($item = getSimpleItemRow($itemID, PRINT_TYPES_TABLE) and !empty($item))) {
    $arrPageData['headCss'][]  = '/css/smart/method.css';
    $arrPageData['headTitle']  = $item['title'];
    $arCategory['meta_descr']  = $item['meta_descr'];
    $arCategory['meta_key']    = $item['meta_key'];
    $arCategory['meta_robots'] = $item['meta_robots'];
    $arCategory['seo_title']   = $item['seo_title'];
    $item['fulldescr'] = unScreenData($item['fulldescr']);
    $item['redirecturl'] = trim($item['redirecturl']);
    $item['redirectid']  = intval($item['redirectid']);
    if ($item['redirecturl']!='') $item['redirectid'] = 0;
    elseif ($item['redirectid']>0) $item['redirecturl'] = $UrlWL->buildCategoryUrl(UrlWL::getCategoryByIdWithSeoPath($item['redirectid']));
    if (!empty($item['redirecturl'])) Redirect($item['redirecturl']);
// List Items
} else {
    // include page styles
    $arrPageData['headCss'][] = '/css/smart/methods.css';
    // IF you want to show all subcategories  products  - uncomment below line
    $query = 'SELECT t.* FROM `'.PRINT_TYPES_TABLE.'` t ';
    $where = 'WHERE t.`active`=1 ';
    $order  = 'ORDER BY t.`order`';
    $result = mysql_query($query.$where.$order) or die(strtoupper($module).' SELECT: ' . mysql_error());
    if(mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $row['descr'] = unScreenData($row['descr']);
            $row['image'] = (!empty($row['image']) and file_exists($arrPageData['files_path'].$row['image'])) ? $arrPageData['files_url'].$row['image'] : $arrPageData['files_url'].'noimage.jpg';
            $row['redirecturl'] = trim($row['redirecturl']);
            $row['redirectid']  = intval($row['redirectid']);
            if ($row['redirecturl']!='') $row['redirectid'] = 0;
            elseif ($row['redirectid']>0) $row['redirecturl'] = $UrlWL->buildCategoryUrl(UrlWL::getCategoryByIdWithSeoPath ($row['redirectid']));
            $items[] = $row;
        }
    }
}

$smarty->assign('item',  $item);
$smarty->assign('items', $items);