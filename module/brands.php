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
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['items_on_page'] = 36;

// Item Detailed View
if ($itemID and $item = getSimpleItemRow($itemID, BRANDS_TABLE)) {
    // include page css
    $arrPageData['headCss'][]     = '/css/smart/brand.css';
    $arrPageData['headScripts'][] = "/js/libs/history.js/bundled/html4+html5/jquery.history.min.js";
    $arrPageData['headScripts'][] = "/js/smart/catalog.js";
    // Set vars
    $item["series"] = getComplexRowItems(SERIES_TABLE, "*", "WHERE `brand_id`=$itemID", "`order`");
    $item['descr']  = unScreenData($item['descr']);
    $item['image']  = (!empty($item['image']) and file_exists($arrPageData['files_path'].$item['image'])) ? $arrPageData['files_url'].$item['image'] : $arrPageData['files_url'].'noimage.jpg';
    // item gallery
    $item['gallery'] = array();
    $q = "SELECT * FROM `".BRANDS_GALLERY_TABLE."` WHERE `pid`={$itemID} AND `active`>0 ORDER BY `fileorder`";
    $r = mysql_query($q);
    if ($r and mysql_num_rows($r)) {
        $files_url  = UPLOAD_URL_DIR."brands/{$itemID}/";
        $files_path = prepareDirPath($files_url);
        while ($img = mysql_fetch_assoc($r)) {
            $img["image"] = (!empty($img["filename"]) and file_exists($files_path.$img["filename"])) ? $files_url.$img["filename"] : $arrPageData['files_url'].'noimage.jpg';
            $img["title"] = unScreenData($img["title"]);
            $item['gallery'][] = $img;
        }
    }
    require_once('include/classes/Filters.php');
    require_once('include/classes/product/CatalogProduct.php');
    // init filters
    $Filters = new BrandFilters($UrlWL, $catid, $itemID);
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
    $FilterQuery = $Filters->prepareQuery($arrPageData['selectedFilters']);
    $order  = 'ORDER BY '.$arrPageData['arSorting'][$sort]['column'];
    $limit  = (!$pages ? 'LIMIT '.$arrPageData['offset'].', '.$arrPageData['items_on_page'] : '');
    $query  = CatalogProduct::getItemsSql($FilterQuery->getWhere(), $FilterQuery->getGroup(), $FilterQuery->getHaving(), $order, $limit);
    $result = mysql_query($query) or die(strtoupper($module).' SELECT: ' . mysql_error());
    if ($result and mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $row['arCategory'] = $arrModules["catalog"];
            $row = CatalogProduct::getItem($row, $UrlWL, UPLOAD_URL_DIR.'catalog/', $images_params, true);
            $items[] = $row;
        }
    }

    $seoFilters = $UrlWL->getFilters()->getSelectedTitles();
    if (!empty($seoFilters)) {
        // Заменяем пол на единичное число если не выбран тип
        if (!array_key_exists(18, $seoFilters) and array_key_exists(3, $seoFilters)) {
            if ($seoFilters[3]=="Мужские") $seoFilters[3]="Мужская";
            if ($seoFilters[3]=="Женские") $seoFilters[3]="Женская";
            if ($seoFilters[3]=="Детские") $seoFilters[3]="Детская";
        }
        $arCategory["seo_title"]  = CategoryMetaHelper::generate($arCategory["filter_seo_title"], $item, $seoFilters);
        $arCategory["seo_text"]   = CategoryMetaHelper::generate($arCategory["filter_seo_text"], $item, $seoFilters);
        $arCategory["meta_descr"] = CategoryMetaHelper::generate($arCategory["filter_meta_descr"], $item, $seoFilters);
        $arCategory["meta_key"]   = CategoryMetaHelper::generate($arCategory["filter_meta_key"], $item, $seoFilters);
    } else {
        $arCategory["seo_title"]  = CategoryMetaHelper::generate(($arCategory["seo_title"]), $item, $seoFilters);
        $arCategory["seo_text"]   = CategoryMetaHelper::generate($arCategory["seo_text"], $item, $seoFilters);
        $arCategory["meta_descr"] = CategoryMetaHelper::generate($arCategory["meta_descr"], $item, $seoFilters);
        $arCategory["meta_key"]   = CategoryMetaHelper::generate($arCategory["meta_key"], $item, $seoFilters);
    }
    $arrPageData["headTitle"]     = CategoryMetaHelper::generate($arCategory["filter_title"], $item, $seoFilters);
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
            "products"          => $smarty->fetch("ajax/products.tpl"),
            "selected_count"    => count($arrPageData["selectedFilters"]),
            "url"               => $UrlWL->getUrl(),
            "h_title"           => unScreenData(ucfirst($arrPageData["headTitle"])),
            "seo_title"         => unScreenData($arCategory["seo_title"]),
            "meta_descr"        => unScreenData($arCategory["meta_descr"]),
            "meta_key"          => unScreenData($arCategory["meta_key"]),
            "seo_text"          => unScreenData($arCategory["seo_text"]),
            'meta_robots'       => $arCategory["meta_robots"],
            "total_pages"       => $arrPageData['pager']->getCount()
        ); die(json_encode($json));
    }
// List Items
} else {
    // include page css
    $arrPageData['headCss'][] = '/css/smart/brands.css';
    // select brands
    $query  = 'SELECT b.* FROM `'.BRANDS_TABLE.'` b WHERE b.`active`=1 ORDER BY b.`order`';
    $result = mysql_query($query) or die(strtoupper($module).' SELECT: ' . mysql_error());
    if ($result and mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $row['descr']   = unScreenData($row['descr']);
            $row['image']   = (!empty($row['image']) and is_file($arrPageData['files_path'].$row['image'])) ? $arrPageData['files_url'].$row['image'] : $arrPageData['files_url'].'noimage.jpg';
            $row['gallery'] = array();
            $q = "SELECT * FROM `".BRANDS_GALLERY_TABLE."` WHERE `pid`={$row["id"]} AND `active`>0 ORDER BY `fileorder`";
            $r = mysql_query($q);
            if ($r and mysql_num_rows($r)) {
                $files_url  = UPLOAD_URL_DIR."brands/{$row["id"]}/";
                $files_path = prepareDirPath($files_url);
                while ($img = mysql_fetch_assoc($r)) {
                    $img["image"] = (!empty($img["filename"]) and file_exists($files_path.$img["filename"])) ? $files_url.$img["filename"] : $arrPageData['files_url'].'noimage.jpg';
                    $img["title"] = unScreenData($img["title"]);
                    $row['gallery'][] = $img;
                }
            } $items[] = $row;
        }
    }
}

$smarty->assign('item',  $item);
$smarty->assign('items', $items);