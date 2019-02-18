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

// Item Detailed View
if($itemID) {
    // Get product item
    $items = PrintProduct::getItems(array($itemID), $UrlWL, false, true, 1);
    $item = array_shift($items);
    if (!empty($item)) {
        // replace item metadata
        $arCategory['meta_descr']  = ProductMetaHelper::generate(($item['meta_descr'] ? $item['meta_descr'] : $arCategory['product_meta_descr']), $item);
        $arCategory['meta_key']    = ProductMetaHelper::generate(($item['meta_key'] ? $item['meta_key'] : $arCategory['product_meta_key']), $item);
        $arCategory['seo_title']   = ProductMetaHelper::generate(($item['seo_title'] ? $item['seo_title'] : $arCategory['product_seo_title']), $item);
        $arCategory['seo_text']    = ProductMetaHelper::generate($item['seo_text'], $item);
        $arCategory['meta_robots'] = $item['meta_robots'];
        $arrPageData['headTitle']  = ProductMetaHelper::generate("{substrate} {title}", $item);
        // Build breadcrumbs
        $category = $arrModules["prints"];
        $_UrlWL = $UrlWL->copy();
        $_UrlWL->initCategory($category);
        $_UrlWL->getFilters()->setCategoryFilters(UrlWL::getCategoryFilters($category['id'], UrlFilters::LIST_TYPE_SEO, $category["module"]));
        $categoryFilters = $_UrlWL->getFilters()->getCategoryFilters();
        $selectedFilters = [];
        $url = $UrlWL->buildCategoryUrl($arrModules["prints"]);
        $arrPageData["arrBreadCrumb"] = [
            $url => $category["title"]
        ];
        foreach ($categoryFilters as $filterID=>$filter) {
            switch ($filter["alias"]) {
                /**
                 * Тип одежды
                 * ID фильтра: 18
                 * ID атрибута: 16
                 * --
                 * Для кого
                 * ID фильтра: 3
                 * ID атрибута: 9
                 */
                case "type":
                case "sex":
                    foreach ($item["attributes"] as $attribute) {
                        if ($attribute["id"]==$filter["aid"]) {
                            $value = reset($attribute["values"]);
                            $selectedFilters[$filterID] = [$value["id"]];
                            $url = $_UrlWL->buildCategoryUrl($category, null, '', 1, false, $_UrlWL->copyFilters()->setSelected($selectedFilters));
                            $arrPageData["arrBreadCrumb"][$url] = $value["title"];
                            break;
                        }
                    }
                    break;
                /**
                 * Категория
                 * ID фильтра: 14
                 */
                case "category":
                    if ($item["category_id"]!=$category["id"]) {
                        $selectedFilters[$filterID] = [$item["category_id"]];
                        $url = $_UrlWL->buildCategoryUrl($category, null, '', 1, false, $_UrlWL->copyFilters()->setSelected($selectedFilters));
                        $arrPageData["arrBreadCrumb"][$url] = $arCategory["title"];
                    }
                    break;
            }
        }
        /**
         * Название товара
         */
        $url = $UrlWL->getUrl();
        $arrPageData["arrBreadCrumb"][$url] = $arrPageData['headTitle'];

        // add page styles & scripts
        $arrPageData['headCss'][]     = "/css/smart/product-print.css";
        $arrPageData['headScripts'][] = "/js/libs/elevate-zoom/jquery.elevateZoom-3.0.8.min.js";
        $arrPageData['headScripts'][] = "/js/libs/share/share".(!$IS_DEV ? ".min" : "").".js";
        $arrPageData['headScripts'][] = "/js/smart/product".(!$IS_DEV ? ".min" : "").".js";
        $arrPageData['headScripts'][] = "/js/smart/product-print".(!$IS_DEV ? ".min" : "").".js";
        $modulename = "product_print";
        // Add item to last watched
        PHPHelper::addToLastWatched($itemID);
    } else {
        $UrlWL->redirectToErrorPage();
    }
// List Items
} else {
    //IF you want to show all subcategories  products  - uncomment below line
    $arCatIdSet = array();//array_merge(array($catid), ($showSubItems ? getChildrensIDs($catid, true) : array()));
   
    require_once('include/classes/Filters.php');
    $Filters = new PrintFilters($UrlWL, $catid, $arCatIdSet);
    $Filters->init();
    $arrPageData['sort']            = ($sort = PHPHelper::getCorrectCatalogSort($sort));
    $arrPageData['arSorting']       = PHPHelper::getCatalogSorting($UrlWL, $sort);
    $arrPageData['filters']         = $Filters->getFilters();
    $arrPageData['selectedFilters'] = $UrlWL->getFilters()->getSelected();
    $arrPageData['headTitle']       = $arCategory['title'];
    $arrPageData['headCss'][]       = '/css/smart/catalog.css';
    $arrPageData['headScripts'][]   = "/js/libs/history.js/bundled/html4+html5/jquery.history.min.js";
    $arrPageData['headScripts'][]   = "/js/smart/catalog".(!$IS_DEV ? ".min" : "").".js";
    // Total pages and Pager
    $arrPageData['total_items'] = $Filters->getCount();
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
            if ($row['category_id']==$arCategory["id"]) $row['arCategory'] = $arCategory;
            $items[] = PrintProduct::getItem($row, $UrlWL, $Filters->getSeparateByType(), $Filters->getSeparateByColor(), true, $sort);
        }
    }    
    if (!empty($arrPageData["selectedFilters"])) $arCategory["seo_text"] = "";
    $seoFilters = $UrlWL->getFilters()->getSelectedTitles();
    $selectedFilters = $arrPageData['selectedFilters'];
    // если пустой результат запроса - переадресовываем на 404
    if (!empty($seoFilters) and empty($items)) {
        $UrlWL->redirectToErrorPage();
    }
//    var_export($selectedFilters);
    if (!empty($seoFilters)) {
        // Вначале проверяем на наличие комбинаций фильтров
        ksort($selectedFilters, SORT_NUMERIC);
        ksort($seoFilters, SORT_NUMERIC);
        $hash = md5(serialize($selectedFilters).$catid);
        $cnt  = getValueFromDB(SEO_FILTERS_TABLE, "COUNT(*)", "WHERE `hash`='{$hash}' AND CHAR_LENGTH(CONCAT(`title`,`seo_title`,`meta_descr`,`meta_key`,`meta_robots`,`seo_text`))>0");
        $found = false;
        $strict = true;
        if ($cnt) {
            $found = true;
            $meta  = getItemRow(SEO_FILTERS_TABLE, "*", "WHERE `hash`='{$hash}'");
            $arrPageData["headTitle"]  = !empty($meta["title"])       ? unScreenData($meta["title"])       : $arrPageData["headTitle"];
            $arCategory["seo_title"]   = !empty($meta["seo_title"])   ? unScreenData($meta["seo_title"])   : $arCategory["seo_title"];
            $arCategory["meta_descr"]  = !empty($meta["meta_descr"])  ? unScreenData($meta["meta_descr"])  : $arCategory["meta_descr"];
            $arCategory["meta_key"]    = !empty($meta["meta_key"])    ? unScreenData($meta["meta_key"])    : $arCategory["meta_key"];
            $arCategory["meta_robots"] = !empty($meta["meta_robots"]) ? unScreenData($meta["meta_robots"]) : $arCategory["meta_robots"];
            $arCategory["seo_text"]    = !empty($meta["seo_text"])    ? unScreenData($meta["seo_text"])    : $arCategory["seo_text"];
            unset($meta);
        }
        // Если нет строгого совпадения комбинации фильтров - заполняем из шаблонов
        if (!$found) {
            $strict = false;
            $length = count($selectedFilters);
//            foreach ($selectedFilters as $k=>$v) {
//                $_selectedFilters = $selectedFilters;
//                unset($_selectedFilters[$k]);
//                $hash  = md5(serialize($_selectedFilters).$catid);
//                $found = (bool)getValueFromDB(SEO_FILTERS_TABLE, "COUNT(*)", "WHERE `hash`='{$hash}' AND CHAR_LENGTH(CONCAT(`title`,`seo_title`,`meta_descr`,`meta_key`,`meta_robots`,`seo_text`))>0");
//                while (!$found and !empty($_selectedFilters)) {
//                    // удаляем по фильтру на каждой итерации
//                    array_pop($_selectedFilters);
//                    $hash  = md5(serialize($_selectedFilters).$catid);
//                    $found = (bool)getValueFromDB(SEO_FILTERS_TABLE, "COUNT(*)", "WHERE `hash`='{$hash}' AND CHAR_LENGTH(CONCAT(`title`,`seo_title`,`meta_descr`,`meta_key`,`meta_robots`,`seo_text`))>0");
//                } if ($found) break;
//            }
            
            for ($i = $length; $i > 0; $i--) {
                $_selectedFilters = $selectedFilters;
                while (count($_selectedFilters) > $i) array_pop($_selectedFilters);
                for ($j = 0; $j < count($_selectedFilters); $j++) {
                    $_selectedFilters = CategoryMetaHelper::shuffleFilters($_selectedFilters);
                    $hash  = md5(serialize($_selectedFilters).$catid);
                    $found = (bool)getValueFromDB(SEO_FILTERS_TABLE, "COUNT(*)", "WHERE `hash`='{$hash}' AND CHAR_LENGTH(CONCAT(`title`,`seo_title`,`meta_descr`,`meta_key`,`meta_robots`,`seo_text`))>0");
                    if ($found) break;
                } if ($found) break;
                array_pop($selectedFilters);
            }
            
//            while (!$found and !empty($_selectedFilters)) {
//                // удаляем по фильтру на каждой итерации
//                array_pop($_selectedFilters);
//                $hash  = md5(serialize($_selectedFilters).$catid);
//                $found = (bool)getValueFromDB(SEO_FILTERS_TABLE, "COUNT(*)", "WHERE `hash`='{$hash}' AND CHAR_LENGTH(CONCAT(`title`,`seo_title`,`meta_descr`,`meta_key`,`meta_robots`,`seo_text`))>0");
//            }
//            $_selectedFilters = $selectedFilters;
//            while (!$found and !empty($_selectedFilters)) {
//                // удаляем по фильтру на каждой итерации
//                array_pop($_selectedFilters);
//                $hash  = md5(serialize($_selectedFilters).$catid);
//                $found = (bool)getValueFromDB(SEO_FILTERS_TABLE, "COUNT(*)", "WHERE `hash`='{$hash}' AND CHAR_LENGTH(CONCAT(`title`,`seo_title`,`meta_descr`,`meta_key`,`meta_robots`,`seo_text`))>0");
//            }
            // если нашли - заполняем шаблон переменными из выбранных фильтров
            if ($found) {
                $_UrlWL = $UrlWL->copy();
                $_UrlWL->getFilters()->setCategoryFilters(UrlWL::getCategoryFilters($catid, UrlFilters::LIST_TYPE_DEFAULT, $module));
                $selectedTitles = $_UrlWL->getFilters()->getSelectedTitles();
                unset($_UrlWL);
                $meta  = getItemRow(SEO_FILTERS_TABLE, "*", "WHERE `hash`='{$hash}'");
                $arrPageData["headTitle"] = !empty($meta["title_var"])     ? CategoryMetaHelper::generate($meta["title_var"], $arrPageData['filters'])     : $meta["title"];
                $arCategory["seo_title"]  = !empty($meta["seo_title_var"]) ? CategoryMetaHelper::generate($meta["seo_title_var"], $arrPageData['filters']) : $meta["title"];
                $arCategory["seo_text"]   = !empty($meta["seo_text_var"])  ? CategoryMetaHelper::generate($meta["seo_text_var"], $arrPageData['filters'])  : $meta["title"];
                $arCategory["meta_descr"] = !empty($meta["meta_descr_var"])? CategoryMetaHelper::generate($meta["meta_descr_var"], $arrPageData['filters']): $meta["title"];
                $arCategory["meta_key"]   = !empty($meta["meta_key_var"])  ? CategoryMetaHelper::generate($meta["meta_key_var"], $arrPageData['filters'])  : $meta["title"];
                unset($meta);
            }
        }

        // Если нет комбинаций фильтров - заполняем из шаблонов
        if (!$found) {
            $arCategory["seo_title"]  = !empty($arCategory["filter_seo_title"]) ? CategoryMetaHelper::generate($arCategory["filter_seo_title"], $arrPageData['filters']) : "";
            $arCategory["seo_text"]   = !empty($arCategory["filter_seo_text"])  ? CategoryMetaHelper::generate($arCategory["filter_seo_text"], $arrPageData['filters'])  : "";
            $arCategory["meta_descr"] = !empty($arCategory["filter_meta_descr"])? CategoryMetaHelper::generate($arCategory["filter_meta_descr"], $arrPageData['filters']): "";
            $arCategory["meta_key"]   = !empty($arCategory["filter_meta_key"])  ? CategoryMetaHelper::generate($arCategory["filter_meta_key"], $arrPageData['filters'])  : "";
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
        $smarty->assign('subMenu',          getMenu(1, GetRootId($catid)));
        $smarty->assign('catalogMenu',      getMenu(6, $arrModules['prints']['id']));
        $json = array(
            "filters"           => $smarty->fetch("ajax/filter.tpl"),
            "filters_mobile"    => $smarty->fetch("ajax/filter-mobile.tpl"),
            "selected_filters"  => $smarty->fetch("ajax/selected_filters.tpl"),
            "subcategories"     => $smarty->fetch("ajax/filter_subcategories.tpl"),
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
            "total_pages"       => $arrPageData['pager']->getCount()
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

$smarty->assign('item',          $item);
$smarty->assign('items',         $items);