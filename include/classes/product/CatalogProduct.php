<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

/*
 * WebLife CMS
 * Created on 06.06.2018, 14:16:00
 * Developed by http://weblife.ua/
 */

require_once 'Product.php';

/**
 * Description of CatalogProduct
 * Use $product_id for catalog row
 *
 * @author Andreas
 */
class CatalogProduct extends Product {

    const MODULE = "catalog";

    /**
     * @return string
     */
    public static function getModule(){
        return self::MODULE;
    }

    /**
     * [ -1 | 0 | 1 | 2 ] SET The optional number of decimal digits to round to OR -1 to not round
     * @return array
     */
    public static function getPrecisions() {
        return array(
            -1 => 'Нет',
             0 => 0,
             1 => 1,
             2 => 2,
        );
    }
     
    /**
     * @inheridoc
     */
    public static function getItemsSql($where = '', $group = '', $having = '', $order = '', $limit = '', $sortID = 0){
        return 'SELECT m.*, t.*, p.* ' . PHP_EOL
             . 'FROM ('
                . 'SELECT ti.`model_id`, ti.`product_id`, ti.`product_sequence` `sequence`, ti.`brand_title`, ti.`series_title`, ti.`color_title`'. PHP_EOL
                . 'FROM `' . CATALOG_INDEX_TABLE . '` ti '. PHP_EOL
                . $where
                . $group
                . $having
             . ') p'. PHP_EOL
             . 'INNER JOIN `'.MODELS_TABLE.'` m ON (m.`id` = p.`model_id`)' . PHP_EOL
             . 'INNER JOIN `'.CATALOG_TABLE.'` t ON (t.`id` = p.`product_id`)' . PHP_EOL
             . $order . PHP_EOL
             . $limit . PHP_EOL;
    }

    public static function getItem($item, UrlWL $UrlWL, $images_path, $arAliases, $inList=true, $shortcutCid=false, $module='catalog', $isKitItem = false) {
        if (!empty($item)) {
            isset($item['arCategory']) or $item['arCategory'] = $UrlWL->getCategoryById($shortcutCid);
            // Set vars
            $item['descr']      = $inList ? '' : unScreenData(@$item['descr']);
            $item['fulldescr']  = $inList ? '' : unScreenData(@$item['fulldescr']);
            // set Item price
            $item["price"]      = self::getItemPrice($item);
            // set price with discount
            $item['new_price']  = 0;
            // get brand
            $item['brand']      = $inList ? array() : getSimpleItemRow($item['brand_id'], BRANDS_TABLE);
            // get series
            $item['series']     = $inList ? array() : getSimpleItemRow($item['series_id'], SERIES_TABLE);
            // get size grid
            $item['size_grid']  = ($inList || empty($item['size_grid_id'])) ? array() : getValueFromDB(SIZE_GRIDS_TABLE, "descr", "WHERE `id`=".$item["size_grid_id"]);
            // set item attributes
            $item['attributes'] = self::getItemAttributes($item['arCategory']["id"], $item["model_id"], 'mid', MODEL_ATTRIBUTES_TABLE, $inList, true, false);
            // get colors
            $item["colors"]     = self::getItemColors($item, $images_path, $inList);
            // get sizes
            $item["sizes"]      = self::getItemSizes($item["id"], $inList);
            // set item idKey Keys
            self::setItemIdKey($item);
            // set basket data
            self::setItemBasketQty($item);
            // set images
            self::getItemImages($images_path, $item, $inList, 'image', $arAliases);
        } return $item;
    }

    public static function getItemPrice(&$item) {
        global $objSettingsInfo;
        // Recalc Item price by currency rate
        $price = $item["price"] * $objSettingsInfo->eurRate;
        if ($objSettingsInfo->pricePrecision >= 0)
            $price = round($price, $objSettingsInfo->pricePrecision);
        return $price;
    }
    /**
     * @example PHPHelper::getItemColors()
     * @param array $item
     * @param String $images_path
     * @param boolean $inList
     * @return array
     */
    public static function getItemColors(&$item, $images_path, $inList=true) {
        $colors = array();
        $query  = "SELECT t.`id`, ct.`title`, t.`title` AS `product_title`, t.`seo_path`, ct.`id` AS `color_id`, "
                . "ct.`hex`, cf.`filename` AS `src`, t.`id` AS `product_id`, t.`price`, t.`pcode`, t.`print_types`,  "
                . "IF(t.`id` = '".(empty($item['isModel']) ? $item["id"] : 0)."', 1, 0) AS `checked` "
                . "FROM `".CATALOG_TABLE."` t "
                . "INNER JOIN `".COLORS_TABLE."` ct ON(ct.`id` = t.`color_id`) "
                . "LEFT JOIN `".CATALOGFILES_TABLE."` cf ON(cf.`pid` = t.`id` AND cf.`isdefault`=1) "
                . "WHERE t.`active`>0 AND t.`price`>0 AND t.`model_id`={$item["model_id"]} "
                . "ORDER BY `checked` DESC, t.`order`";
        $result = mysql_query($query);
        if ($result and mysql_num_rows($result)) {
            while ($color = mysql_fetch_assoc($result)) {
                $item_img_path     = $images_path.$color["id"].'/';
                $prepared_img_path = prepareDirPath($item_img_path);
                $img_prefix = $inList ? "middle_" : "";
                $color["image"] = (!empty($color["src"]) and file_exists($prepared_img_path.$img_prefix.$color["src"])) ? $item_img_path.$img_prefix.$color["src"] : $images_path.$img_prefix."noimage.jpg";
                $color["price"] = self::getItemPrice($color);
                $colors[] = array_merge($item, $color);
            }
            // set default 
            if(!empty($item['isModel'])) {
                $color = reset($colors);
                if($item["id"] != $color["id"]) {
                    $color['title'] = $color['product_title'];
                    unset($color['product_title'], $color['src'], $color['hex']);
                    $item = array_merge($item, $color);
                }
            }
        } return $colors;
    }
    /**
     * @example PHPHelper::getItemSizes()
     * @param int $itemID
     * @return array
     */
    public static function getItemSizes($itemID, $inList = true) {
        if ($inList) return array(); // чтобы не нагружать выдачу - размеры нужны только на карточке товара
        $items  = array();
        $query  = "SELECT DISTINCT s.* FROM `".PRODUCT_SIZES_TABLE."` ps "
                . "LEFT JOIN `".SIZES_TABLE."` s ON(s.`title` = ps.`size`) "
                . "WHERE ps.`pid`=$itemID "
                . "ORDER BY s.`order`";
        $result = mysql_query($query);
        if ($result and mysql_num_rows($result)) {
            while ($item = mysql_fetch_assoc($result)) {
                $items[] = $item;
            }
        } return $items;
    }
    /**
     * @example PHPHelper::getItemImages()
     * @param String $img_path
     * @param array $item
     * @param boolean $list
     * @param string $colname
     * @param array $arAliases
     */
    public static function getItemImages ($img_path, &$item, $list = false, $colname='image', $arAliases=array('')) {
        $item_img_path  = $img_path.$item["id"].'/';
        $prepared_img_path = prepareDirPath($item_img_path);
        $item["images"] = array();
        $query  = "SELECT gf.* FROM `".CATALOGFILES_TABLE."` gf "
                . "WHERE gf.`pid`={$item['id']} AND gf.`active`>0 "
                . "ORDER BY gf.`isdefault` DESC, gf.`fileorder`".($list ? " LIMIT 1" : "");
        $result = mysql_query($query);
        if ($result and mysql_num_rows($result) > 0) {
            $i = 0;
            while ($image = mysql_fetch_assoc($result)) {
                $image[$colname] = (!empty($image["filename"]) AND file_exists($prepared_img_path.$image["filename"])) ? $item_img_path.$image["filename"] : $img_path.'noimage.jpg';
                if (!empty($arAliases)) {
                    foreach($arAliases as $arAlias) {
                        $image[$arAlias[0].$colname] = (!empty($image["filename"]) AND file_exists($prepared_img_path.$arAlias[0].$image["filename"])) ? $item_img_path.$arAlias[0].$image["filename"] : $img_path.$arAlias[0].'noimage.jpg';
                    }
                } array_push($item["images"], $image);
                if (!$i) {
                    $item[$colname] = $image[$colname];
                    foreach($arAliases as $arAlias) {
                        $item[$arAlias[0].$colname] = $image[$arAlias[0].$colname];
                    }
                } $i++;
            }
        } else {
            $item[$colname] = $img_path.'noimage.jpg';;
            if (!empty($arAliases)) {
                foreach($arAliases as $arAlias) {
                    $item[$arAlias[0].$colname] = $img_path.$arAlias[0].'noimage.jpg';
                }
            }
        }
    }

    protected static function setItemIdKey (&$item) {
        foreach(array_keys($item["sizes"]) as $key){
            // make item idKey
            $item["sizes"][$key]["idKey"] = self::makeItemIdKey($item["id"], $item["sizes"][$key]["id"]);
        }
        // set default size
        $item["size_id"]    = 0;
        $item["size_title"] = '';
        if($item["sizes"] && ($size = reset($item["sizes"]))) {
            $item["size_id"]    = $size['id'];
            $item["size_title"] = $size['title'];
        }
        // make default item idKey
        $item["idKey"] = self::makeItemIdKey($item["id"], $item["size_id"]);
    }

    public static function makeItemIdKey ($catalogID = 0, $sizeID = 0) {
        return self::packItemIdKey(array($catalogID, $sizeID), 'c%ds%d');
    }

    public static function parseItemIdKey ($idKey) {
        return self::unPackItemIdKey($idKey, '/^c(\d+)s(\d+)$/i');
    }
}
