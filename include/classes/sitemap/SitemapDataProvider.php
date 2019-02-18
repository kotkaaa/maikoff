<?php

/*
 * WebLife CMS
 * Created on 01.11.2018, 11:18:17
 * Developed by http://weblife.ua/
 */

namespace Sitemap;

use \CatalogFilters;
use \PrintFilters;
use \UrlFilters;

interface DataProvider {

    public function get();
}

/**
 * Description of SitemapDataProvider
 *
 * @author user5
 */

class SitemapDataProvider {

    const BRANDS_CATID  = 39;
    const CATALOG_CATID = 18;

    protected $items = [];
    protected $DB;
    protected $UrlWL;

    public function __construct (\DbConnector $DB, \UrlWL $UrlWL) {
        $this->DB    = $DB;
        $this->UrlWL = $UrlWL;
    }

    public function free () {
        $this->items = [];
        $this->DB    = null;
        $this->UrlWL = null;
    }
}

class CategoriesDataProvider extends SitemapDataProvider implements DataProvider {

    protected $unSupportedModules = [
        "thanks", "checkout", "basket", "error",
        "callback", "request", "subscribe",
    ];
    /**
     *
     * @return array
     */
    public function get() {
        $this->main();
        $this->news();
        return $this->items;
    }

    private function main () {
        $query = "SELECT `id`, `redirectid`, `redirecturl`, `title`, `seo_path`, `pagetype`, `menutype`, `module`, " . PHP_EOL
                . "NULL AS `loc`, '9' AS `priority` FROM `".MAIN_TABLE."` " . PHP_EOL
                . "WHERE `active`>0 AND `id`>8 " . PHP_EOL
                . "AND (`redirectid` + LENGTH(`redirecturl`)) = 0 " . PHP_EOL
                . "AND LENGTH(`seo_path`) > 0 " . PHP_EOL
                . "AND `module` NOT IN('" . implode("','", $this->unSupportedModules) . "')" . PHP_EOL
                . "ORDER BY `id`";
        $this->DB->Query($query) or die (mysql_error());
        if ($this->DB->getNumRows()) {
            while ($row = $this->DB->fetchObject()) {
                $row->loc      = $this->UrlWL->buildCategoryUrl((array)$row);
                $this->items[] = $row;
            }
        } $this->DB->Free();
    }

    private function news () {
        $query = "SELECT `id`, `cid`, `title`, `seo_path`, `active`, " . PHP_EOL
                . "NULL AS `loc`, '7' AS `priority` FROM `".NEWS_TABLE."` " . PHP_EOL
                . "WHERE `active`>0 " . PHP_EOL
                . "ORDER BY `id`";
        $this->DB->Query($query) or die (mysql_error());
        if ($this->DB->getNumRows()) {
            $arCategory = [];
            while ($row = $this->DB->fetchObject()) {
                if (empty($arCategory)) $arCategory = $this->UrlWL->getCategoryById ($row->cid);
                $row->loc      = $this->UrlWL->buildItemUrl($arCategory, (array)$row);
                $this->items[] = $row;
            } unset($arCategory);
        } $this->DB->Free();
    }
}

class CatalogDataProvider extends SitemapDataProvider implements DataProvider {

    public function get() {
        $this->brands();
        $this->catalog();
        return $this->items;
    }
    
    private function brands () {
        $query = "SELECT `id`, `title`, `seo_path`, `active`, " . PHP_EOL
                . "NULL AS `loc`, '9' AS `priority` FROM `".BRANDS_TABLE."` " . PHP_EOL
                . "WHERE `active`>0 " . PHP_EOL
                . "ORDER BY `id`";
        $this->DB->Query($query) or die (mysql_error());
        if ($this->DB->getNumRows()) {
            $arCategory = [];
            while ($row = $this->DB->fetchObject()) {
                if (empty($arCategory)) $arCategory = $this->UrlWL->getCategoryById (parent::BRANDS_CATID);
                $row->loc      = $this->UrlWL->buildItemUrl($arCategory, (array)$row);
                $this->items[] = $row;
            } unset($arCategory);
        } $this->DB->Free();
    }
    
    private function catalog () {
        $query = "SELECT `id`, `title`, `seo_path`, `active`, " . PHP_EOL
                . "NULL AS `loc`, '9' AS `priority` FROM `".CATALOG_TABLE."` " . PHP_EOL
                . "WHERE `active`>0 " . PHP_EOL
                . "ORDER BY `id`";
        $this->DB->Query($query) or die (mysql_error());
        if ($this->DB->getNumRows()) {
            $arCategory = [];
            while ($row = $this->DB->fetchObject()) {
                if (empty($arCategory)) $arCategory = $this->UrlWL->getCategoryById (parent::BRANDS_CATID);
                $row->loc      = $this->UrlWL->buildItemUrl($arCategory, (array)$row);
                $this->items[] = $row;
            } unset($arCategory);
        } $this->DB->Free();
    }
}

class PrintsDataProvider extends SitemapDataProvider implements DataProvider {

    public function get() {
        $this->prints();
        return $this->items;
    }

    private function prints () {
        $query = "SELECT p.`id`, p.`category_id`, p.`title`, p.`active`, " . PHP_EOL
                . "(CONCAT(pt.`seo_path`, '" . \UrlWL::URL_SEPARATOR . "', p.`seo_path`)) AS `seo_path`, " . PHP_EOL
                . "NULL AS `loc`, '9' AS `priority` FROM `".PRINTS_TABLE."` p " . PHP_EOL
                . "LEFT JOIN `".SUBSTRATES_TABLE."` pt ON(pt.`id` = p.`substrate_id`) " . PHP_EOL
                . "WHERE p.`active`>0 " . PHP_EOL
                . "GROUP BY p.`id`";
        $this->DB->Query($query) or die (mysql_error());
        if ($this->DB->getNumRows()) {
            while ($row = $this->DB->fetchObject()) {
                $arCategory    = $this->UrlWL->getCategoryById($row->category_id);
                $row->loc      = $this->UrlWL->buildItemUrl($arCategory, (array)$row);
                $this->items[] = $row;
                unset($arCategory);
            } 
        } $this->DB->Free();
    }
}

class FiltersDataProvider extends SitemapDataProvider implements DataProvider {

    protected $module = "catalog";
    protected $Filters;

    public function get() {
        $query = "SELECT `id`, `pid`, `redirectid`, `redirecturl`, `title`, `seo_path`, `pagetype`, `menutype`, `module` FROM `". MAIN_TABLE."` WHERE `module`='{$this->module}' AND `active`>0 AND `pid`=0";
        $result = $this->DB->Query($query) or die(mysql_error());
        while ($category = mysql_fetch_assoc($result)) {
            $this->UrlWL->initCategory($category);
            $this->UrlWL->setPath($category['arPath']);
            // бренд + тип одежды + пол
            $this->initFilters($category["id"]);
            $this->getCategoryFilters($category);
        } $this->DB->Free();
        return $this->items;
    }
    
    public function initFilters ($catid) {
        if ($this->module=="catalog")
            $this->Filters = new \CatalogFilters($this->UrlWL, $catid);
        elseif ($this->module=="prints")
            $this->Filters = new \PrintFilters($this->UrlWL, $catid, array()/*array_merge([$catid], getChildrensIDs($catid))*/);
        $this->Filters->init();
    }

    public function getCategoryFilters($category) {
        $arFilters = $this->Filters->getFilters(true, array(\UrlFilters::TYPE_TEXT, \UrlFilters::TYPE_CATEGORY));
        if (!empty($arFilters['items'])) {
            // Выбираем все значения фильтров
            $arAllFilters = array();
            foreach ($arFilters['items'] as $fid => $filter) {
                $children = array();
                foreach ($filter['children'] as $child) {
                    if ($child['cnt'] > 0) {
                        $children[$child['alias']] = $child['alias'];
                    }
                    if ($filter["tid"]==\UrlFilters::TYPE_CATEGORY and !empty($child["subcategories"])) {
                        foreach ($child["subcategories"] as $subchild) {
                            if ($subchild['cnt'] > 0) {
                                $children[$subchild['alias']] = $subchild['alias'];
                            }
                        }
                    }
                }
                if ($children) {
                    $arAllFilters[$fid] = $children;
                }
            }
            // Выбираем все возможные комбинации фильтров
            $arSelected = $arCombines = array();
            foreach (array_keys($arAllFilters) as $fid) {
                self::recursion($arAllFilters, $fid, $arCombines);
            }
            if (!empty($arCombines)) {
                $arCombines = array_unique($arCombines);
                foreach($arCombines as $comb) {
                    $arInput = array();
                    $arFidx = explode(',', $comb);
                    if(count($arFidx) > 1) {
                        foreach($arFidx as $id) {
                            if (isset($arAllFilters[$id])) {
                                $arInput[$id] = $arAllFilters[$id];
                                unset($arAllFilters[$id]);
                            }
                        }
                        if (($combines = self::getAllVariations($arInput))) {
                            foreach($combines as $arr) {
                                $select = array();
                                foreach($arr as $i => $a) {
                                    $select[$i] = array($a);
                                } $arSelected[] = $select;
                            } unset($combines);
                        }
                    }
                }
            }
            
            $urlFilters = $this->UrlWL->getFilters();
            $urlFilters->setCategoryFilters(\UrlWL::getCategoryFilters($category['id'], UrlFilters::LIST_TYPE_SEO, $this->module, array(\UrlFilters::TYPE_TEXT, \UrlFilters::TYPE_CATEGORY)));
//            var_export($arSelected);
//            exit;
            foreach($arSelected as $selected) {
                $urlFilters->setSelected($selected);
                // check items count
                if ($this->Filters->getTotalFilteredItems($selected)) {
                    $url = $this->Filters->getUrl($urlFilters);
                    if (!$this->url_exists($url) and strpos($url, "?")==null) {
                        $item = [
                            "loc" => $url,
                            "priority" => 9
                        ];
                        $this->items[] = (object)$item;
                        unset($item);
                    }
                }
            }

            foreach ($arFilters['items'] as $tid => $filter) {
                foreach ($filter['children'] as $f) {
                    $url = $f['url'];
                    if (!$this->url_exists($url) and strpos($url, "?")==null){
                        $item = [
                            "loc" => $url,
                            "priority" => 9
                        ];
                        $this->items[] = (object)$item;
                        unset($item);
                    }
                }
            }
        }
    }

    public function url_exists($url) {
        $exists = array_filter($this->items, function($ar) {
            global $url;
            return ($ar->loc == $url);
        }); unset($url);
        return $exists;
    }
    
    public static function getAllVariations($input) {
        $result = array();
        $cnt = array_product(array_map('count', $input));
        $step = 1;
        foreach ($input as $key=>$array) {
            for ($i=0; $i<$cnt; $i++) {
                foreach ($array as $value) {
                    for ($k=0; $k<$step; $k++) {
                        $result[$i+$k][$key] = $value;
                    } $i += $step;
                } $i--;
            } $step = $step * count($array);
        } return $result;
    }

    public static function recursion ($arAllFilters, $fid, &$arPairs = array(), &$arCurrentIDXs = array()) {
        $arCurrentIDXs[] = $fid;  
        $arPairs[] = (string)$fid;
        $arFilters = $arAllFilters;
        unset($arFilters[$fid]);  
        foreach ($arFilters as $subFid => $subFilters) {    
            $subCurrentIDXs = $arCurrentIDXs;
            $subCurrentIDXs[] = $subFid;
            sort($subCurrentIDXs);  
            $arPairs[] = implode(',', $subCurrentIDXs);
            self::recursion($arFilters, $subFid, $arPairs, $arCurrentIDXs);
        } $arCurrentIDXs = array();
    }

    public function free() {
        parent::free();
        $this->Filters = null;
    }
}

class PrintsFiltersDataProvider extends FiltersDataProvider implements DataProvider {

    public function __construct (\DbConnector $DB, \UrlWL $UrlWL) {
        parent::__construct($DB, $UrlWL);
        $this->module = "prints";
    }
}