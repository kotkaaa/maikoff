<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

require_once('include/classes/Filters.php');
require_once('include/classes/product/PrintProduct.php');

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$sort          = intval($UrlWL->getParam(UrlWL::SORT_KEY_NAME, 0));
$pages         = trim(addslashes($UrlWL->getParam(UrlWL::PAGES_KEY_NAME, '')));
$livesearch    = !empty($_POST['livesearch'])  ? intval($_POST['livesearch']) : 0;
$searchtext    = !empty($_POST['stext'])  ? PHPHelper::prepareSearchText($_POST['stext'], true)  : '';
$searchtext    = (!$searchtext && !empty($_GET['stext'])) ? PHPHelper::prepareSearchText($_GET['stext'], true)  : $searchtext;
$itemID        = $UrlWL->getItemId();
$items         = array(); // Items Array of items Info arrays
$showSubItems  = true;
$showEmptyAttr = true; // Show or hide empty product attributes
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////// OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\\\\\
// Manipulation with Sort
if ($sort) {
    $sort = PHPHelper::getCorrectCatalogSort($sort);
    $_SESSION[MDATA_KNAME][$module][UrlWL::SORT_KEY_NAME] = &$sort;
} elseif ($itemID AND isset($_SESSION[MDATA_KNAME][$module][UrlWL::SORT_KEY_NAME]) ) {
    $sort = &$_SESSION[MDATA_KNAME][$module][UrlWL::SORT_KEY_NAME];
} elseif (isset($_SESSION[MDATA_KNAME][$module][UrlWL::SORT_KEY_NAME])) {
    unset($_SESSION[MDATA_KNAME][$module][UrlWL::SORT_KEY_NAME]);
}
// Manipulation with Page Number
if ($page > 1) {                                                         
    $_SESSION[MDATA_KNAME][$module]['page'] = &$page;
} elseif ($itemID AND isset($_SESSION[MDATA_KNAME][$module]['page']) ) {
    $page = &$_SESSION[MDATA_KNAME][$module]['page'];
} elseif (isset($_SESSION[MDATA_KNAME][$module]['page'])) {
    unset($_SESSION[MDATA_KNAME][$module]['page']);
}
// Manipulation with Show Pages All Session Var
if ($pages) {
    $_SESSION[MDATA_KNAME][$module]['pagesall'] = &$pages;
} elseif ($itemID AND isset($_SESSION[MDATA_KNAME][$module]['pagesall'])) {
    $pages = &$_SESSION[MDATA_KNAME][$module]['pagesall'];
} elseif (isset($_SESSION[MDATA_KNAME][$module]['pagesall'])) {
    unset($_SESSION[MDATA_KNAME][$module]['pagesall']);
}
//// Manipulation with Search text
//if ($searchtext) {
//    $_SESSION[MDATA_KNAME][$module]['stext'] = &$searchtext;
//} elseif (!empty($_SESSION[MDATA_KNAME][$module]['stext'])) {
//    $searchtext = &$_SESSION[MDATA_KNAME][$module]['stext'];
//}
// ////////// END OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['stext']         = $searchtext;
$arrPageData['pagesall']      = &$pages;
$arrPageData['backurl']       = $UrlWL->buildCategoryUrl($arCategory, ($pages ? UrlWL::PAGES_KEY_NAME.'='.UrlWL::PAGES_ALL_VAL : '').($sort ? UrlWL::SORT_KEY_NAME.'='.$sort : ''), '', $page);
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['items_on_page'] = $livesearch ? 5 : 36;
$arrPageData['search_result'] = '';
$arrPageData['headTitle']     = $arCategory['title'];
$arrPageData['headCss'][]     = '/css/smart/search.css';
$arrPageData['headScripts'][] = "/js/libs/history.js/bundled/html4+html5/jquery.history.min.js";
$arrPageData['headScripts'][] = "/js/smart/catalog.js";

if($searchtext && strlen($searchtext)>2) {
    $arrFields = array('pcode', 'title', 'meta_descr', 'meta_key', 'seo_title', 'seo_text');             
    $arFoundedIDX = getArrValueFromDB(PRINTS_TABLE, 'id', 'WHERE `active`=1 AND (LOWER('.implode(') LIKE "%'.$searchtext.'%" OR LOWER(', $arrFields).') LIKE "%'.$searchtext.'%")');
    if($arFoundedIDX) {
        require_once('include/classes/Filters.php');
        $Filters = new PrintFilters($UrlWL, $catid, array());
        $Filters->init($arFoundedIDX);
        $arrPageData['sort']      = ($sort = PHPHelper::getCorrectCatalogSort($sort));
        $arrPageData['arSorting'] = PHPHelper::getCatalogSorting($UrlWL, $sort);
        $arrPageData['filters']   = $Filters->getFilters();
        $arrPageData['selectedFilters'] = $UrlWL->getFilters()->getSelected();
        // Total pages and Pager
        $arrPageData['total_items'] = $Filters->getCount();
        $arrPageData['search_result'] = 'По запросу <strong>"'.$arrPageData['stext'].'"</strong> '.($arrPageData['total_items']>0 ? 'найдено '.$arrPageData['total_items'].' '.$HTMLHelper->getNumEnding($arrPageData['total_items'], array('товар', 'товара', 'товаров')) : 'ничего не найдено');
        if(!$pages){
            $arrPageData['pager']       = new Pager($UrlWL, $page, $arrPageData['total_items'], $arrPageData['items_on_page']);
            $arrPageData['total_pages'] = $arrPageData['pager']->getCount();
            $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
            // для манипуляций с последним/первым товаром на странице
            if($page > 1){
                $arrPageData['offset']--;
                if($page == $arrPageData['total_pages']){
                    $arrPageData['items_on_page']++;
                }
            }
            // если такой страницы нет - делаем переадресацию
            if ($arrPageData['pager']->isIncorrectPage()) {
                $UrlWL->redirectToErrorPage();
            }
        }
        // build main query
        $FilterQuery = $Filters->prepareQuery($arrPageData['selectedFilters']);
        $order = 'ORDER BY '.$arrPageData['arSorting'][$sort]['column'];
        $limit = (!$pages ? 'LIMIT '.$arrPageData['offset'].', '.$arrPageData['items_on_page'] : '');
        $query  = PrintProduct::getItemsSql($FilterQuery->getWhere(), $FilterQuery->getGroup(), $FilterQuery->getHaving(), $order, $limit, $sort);
        $result = mysql_query($query) or die(strtoupper($module).' SELECT: ' . mysql_error());
        if(mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                $row['is_fast_print'] = false;
                $items[] = PrintProduct::getItem($row, $UrlWL, $Filters->getSeparateByType(), $Filters->getSeparateByColor(), true, $sort);
            }
        }
        if ($page > 1) {
            $arrPageData['headTitle'] .= ' - Страница '.$page;
            $arCategory['meta_descr'] .= ' | Страница '.$page;
            $arCategory['seo_title']  .= ' | Страница '.$page;
            $arCategory['title']      .= ' - Страница '.$page;
        }
    } else {
        $arrPageData['search_result'] = 'По запросу <strong>"'.$arrPageData['stext'].'"</strong> ничего не найдено';
    }
} else {
    $arrPageData['search_result'] = FOUND_ERROR;
}
// Update page via Ajax
if ($IS_AJAX and $_SERVER["REQUEST_METHOD"]=="POST") {
    $smarty->assign('UrlWL',            $UrlWL);
    $smarty->assign('items',            $items);
    $smarty->assign('arrModules',       $arrModules);
    $smarty->assign('arrPageData',      $arrPageData);
    if($livesearch) {
        echo $smarty->fetch("ajax/live-search.tpl");
        die();
    } else {
        $smarty->assign('objSettingsInfo',  $objSettingsInfo);                        
        $smarty->assign('itemID',           $itemID);
        $smarty->assign('Basket',           $Basket);
        $smarty->assign('arCategory',       $arCategory);
        $smarty->assign('HTMLHelper',       $HTMLHelper);            
        $smarty->assign('sort',             $sort);
        $smarty->assign('arrPager',         $arrPageData['pager']);
        $smarty->assign('page',             $arrPageData['page']);
        $smarty->assign('arrBreadCrumb',    $arrPageData["arrBreadCrumb"]);
        $smarty->assign('IS_AJAX',          $IS_AJAX);
        $smarty->assign('showFirstLast',    true);
        $smarty->assign('subMenu',          getMenu(1, GetRootId($catid)));
        $smarty->assign('catalogMenu',      getMenu(6, $arrModules['prints']['id']));
        $json = array(
            "filters"           => $smarty->fetch("ajax/filter.tpl"),
            "filters_mobile"    => $smarty->fetch("ajax/filter-mobile.tpl"),
            "selected_filters"  => $smarty->fetch("ajax/selected_filters.tpl"),
            "control_view"      => $smarty->fetch("ajax/control-sort.tpl"),
            "breadcrumbs"       => $smarty->fetch("core/breadcrumb.tpl"),
            'pager'             => $smarty->fetch("core/pager.tpl"),
            "selected_count"    => count($arrPageData["selectedFilters"]),
            "url"               => $UrlWL->getUrl(),
            "h_title"           => ucfirst($arrPageData["headTitle"]),
            "seo_title"         => unScreenData($arCategory["seo_title"]),
            "meta_descr"        => unScreenData($arCategory["meta_descr"]),
            "meta_key"          => unScreenData($arCategory["meta_key"]),
            "seo_text"          => unScreenData($arCategory["seo_text"]),
            'meta_robots'       => $arCategory["meta_robots"],
            "total_pages"       => $arrPageData['total_pages'],
            'search_result'     => $arrPageData['search_result'],
        );
        if (isset($_POST["ajaxLoadMore"])) {
            $json["products"] = "";
            foreach ($items as $item) {
                $smarty->assign('item', $item);
                $json["products"] .= $smarty->fetch("core/product-print.tpl");
            }
        }
        if (isset($_POST["ajaxUpdate"])) {
            $json["products"] = $smarty->fetch("ajax/products.tpl");
        } die(json_encode($json));
    }        
}
$smarty->assign('items',         $items);