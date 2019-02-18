<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

require_once('include/classes/Filters.php');
require_once('include/classes/product/CatalogProduct.php');

# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$sort         = intval($UrlWL->getParam(UrlWL::SORT_KEY_NAME, 0));
$pages        = trim(addslashes($UrlWL->getParam(UrlWL::PAGES_KEY_NAME, '')));
$itemID       = $UrlWL->getItemId();
$item         = array(); // Item Info Array
$items        = array(); // Items Array of items Info arrays
$itemsIDX     = array();
$showSubItems = true;
$showEmptyAttr= true; // Show or hide empty product attributes
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
// ////////// END OPERATION MANIPULATION WITH SESSION VARIABLE \\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['pagesall']      = &$pages;
$arrPageData['backurl']       = $UrlWL->buildCategoryUrl($arCategory, ($pages ? UrlWL::PAGES_KEY_NAME.'='.UrlWL::PAGES_ALL_VAL : '').($sort ? UrlWL::SORT_KEY_NAME.'='.$sort : ''), '', $page);
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url']);
$arrPageData['items_on_page'] = 36;
$images_params = SystemComponent::prepareImagesParams(getValueFromDB(IMAGES_PARAMS_TABLE, 'aliases', 'WHERE `module`="'.$module.'"'));
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################

// Item Detailed View
if ($itemID) {
    // Get product item
    $FQ = new \Filters\FilterQuery();
    $FQ->where = "ti.`is_deleted`=0 AND ti.`product_id`=$itemID";
    $FQ->group = "ti.`product_id`";
    $query = CatalogProduct::getItemsSql($FQ->getWhere(), $FQ->getGroup(), "", "", "LIMIT 1");
    $result = mysql_query($query);
    if ($result and mysql_num_rows($result)>0) {
        // Set vars
        $item = mysql_fetch_assoc($result);
        $item['arCategory'] = $arCategory;
        $item['text'] = unScreenData($item['text']);
        $item = CatalogProduct::getItem($item, $UrlWL, $arrPageData['files_url'], $images_params, false, $arCategory["id"]);
        // replace item metadata
        $arrPageData['headTitle']  = ProductMetaHelper::generate("{attribute_9:_single} {attribute_16:_single} {brand} {pcode}", $item);
        $arCategory['seo_title']   = ProductMetaHelper::generate("{attribute_16:_single} {brand} {attribute_9:_single} Original купить в Киеве и Украине | Maikoff | {pcode}", $item);
        $arCategory['meta_descr']  = ProductMetaHelper::generate("{attribute_16:_single} {brand} {attribute_9:_single} Original {pcode} ✅ купить в Киеве и Украине. Цена в интернет магазине MaikOff ✅ Отзывы ✍ Быстрая доставка.", $item);
        $arCategory['meta_key']    = ProductMetaHelper::generate($item['meta_key'], $item);
        $arCategory['meta_robots'] = $item['meta_robots'];
//        $arrPageData["arrBreadCrumb"] = [
//            $UrlWL->buildCategoryUrl($arrModules["brands"]) => $arrModules["brands"]["title"],
//            $UrlWL->buildItemUrl($arrModules["brands"], $item["brand"]) => $item["brand_title"],
//            $UrlWL->buildItemUrl($arrModules["series"], $item["series"]) => $item["series_title"],
//            $UrlWL->buildItemUrl($arCategory, $item) => $arrPageData['headTitle'],
//        ];
        // add page styles & scripts
        $arrPageData['headCss'][]     = '/css/smart/product.css';
        $arrPageData['headScripts'][] = "/js/libs/elevate-zoom/jquery.elevateZoom-3.0.8.min.js";
        $arrPageData['headScripts'][] = "/js/libs/share/share".(!$IS_DEV ? ".min" : "").".js";
        $arrPageData['headScripts'][] = '/js/smart/product'.(!$IS_DEV ? ".min" : "").'.js';
        $modulename = "product";
        // Get print types
        if (!empty($item["print_types"]) and mb_strlen(trim($item["print_types"]))>0) {
            $pTypes = $item["print_types"];
            $squery = "SELECT DISTINCT pt.* FROM `".PRINT_TYPES_TABLE."` pt "
                    . "WHERE pt.`id` IN($pTypes) ORDER BY pt.`order`";
            $result = mysql_query($squery);
            if ($result and mysql_num_rows($result)>0) {
                $item["print_types"] = array();
                $files_url  = UPLOAD_URL_DIR."print_types/";
                $files_path = prepareDirPath($files_url);
                while ($row = mysql_fetch_assoc($result)) {
                    $row["icon"] = (!empty($row["icon"]) and file_exists($files_path.$row["icon"])) ? $files_url.$row["icon"] : "";
                    $item["print_types"][] = $row;
                }
            }
        }
        // Get item article
        $item["article"] = array();
        $squery = "SELECT * FROM `".NEWS_TABLE."` WHERE `id`={$item["article_id"]} LIMIT 1";
        $result = mysql_query($squery);
        if ($result and mysql_num_rows($result)>0) {
            $article = mysql_fetch_assoc($result);
            $files_url  = UPLOAD_URL_DIR."news/";
            $files_path = prepareDirPath($files_url);
            $article["arCategory"] = $arrModules["news"];
            $article["title"] = unScreenData($article["title"]);
            $article["descr"] = unScreenData($article["descr"]);
            $article["image"] = (!empty($article["image"]) and file_exists($files_path.$article["image"])) ? $files_url.$article["image"] : $files_url."noimage.jpg";
            $item["article"]  = $article;
        }
        // Size table
        $item["size_grid"] = getValueFromDB(SIZE_GRIDS_TABLE, "descr", "WHERE `id`={$item["size_grid_id"]}");
    } else {
        $UrlWL->redirectToErrorPage();
    }
// List Items
} else {
    $Filters = new CatalogFilters($UrlWL, $catid);
    $Filters->init();
    $arrPageData['headTitle'] = $arCategory['title'];
    $arrPageData['sort']      = ($sort = PHPHelper::getCorrectCatalogSort($sort));
    $arrPageData['arSorting'] = PHPHelper::getCatalogSorting($UrlWL, $sort);
    $arrPageData['filters']   = $Filters->getFilters(true);
    $arrPageData['selectedFilters'] = $UrlWL->getFilters()->getSelected();
    $arrPageData['headCss'][]     = "/css/smart/category.css";
    $arrPageData['headScripts'][] = "/js/libs/history.js/bundled/html4+html5/jquery.history.min.js";
    $arrPageData['headScripts'][] = "/js/smart/catalog".(!$IS_DEV ? ".min" : "").".js";
    // Total pages and Pager
    $arrPageData['total_items'] = $Filters->getCount();
    if (!$pages) {
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
    $order  = 'ORDER BY '.$arrPageData['arSorting'][$sort]['column'];
    $limit  = (!$pages ? 'LIMIT '.$arrPageData['offset'].', '.$arrPageData['items_on_page'] : '');
    $query  = CatalogProduct::getItemsSql($FilterQuery->getWhere(), $FilterQuery->getGroup(), $FilterQuery->getHaving(), $order, $limit);
    $result = mysql_query($query) or die(strtoupper($module).' SELECT: ' . mysql_error());
    if ($result and mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $row['arCategory'] = $arCategory;
            $row['isModel'] = !$Filters->getSeparateByColor();
            $items[] = CatalogProduct::getItem($row, $UrlWL, $arrPageData['files_url'], $images_params, true);
        }
    }
    if (!empty($arrPageData["selectedFilters"])) $arCategory["seo_text"] = "";
    $seoFilters = $UrlWL->getFilters()->getSelectedTitles();
    $selectedFilters = $arrPageData['selectedFilters'];
    if (!empty($seoFilters)) {
        // Вначале проверяем на наличие комбинаций фильтров
        ksort($selectedFilters, SORT_NUMERIC);
        $hash = md5(serialize($selectedFilters).$arCategory["id"]);
        $cnt  = getValueFromDB(SEO_FILTERS_TABLE, "COUNT(*)", "WHERE `hash`='{$hash}' AND CHAR_LENGTH(CONCAT(`title`,`seo_title`,`meta_descr`,`meta_key`,`meta_robots`,`seo_text`))>0");
        if ($cnt) {
            $meta = getItemRow(SEO_FILTERS_TABLE, "*", "WHERE `hash`='{$hash}'");
            $arrPageData["headTitle"]  = !empty($meta["title"])       ? unScreenData($meta["title"])       : $arrPageData["headTitle"];
            $arCategory["seo_title"]   = !empty($meta["seo_title"])   ? unScreenData($meta["seo_title"])   : $arCategory["seo_title"];
            $arCategory["meta_descr"]  = !empty($meta["meta_descr"])  ? unScreenData($meta["meta_descr"])  : $arCategory["meta_descr"];
            $arCategory["meta_key"]    = !empty($meta["meta_key"])    ? unScreenData($meta["meta_key"])    : $arCategory["meta_key"];
            $arCategory["meta_robots"] = !empty($meta["meta_robots"]) ? unScreenData($meta["meta_robots"]) : $arCategory["meta_robots"];
            $arCategory["seo_text"]    = !empty($meta["seo_text"])    ? unScreenData($meta["seo_text"])    : $arCategory["seo_text"];
            unset($meta);
        }
        // Если нет комбинаций фильтров - заполняем из шаблонов
        else {
            $arCategory["seo_title"]  = !empty($arCategory["filter_seo_title"]) ? CategoryMetaHelper::generate($arCategory["filter_seo_title"], $seoFilters) : "";
            $arCategory["seo_text"]   = !empty($arCategory["filter_seo_text"])  ? CategoryMetaHelper::generate($arCategory["filter_seo_text"], $seoFilters)  : "";
            $arCategory["meta_descr"] = !empty($arCategory["filter_meta_descr"])? CategoryMetaHelper::generate($arCategory["filter_meta_descr"], $seoFilters): "";
            $arCategory["meta_key"]   = !empty($arCategory["filter_meta_key"])  ? CategoryMetaHelper::generate($arCategory["filter_meta_key"], $seoFilters)  : "";
        }
        unset($selectedFilters);
        unset($seoFilters);
        unset($hash);
        unset($cnt);
    }
    if ($page > 1) {
        $arrPageData['headTitle'] .= ' - Страница '.$page;
        $arCategory['meta_descr'] .= ' | Страница '.$page;
        $arCategory['seo_title']  .= ' | Страница '.$page;
        $arCategory['title']      .= ' - Страница '.$page;
        $arCategory['seo_text']    = "";
    }
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
            'pager'             => $smarty->fetch("core/pager.tpl"),
            "breadcrumbs"       => $smarty->fetch("core/breadcrumb.tpl"),
            "selected_count"    => count($arrPageData["selectedFilters"]),
            "url"               => $UrlWL->getUrl(),
            "h_title"           => ucfirst($arrPageData["headTitle"]),
            "seo_title"         => unScreenData($arCategory["seo_title"]),
            "meta_descr"        => unScreenData($arCategory["meta_descr"]),
            "meta_key"          => unScreenData($arCategory["meta_key"]),
            "seo_text"          => unScreenData($arCategory["seo_text"]),
            'meta_robots'       => $arCategory["meta_robots"],
            "total_pages"       => $arrPageData['pager']->getCount()
        );
        if (isset($_POST["ajaxLoadMore"])) {
            $json["products"] = "";
            foreach ($items as $item) {
                $smarty->assign('item', $item);
                $json["products"] .= $smarty->fetch("core/product.tpl");
            }
        }
        if (isset($_POST["ajaxUpdate"])) {
            $json["products"] = $smarty->fetch("ajax/products.tpl");
        } die(json_encode($json));
    }
}

$smarty->assign('item',          $item);
$smarty->assign('items',         $items);