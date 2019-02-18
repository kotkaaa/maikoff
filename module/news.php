<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$pages_all    = !empty($_GET['pages']) ? trim(addslashes($_GET['pages'])) : false;
$itemID       = $UrlWL->getItemId();
$item         = array(); // Item Info Array
$items        = array(); // Items Array of items Info arrays
$showSubItems = true;

if ($page > 1)                                                          $_SESSION[MDATA_KNAME][$module]['page'] = &$page;
elseif ($itemID && isset($_SESSION[MDATA_KNAME][$module]['page']) )     $page = &$_SESSION[MDATA_KNAME][$module]['page'];
elseif (isset($_SESSION[MDATA_KNAME][$module]['page']))                 unset($_SESSION[MDATA_KNAME][$module]['page']);
// Manipulation with Show Pages All Session Var
if ($pages_all)                                                         $_SESSION[MDATA_KNAME][$module]['pagesall'] = &$pages_all;
elseif ($itemID && isset($_SESSION[MDATA_KNAME][$module]['pagesall']))  $pages_all = &$_SESSION[MDATA_KNAME][$module]['pagesall'];
elseif (isset($_SESSION[MDATA_KNAME][$module]['pagesall']))             unset($_SESSION[MDATA_KNAME][$module]['pagesall']);

$arrPageData['pagesall']      = &$pages_all;
$arrPageData['backurl']       = $UrlWL->buildCategoryUrl($arCategory, ($pages_all ? 'pages=all' : ''), '', $page);
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['items_on_page'] = 4;
$arrPageData['arPopular']     = getComplexRowItems(NEWS_TABLE, "*", "WHERE `active`>0".($itemID ? " AND `id`!=$itemID" : ""), "`created` DESC", 4);

// Item Detailed View
if ($itemID and ($item = getSimpleItemRow($itemID, NEWS_TABLE) and !empty($item))) {
    $arrPageData['headCss'][]  = '/css/smart/news-more.css';
    $arrPageData['headTitle']  = $item['title'];
    $arCategory['meta_descr']  = $item['meta_descr'];
    $arCategory['meta_key']    = $item['meta_key'];
    $arCategory['meta_robots'] = $item['meta_robots'];
    $arCategory['seo_title']   = $item['seo_title'];
    $item['descr']     = unScreenData($item['descr']);
    $item['fulldescr'] = unScreenData($item['fulldescr']);
// List Items
} else {
    // include page styles
    $arrPageData['headCss'][] = '/css/smart/news.css';
    // IF you want to show all subcategories  products  - uncomment below line
    $arChildrensID = $showSubItems ? getChildrensIDs($catid, true) : 0;
    $query = 'SELECT t.* FROM `'.NEWS_TABLE.'` t ';
    $where = 'WHERE t.`active`=1 ';
    if (!$pages_all) {
        $arrPageData['total_items'] = intval(getValueFromDB(NEWS_TABLE.' t', 'COUNT(*)', $where, 'cnt'));
        $arrPageData['pager']       = new Pager($UrlWL, $page, $arrPageData['total_items'], $arrPageData['items_on_page']);//getPager($page, $arrPageData['total_items'], $arrPageData['items_on_page'], $UrlWL->buildPagerUrl($arCategory));
        $arrPageData['total_pages'] = $arrPageData['pager']->getCount();
        $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
    }
    $order  = 'ORDER BY t.`created` DESC ';
    $limit  = $pages_all ? '' : "LIMIT {$arrPageData['offset']}, {$arrPageData['items_on_page']}";
    $result = mysql_query($query.$where.$order.$limit) or die(strtoupper($module).' SELECT: ' . mysql_error());
    if(mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $row['arCategory'] = ($row['cid']>0 and $row['cid']!=$catid) ? $UrlWL->getCategoryById($row['cid']) : $arCategory;
            $row['descr']      = unScreenData($row['descr']);
            $row['image']      = (!empty($row['image']) and file_exists($arrPageData['files_path'].$row['image'])) ? $arrPageData['files_url'].$row['image'] : $arrPageData['files_url'].'noimage.jpg';
            $items[] = $row;
        }
    }
    if ($page > 1) {
        $arrPageData['headTitle'] .= ' - Страница '.$page;
        $arCategory['meta_descr'] .= ' | Страница '.$page;
        $arCategory['seo_title']  .= ' | Страница '.$page;
        $arCategory['title']      .= ' - Страница '.$page;
    }
}

$smarty->assign('item',         $item);
$smarty->assign('items',        $items);