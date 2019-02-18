<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access
// Set default canonical url
$arrPageData["canonical"] = WLCMS_HTTP_HOST;
$_UrlWL = $UrlWL->copy();
$_UrlWL->unsetParam(UrlWL::SORT_KEY_NAME);
$_UrlWL->unsetParam(UrlWL::VIEW_KEY_NAME);
// Set canonical url for news flypage
if (($arCategory["module"]=="prints" or $arCategory["module"]=="catalog" or $arCategory["module"]=="news" or $arCategory["module"]=="print_types" or $arCategory["module"]=="brands") and !empty($item)) {
    $arrPageData["canonical"] .= $_UrlWL->buildItemUrl($arCategory, $item);
}
// Set meta robots "noindex, nofollow" when more of one filters selected
elseif (($arCategory["module"]=="catalog" or $arCategory["module"]=="prints") and !empty($items)) {
    $filtersCNT = 0;
    if (!empty($arrPageData["filters"])) {
        foreach ($arrPageData["filters"]["items"] as $filterID => $filter) {
            // For price filter type
            if ($filter['tid']==UrlFilters::TYPE_PRICE AND isset($filter["children"]["selected"]) AND $filter["children"]["selected"]["min"] AND $filter["children"]["selected"]["max"]) {
                $filtersCNT++;
            }
            // For other filters
            else {
                $filtersCNT += $filter["selectedCount"];
            }
        }
    }
    $arrPageData["canonical"] .= $_UrlWL->buildUrl();
    if ($filtersCNT > 0) {
        if (!empty($_SERVER["QUERY_STRING"])) {
            $arrPageData["canonical"] = "";
            $arCategory["meta_robots"] = "noindex,nofollow";
//            $arrPageData["canonical"] = str_replace($_SERVER["QUERY_STRING"], "", $arrPageData["canonical"]);
//            $arrPageData["canonical"] = preg_replace("/\?+$/u", "", $arrPageData["canonical"]);
        }
    }
} else {
    $arrPageData["canonical"] .= $_UrlWL->buildCategoryUrl($arCategory, null, "", $page, false, $_UrlWL->copy()->getFilters());
}
// Set canonical url if page>1
if (($arCategory["module"]=="catalog" or $arCategory["module"]=="prints" or $arCategory["module"]=="news") and !empty($items)) {
    if ($page > 1) {
        $arrPageData["link_prev"] = WLCMS_HTTP_HOST.$arrPageData["pager"]->getUrl($arrPageData["pager"]->getPrev());
    }
    if ($page < $arrPageData['pager']->getCount()) {
        $arrPageData["link_next"] = WLCMS_HTTP_HOST.$arrPageData["pager"]->getUrl($arrPageData["pager"]->getNext());
    }
}

unset($_UrlWL);