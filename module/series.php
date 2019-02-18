<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

$pages         = trim(addslashes($UrlWL->getParam(UrlWL::PAGES_KEY_NAME, '')));
$sort          = intval($UrlWL->getParam(UrlWL::SORT_KEY_NAME, 0));
$itemID        = $UrlWL->getItemId();
$cid           = (!empty($_GET['cid']) && intval($_GET['cid']))? intval($_GET['cid']): false;
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$images_params = SystemComponent::prepareImagesParams(getValueFromDB(IMAGES_PARAMS_TABLE, 'aliases', 'WHERE `module`="catalog"'));

if ($page > 1) {
    $_SESSION[MDATA_KNAME][$module]['page'] = &$page;
} elseif ($itemID && isset($_SESSION[MDATA_KNAME][$module]['page'])) {
    $page = &$_SESSION[MDATA_KNAME][$module]['page'];
} elseif (isset($_SESSION[MDATA_KNAME][$module]['page'])) {
    unset($_SESSION[MDATA_KNAME][$module]['page']);
}
// Manipulation with Show Pages All Session Var
if ($pages) {
    $_SESSION[MDATA_KNAME][$module]['pagesall'] = &$pages;
} elseif ($itemID && isset($_SESSION[MDATA_KNAME][$module]['pagesall'])) {
    $pages = &$_SESSION[MDATA_KNAME][$module]['pagesall'];
} elseif (isset($_SESSION[MDATA_KNAME][$module]['pagesall'])) {
    unset($_SESSION[MDATA_KNAME][$module]['pagesall']);
}

$arrPageData['cid']           = $cid;
$arrPageData['pagesall']      = &$pages;
$arrPageData['backurl']       = $UrlWL->buildCategoryUrl($arCategory, ($pages ? 'pages=all' : ''), '', $page);
$arrPageData['files_url']     = UPLOAD_URL_DIR.'catalog/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['items_on_page'] = 36;

// Item Detailed View
if ($itemID and $item = getSimpleItemRow($itemID, SERIES_TABLE)) {
    // include page css
    $arrPageData['headCss'][]     = '/css/smart/series.css';
    $arrPageData['headScripts'][] = "/js/libs/history.js/bundled/html4+html5/jquery.history.min.js";
    $arrPageData['headScripts'][] = "/js/smart/catalog.js";
    // set vars
    $item["brand"] = getSimpleItemRow($item["brand_id"], BRANDS_TABLE);
    $item["brand_title"] = $item["brand"]["title"];
    $item["sizes"] = array();
    $item["attributes"] = array();
    $arrPageData["arrBreadCrumb"] = [
        $UrlWL->buildCategoryUrl($arrModules["brands"]) => $arrModules["brands"]["title"],
        $UrlWL->buildItemUrl($arrModules["brands"], $item["brand"]) => $item["brand"]["title"],
        $UrlWL->buildItemUrl($arCategory, $item) => $item["title"],
    ];
    // init filters
    require_once('include/classes/Filters.php');
    require_once('include/classes/product/CatalogProduct.php');
    $Filters = new BrandFilters($UrlWL, $catid, $item["brand_id"]);
    $Filters->init();
    $arrPageData['sort']      = ($sort = PHPHelper::getCorrectCatalogSort($sort));
    $arrPageData['arSorting'] = PHPHelper::getCatalogSorting($UrlWL, $sort);
    $arrPageData['filters']   = $Filters->getFilters(true);
    $arrPageData['selectedFilters'] = $UrlWL->getFilters()->getSelected();
    // Total pages and Pager
    $arrPageData['total_items'] = $Filters->getCount();
    if (!$pages) {
        $arrPageData['pager']       = new Pager($UrlWL, $page, $arrPageData['total_items'], $arrPageData['items_on_page']);
        $arrPageData['total_pages'] = $arrPageData['pager']->getCount();
        $arrPageData['offset']      = ($page-1)*$arrPageData['items_on_page'];
        // для манипуляций с последним/первым товаром на странице
        if ($page > 1) {
            $arrPageData['offset']--;
            if ($page == $arrPageData['total_pages']){
                $arrPageData['items_on_page']++;
            }
        }
        // если такой страницы нет - делаем переадресацию
        if ($arrPageData['pager']->isIncorrectPage()) {
            $UrlWL->redirectToErrorPage();
        }
    }
    // build main query
    $FilterQuery = new \Filters\FilterQuery();
    $FilterQuery->where = "ti.`is_deleted`=0 AND ti.`brand_id`={$item["brand_id"]} AND ti.`series_id`=$itemID";
    $FilterQuery->group = "ti.`model_id`";
    $order  = 'ORDER BY '.$arrPageData['arSorting'][$sort]['column'];
    $limit  = (!$pages ? 'LIMIT '.$arrPageData['offset'].', '.$arrPageData['items_on_page'] : '');
    $query  = CatalogProduct::getItemsSql($FilterQuery->getWhere(), $FilterQuery->getGroup(), $FilterQuery->getHaving(), $order, $limit);
    $result = mysql_query($query) or die(strtoupper($module).' SELECT: ' . mysql_error());
    if ($result and mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $row['arCategory'] = $arrModules["catalog"];
            $row = CatalogProduct::getItem($row, $UrlWL, UPLOAD_URL_DIR.'catalog/', $images_params, true);
            $row["title"] = trim((!empty($row["type"]["title_short"]) ? $row["type"]["title_short"] : $row["type"]["title"])." ".(!empty($row["series"]) ? $row["series"]["title"] : "")." ".$row["pcode"]);
            $items[] = $row;
        }
    }
    // meta data
    $arrPageData["headTitle"] = ProductMetaHelper::generate("Одежда {brand} {title}", $item);
    $arCategory["seo_title"]  = CategoryMetaHelper::generate((!empty($item["seo_title"])  ? $item["seo_title"]  : $arCategory["seo_title"]), $item);
    $arCategory["meta_descr"] = CategoryMetaHelper::generate((!empty($item["meta_descr"]) ? $item["meta_descr"] : $arCategory["meta_descr"]), $item);
    $arCategory["meta_key"]   = CategoryMetaHelper::generate((!empty($item["meta_key"])   ? $item["meta_key"]   : $arCategory["meta_key"]), $item);
    $arCategory["seo_text"]   = CategoryMetaHelper::generate((!empty($item["seo_text"])   ? $item["seo_text"]   : $arCategory["seo_text"]), $item);
    // Update page via Ajax
    if ($IS_AJAX and $_SERVER["REQUEST_METHOD"]=="POST") {
        $smarty->assign('objSettingsInfo',  $objSettingsInfo);
        $smarty->assign('UrlWL',            $UrlWL);
        $smarty->assign('items',            $items);
        $smarty->assign('itemID',           $itemID);
        $smarty->assign('Basket',           $Basket);
        $smarty->assign('arCategory',       $arCategory);
        $smarty->assign('HTMLHelper',       $HTMLHelper);
        $smarty->assign('arrModules',       $arrModules);
        $smarty->assign('arrPageData',      $arrPageData);
        $smarty->assign('sort',             $sort);
        $smarty->assign('arrPager',         $arrPageData['pager']);
        $smarty->assign('page',             $arrPageData['page']);
        $smarty->assign('arrBreadCrumb',    $arrPageData["arrBreadCrumb"]);
        $smarty->assign('IS_AJAX',          $IS_AJAX);
        $smarty->assign('showFirstLast',    true);
        $json = array(
            "filters"           => $smarty->fetch("ajax/filter.tpl"),
            "filters_mobile"    => $smarty->fetch("ajax/filter-mobile.tpl"),
            "selected_filters"  => $smarty->fetch("ajax/selected_filters.tpl"),
            "control_view"      => $smarty->fetch("ajax/control-sort.tpl"),
            "breadcrumbs"       => $smarty->fetch("core/breadcrumb.tpl"),
            'pager'             => $smarty->fetch("core/pager.tpl"),
            "url"               => $UrlWL->getUrl(),
            "selected_count"    => count($arrPageData["selectedFilters"]),
            "h_title"           => unScreenData(ucfirst($arrPageData["headTitle"])),
            "seo_title"         => unScreenData($arCategory["seo_title"]),
            "meta_descr"        => unScreenData($arCategory["meta_descr"]),
            "meta_key"          => unScreenData($arCategory["meta_key"]),
            "seo_text"          => unScreenData($arCategory["seo_text"]),
            'meta_robots'       => $arCategory["meta_robots"],
            "total_pages"       => $arrPageData['total_pages'],
            "products"          => $smarty->fetch("ajax/products.tpl")
        ); die(json_encode($json));
    }
}

$smarty->assign('item',  $item);
$smarty->assign('items', $items);