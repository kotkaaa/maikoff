<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

/*
 * WebLife CMS
 * Created on 06.06.2018, 14:18:47
 * Developed by http://weblife.ua/
 */

require_once 'Product.php';

/**
 * Description of PrintProduct
 * Use $product_id for print assortment color row
 *
 * @author Andreas
 */
class PrintProduct extends Product {

    const MODULE = 'prints';
    const SIDE_FRONT = 'front';
    const SIDE_REAR = 'rear';

    /**
     * @var array 
     */
    public static $arSides = array(
        self::SIDE_FRONT => 'перед', 
        self::SIDE_REAR => 'спина', 
    );
    private static $arrSides;
    private static $arrDimensions;

    /**
     * @param string $id
     * @param string $title
     * @return array
     */
    private static function _getSide($id, $title) {
        return array(
            'id' => $id, 
            'title' => $title, 
            'column' => 'img_' . $id,
        );
    }

    /**
     * @return string
     */
    public static function getModule(){
        return self::MODULE;
    }

    /**
     * @param int $substrateID
     * @param int $colorID
     * @param string $placement
     * @return string
     */
    final public static function getTypeColorImage($substrateID, $colorID, $placement){
        $path = UPLOAD_DIR . DIRECTORY_SEPARATOR . 'substrates';
        $pattern = self::createSubstrateColorFileName($substrateID, $colorID, $placement, '*');
        return self::findFile($path . DIRECTORY_SEPARATOR . $pattern);
    }

    /**
     * @param int $logoID
     * @return string
     */
    final public static function getLogoImage($logoID){
        $path = self::getImagesDir();
        $pattern = self::createLogoFileName($logoID, '*');
        return self::findFile($path . DIRECTORY_SEPARATOR . $pattern);
    }

    /**
     * @param int $width
     * @param int $offset
     * @return array
     */
    public static function getDimension($width = 0, $offset = 0) {
        return array(
            'width'  => $width,
            'offset' => $offset,
        );
    }

    /**
     * @param string $sideID
     * @return array
     */
    public static function getSides($sideID = '') {
        if (self::$arrSides === null) {
            self::$arrSides = array();
            foreach(self::$arSides as $id => $title){
                self::$arrSides[$id] = self::_getSide($id, $title);
            }
        } return $sideID ? (array_key_exists($sideID, self::$arrSides) ? self::$arrSides[$sideID] : null) : self::$arrSides;
    }

    /**
     * @param string $sideID
     * @return array
     */
    public static function getDimensions($sideID = '') {
        if (self::$arrDimensions === null) {
            self::$arrDimensions = array();
            foreach(self::$arSides as $id => $title){
                self::$arrDimensions[$id] = self::getDimension();
            }
        } return $sideID ? (array_key_exists($sideID, self::$arrDimensions) ? self::$arrDimensions[$sideID] : null) : self::$arrDimensions;
    }

    /**
     * @param array $dimensions
     * @return string
     */
    public static function dimensionsToDB($dimensions) {
        return serialize((empty($dimensions) OR !is_array($dimensions)) ? self::getDimensions() : $dimensions);
    }

    /**
     * @param string $dimensions
     * @return array
     */
    public static function dimensionsFromDB($dimensions) {
        return (empty($dimensions) || !($dimensions = (array)unserialize($dimensions))) ? self::getDimensions() : $dimensions;
    }

    /**
     * @param string $substrateID
     * @param string $seoName
     * @return string
     */
    public static function createSubstrateFileName($substrateID, $seoName) {
        return self::createFileNameElement('t', $substrateID, $seoName);
    }

    /**
     * @param string $logoID
     * @param string $seoName
     * @return string
     */
    public static function createLogoFileName($logoID, $seoName) {
        return self::createFileNameElement('l', $logoID, $seoName);
    }

    /**
     * @param string $colorID
     * @param string $seoName
     * @return string
     */
    public static function createColorFileName($colorID, $seoName) {
        return self::createFileNameElement('c', $colorID, $seoName);
    }

    /**
     * @param string $substrateID
     * @param string $colorID
     * @param string $placement
     * @param string $seoName
     * @return string
     */
    public static function createSubstrateColorFileName($substrateID, $colorID, $placement, $seoName) {
        return self::createSubstrateFileName($substrateID.self::createColorFileName($colorID.$placement, false), $seoName);
    }

    /**
     * @param string $width
     * @return string
     */
    protected static function createWidthForFileName($width) {
        return self::createFileNameElement('w', $width, false);
    }

    /**
     * @param string $offset
     * @return string
     */
    protected static function createOffsetForFileName($offset) {
        return self::createFileNameElement('o', $offset, false);
    }

    /**
     * @param string $alias
     * @return string
     */
    protected static function createAliasForFileName($alias) {
        return self::createFileNameElement('0', $alias, false);
    }

    /**
     * @param string $printID
     * @param string $logoID
     * @param string $width
     * @param string $offset
     * @param string $substrateID
     * @param string $colorID
     * @param string $placement
     * @param string $alias
     * @param string $seoName
     * @return string
     */
    private static function _createSpoolFileName($printID, $logoID, $width, $offset, $substrateID, $colorID, $placement, $alias, $seoName) {
        return self::createItemFileName($printID
             . self::createLogoFileName($logoID, false)
             . self::createWidthForFileName($width)
             . self::createOffsetForFileName($offset)
             . self::createSubstrateColorFileName($substrateID, $colorID, $placement, false)
             . self::createAliasForFileName($alias)
        , $seoName);
    }

    /**
     * @param string $printID
     * @param string $logoID
     * @param string $substrateID
     * @param string $colorID
     * @param string $placement
     * @return string
     */
    private static function _createSpoolPatternFileName($printID=self::PATTERN_NUMERIC, $logoID=self::PATTERN_NUMERIC, $substrateID=self::PATTERN_NUMERIC, $colorID=self::PATTERN_NUMERIC, $placement=self::PATTERN_STRING) {
        return self::_createSpoolFileName($printID, $logoID, self::PATTERN_NUMERIC, self::PATTERN_NUMERIC, $substrateID, $colorID, $placement, self::PATTERN_STRING, self::PATTERN_IMAGE);
    }

    /**
     * @param int $printID
     * @param int $logoID
     * @param int $width
     * @param int $offset
     * @param int $substrateID
     * @param int $colorID
     * @param string $placement
     * @param string $alias
     * @param string $seoName
     * @return string
     */
    public static function createSpoolFileName($printID, $logoID, $width, $offset, $substrateID, $colorID, $placement, $alias, $seoName) {
        return self::_createSpoolFileName($printID, $logoID, $width, $offset, $substrateID, $colorID, $placement, $alias, $seoName).'.'.self::SPOOL_EXT;
    }

    /**
     * @param string $fileName
     * @return array
     */
    public static function parseSpoolFileName($fileName) {
        $pattern = '/' . self::_createSpoolFileName('(\d+)', '(\d+)', '(\d+)', '(\d+)', '(\d+)', '(\d+)', '([a-z]+)', '([a-z]+)', '(.*)?\.('.self::SPOOL_EXT.')') . '/i';
        if(preg_match($pattern, $fileName, $matches)){
            return array(
                'printID'   => $matches[1],
                'logoID'    => $matches[2],
                'width'     => $matches[3],
                'offset'    => $matches[4],
                'substrateID' => $matches[5],
                'colorID'   => $matches[6],
                'placement' => $matches[7],
                'alias'     => $matches[8],
                'seoName'   => $matches[9],
                'ext'       => $matches[10],
                'fileName'  => self::createSpoolFileName($matches[1], $matches[2], $matches[3], $matches[4], $matches[5], $matches[6], $matches[7], $matches[8], $matches[9]),
            );
        } return null;
    }

    /**
     * @param string $itemID
     * @return int
     */
    public static function deleteSpoolByItem($itemID){
        return self::deleteSpoolFiles(self::_createSpoolPatternFileName($itemID));
    }

    /**
     * @param string $substrateID
     * @return int
     */
    public static function deleteSpoolBySubstrate($substrateID){
        return self::deleteSpoolFiles(self::_createSpoolPatternFileName(self::PATTERN_NUMERIC, self::PATTERN_NUMERIC, $substrateID));
    }

    /**
     * @param string $printID
     * @param string $logoID
     * @return int
     */
    public static function deleteSpoolByPrintLogo($printID, $logoID){
        return self::deleteSpoolFiles(self::_createSpoolPatternFileName($printID, $logoID));
    }

    /**
     * @param string $substrateID
     * @param string $colorID
     * @param string $placement
     * @return int
     */
    public static function deleteSpoolBySubstrateColor($substrateID, $colorID, $placement=self::PATTERN_STRING){
        return self::deleteSpoolFiles(self::_createSpoolPatternFileName(self::PATTERN_NUMERIC, self::PATTERN_NUMERIC, $substrateID, $colorID, $placement));
    }

    /**
     * @param int $printID
     * @param int $substrateID
     * @param int $sortID
     * @return int
     */
    protected static function findDefaultSubstrateId($printID, $substrateID, $sortID = 0) {
        // переопределяем подложку по умолчанию если установлена сортировка или подложка по умолчанию не установлен
        $sort = ($sortID == PHPHelper::SORT_PRICE_ASC || $sortID == PHPHelper::SORT_PRICE_DESC);
        if($sort || !$substrateID) {
            $select = 'pa.`substrate_id`';
            $table  = PRINT_ASSORTMENT_TABLE . ' pa'.PHP_EOL
                . "JOIN `".PRINT_ASSORTMENT_COLORS_TABLE."` pc ON(pc.`assortment_id`=pa.`id`) ".PHP_EOL
                . "JOIN `".PRINT_ASSORTMENT_SETTINGS_TABLE."` ps ON(ps.`assortment_id`=pa.`id` AND ps.`file_id`=pc.`file_id`) ".PHP_EOL;
            $where = "WHERE pa.`print_id`={$printID} AND pa.`active`=1 AND pc.`active`=1 AND ps.`active`=1".PHP_EOL;
            $order = "ORDER BY ";
            if($sort) 
                $order .= "IF(`price`>0,1,0) DESC, `price` " . (($sortID == PHPHelper::SORT_PRICE_ASC) ? 'ASC' : 'DESC') . ", ";
            $order .= "pa.`isdefault` DESC, pc.`isdefault` DESC".PHP_EOL;
            $limit = "LIMIT 1";
            if(($found = getValueFromDB($table, $select, $where.$order.$limit, 'substrate_id'))) {
                $substrateID = $found;
            }
        }
        return $substrateID;
    }
     
    /**
     * @inheridoc
     */
    public static function getItemsSql($where = '', $group = '', $having = '', $order = '', $limit = '', $sortID = 0){
        $priceColumn = 'ti.`product_price`';
        if($sortID == PHPHelper::SORT_PRICE_ASC)
            $priceColumn = 'MIN('.$priceColumn.')';
        else if($sortID == PHPHelper::SORT_PRICE_DESC) 
            $priceColumn = 'MAX('.$priceColumn.')';
        return 'SELECT p.*, t.*, p.`assortment_id` `id`, p.`substrate_id`, /*t.`comments_count`*/(0) `commentsCnt` ' . PHP_EOL
             . 'FROM ('
                . 'SELECT ti.*, '.$priceColumn.' `price`, ti.`product_sequence` `sequence`, IF(ti.`shortcut_id`>0,1,0) `isshortcut`, IF(ti.`shortcut_id`>0, ti.`category_id`, 0) `scid`'. PHP_EOL
                . 'FROM `' . PRINT_INDEX_TABLE . '` ti '. PHP_EOL
                . $where
                . $group
                . $having
             . ') p'. PHP_EOL
             . 'INNER JOIN `'.PRINTS_TABLE.'` t ON (t.`id` = p.`print_id`)' . PHP_EOL
             . $order . PHP_EOL
             . $limit . PHP_EOL;
    }

    /**
     * @param array $ids
     * @param UrlWL $UrlWL
     * @param boolean $inList
     * @param boolean $checkActive
     * @param int $limit
     * @return array
     */
    public static function getItems(array $ids, UrlWL $UrlWL, $inList = true, $checkActive = true, $limit = 0) {
        $rows = array();
        if($ids) {
            $idSet  = implode(',', $ids);
            $query  = "SELECT p.*, pa.*, pc.`id` `product_id`, pc.`assortment_id`, "
                         . "ps.`file_id` `logo_id`, ps.`width` `logo_width`, ps.`height` `logo_height`, ps.`offset` `logo_offset` "
                    . "FROM `".PRINT_ASSORTMENT_TABLE."` pa ".PHP_EOL
                    . "JOIN `".PRINTS_TABLE."` p ON(p.`id`=pa.`print_id`) ".PHP_EOL
                    . "JOIN `".PRINT_ASSORTMENT_COLORS_TABLE."` pc ON(pc.`assortment_id`=pa.`id` AND pc.`color_id` = pa.`color_id`) ".PHP_EOL
                    . "JOIN `".PRINT_ASSORTMENT_SETTINGS_TABLE."` ps ON(ps.`assortment_id`=pa.`id` AND ps.`file_id`=pc.`file_id`) ".PHP_EOL
                    . "WHERE pa.`id` IN({$idSet})" . ($checkActive ? " AND pa.`active`=1 AND pc.`active`=1 AND ps.`active`=1 AND p.`active`=1" : '').PHP_EOL
                    . "ORDER BY FIND_IN_SET(pa.`id`, '{$idSet}')".PHP_EOL
                    . ($limit ? "LIMIT {$limit}" : '');
            $result = mysql_query($query);
            if ($result && mysql_num_rows($result)>0) {
                while ($row = mysql_fetch_assoc($result)) {
                    $rows[] = self::getItem($row, $UrlWL, true, false, $inList);
                }
            }
        } return $rows;
    }

    /**
     * @param array $item
     * @param UrlWL $UrlWL
     * @param boolean $separateByType
     * @param boolean $separateByColor
     * @param boolean $inList
     * @param int $sortID
     * @return array
     */
    public static function getItem($item, UrlWL $UrlWL, $separateByType = false, $separateByColor = false, $inList = true, $sortID = 0) {
        if (!empty($item)) {
            // определяем категорию
            isset($item['arCategory']) || $item['arCategory'] = $UrlWL->getCategoryById($item['category_id']);
            // определяем подложку по умолчанию
            if ($separateByType || $separateByColor) $item['default_substrate_id'] = $item['substrate_id'];
            // переопределяем подложку по умолчанию если установлена сортировка
            else $item['default_substrate_id'] = self::findDefaultSubstrateId($item['print_id'], $item['default_substrate_id'], $sortID);
            // Set vars
            $item['descr'] = isset($item['descr']) ? unScreenData($item['descr']) : '';
            $item['text']  = (isset($item['text']) && !$inList) ? unScreenData($item['text']) : '';
            // set price with discount
            $item['new_price'] = 0;
            // get substrate
            $item['substrate'] = $inList ? array() : getSimpleItemRow($item['default_substrate_id'], SUBSTRATES_TABLE);
            // get size grid
            $item['size_grid']  = !empty($item['substrate']) ? getValueFromDB(SIZE_GRIDS_TABLE, "descr", "WHERE `id`=".$item['substrate']["size_grid_id"]) : array();
            // get all substrates
            $item['substrates'] = self::getItemSubstrates($item['print_id'], $item['default_substrate_id'], $item['placement'], $inList);
            // set item attributes
            $item['attributes'] = self::getItemAttributes($item['arCategory']["id"], $item['print_id'], 'pid', PRINT_ATTRIBUTES_TABLE, $inList, true, false);
            $item['attributes'] = array_merge($item['attributes'], self::getItemAttributes($item['arCategory']["id"], $item['substrate_id'], 'sid', SUBSTRATES_ATTRIBUTES_TABLE, $inList, true, false));
            // get sizes
            $item["sizes"] = self::getItemSizes($item["default_substrate_id"], $inList);
            // set assortment
            self::setItemAssortment($item, $separateByType, $separateByColor, $inList);
            // set item idKey Keys
            self::setItemIdKey($item);
            // set basket qty data
            self::setItemBasketQty($item);
            // color hash
            $item["color_hash"] = $separateByColor ? '#'.$item['color_hex'] : '';
            // product url
            $item["product_url"] = $UrlWL->buildItemUrl($item['arCategory'], $item);
        } return $item;
    }

    /**
     * simple get item with only substrates sizes and assortment
     * @param array $item
     * @return array
     */
    public static function getSimpleItem($item, $idkey = false, $separateByColor = false, $colorID = 0, $price = '') {
        if (!empty($item)) {
            // get all substrates
            $item['substrates'] = self::getItemSubstrates($item['print_id'], $item['default_substrate_id'], $item['placement'], false);
            // get sizes
            $item["sizes"] = self::getItemSizes($item["default_substrate_id"], false);
            // set assortment
            self::setItemAssortment($item, false, $separateByColor, false, $colorID, $price);
            if($idkey) {
                // set item idKey Keys
                $item["idKey"] = self::makeItemIdKey($item["assortment_id"], $item['color_id'], $item["size_id"]);
            }
            if($price) {
                $item['price'] = $price;
            }
        } return $item;
    }
    /**
     * @param array $item
     * @param boolean $separateByType
     * @param boolean $separateByColor
     * @param boolean $inList
     */
    private static function setItemAssortment(&$item, $separateByType = false, $separateByColor = false, $inList = true, $colorID = 0) {
        $item['assortment'] = array();
        //выбираем все цвета для товара по дефолтному/переданной подложке сортируем по сортировке цвета
        $query  = "SELECT pc.`id` `product_id`, pc.`assortment_id`, pa.`print_id`, pa.`substrate_id`, pa.`seo_path`, pa.`price`, pc.`isdefault` `is_default`, 
                          ps.`file_id` `logo_id`, ps.`width` `logo_width`, ps.`height` `logo_height`, ps.`offset` `logo_offset`, 
                          c.`id` `color_id`, c.`hex` `color_hex`, c.`title` `color_title`, st.`title` `substrate_title` ".PHP_EOL
                . "FROM `".PRINT_ASSORTMENT_TABLE."` pa ".PHP_EOL
                . "LEFT JOIN `".PRINT_ASSORTMENT_COLORS_TABLE."` pc ON(pc.`assortment_id`=pa.`id`) ".PHP_EOL
                . "LEFT JOIN `".PRINT_ASSORTMENT_SETTINGS_TABLE."` ps ON(ps.`assortment_id`=pa.`id` AND ps.`file_id`=pc.`file_id`) ".PHP_EOL
                . "LEFT JOIN `".COLORS_TABLE."` c ON(c.`id`=pc.`color_id`) ".PHP_EOL
                . "LEFT JOIN `".SUBSTRATES_TABLE."` st ON(st.`id`=pa.`substrate_id`) ".PHP_EOL
                . "WHERE pa.`print_id`={$item['print_id']} AND  pa.`substrate_id`={$item['default_substrate_id']} AND pa.`active`=1". ($separateByColor ? 
                   " AND pc.`color_id`={$item['color_id']}" : "")." AND pc.`active`=1 AND ps.`active`=1 AND st.`active`=1 ".PHP_EOL
                . "ORDER BY pc.`isdefault` DESC, pc.`order`";
        $result = mysql_query($query);
        if ($result and mysql_num_rows($result)) {
            $defID = $cnt = 0; 
            $rows  = array();
            while ($assort = mysql_fetch_assoc($result)) {
                $assort['placement'] = $item['placement'];
                self::setItemImages($assort);
                // поиск ассортимента по умолчанию
                if(!$colorID && ($assort['is_default'] || $separateByColor) || $colorID == $assort['color_id']) 
                    $defID = $assort['product_id'];
                // добавляем к товару
                $rows[$assort['product_id']] = $assort;
                // увеличиваем количество найденных
                $cnt++;
            }
            // заменяем ключи значениями из ассортимента
            $item = array_merge($item, ($defID ? $rows[$defID] : reset($rows)));
            // добавляем к товару
            if(!$inList || $cnt > 1)
                $item['assortment'] = array_values($rows);
        }
    }
    
    /**
     * @example PHPHelper::getItemSizes()
     * @param int $itemID
     * @return array
     */
    public static function getItemSizes($itemID, $inList = true) {
        $items  = array();
        if (!$inList) { // чтобы не нагружать выдачу - размеры нужны только на карточке товара
            $query  = "SELECT DISTINCT s.* FROM `".SUBSTRATES_SIZES_TABLE."` ss "
                    . "LEFT JOIN `".SIZES_TABLE."` s ON(s.`id` = ss.`size_id`) "
                    . "WHERE ss.`substrate_id`=$itemID "
                    . "ORDER BY s.`order`";
            $result = mysql_query($query);
            if ($result and mysql_num_rows($result)) {
                while ($item = mysql_fetch_assoc($result)) {
                    $items[] = $item;
                }
            }
        } return $items;
    }
    
    public static function getItemSubstrates($printID, $substrateID, $placement, $inList = true) {
        $items  = array();
        if (!$inList) { // чтобы не нагружать выдачу - все подложки нужны только на карточке товара
            $query  = "SELECT pc.`id` `product_id`, pc.`assortment_id`, pa.`print_id`,  pa.`substrate_id`, pa.`seo_path`, pa.`price`, st.`title` `substrate_title`,
                              ps.`file_id` `logo_id`, ps.`width` `logo_width`, ps.`height` `logo_height`, ps.`offset` `logo_offset`, 
                              c.`id` `color_id`, c.`hex` `color_hex`, c.`title` `color_title`, IF(pa.`substrate_id`={$substrateID}, 1, 0) `selected` "
                    . "FROM `".PRINT_ASSORTMENT_TABLE."` pa ".PHP_EOL
                    . "LEFT JOIN `".SUBSTRATES_TABLE."` st ON st.`id`=pa.`substrate_id` ".PHP_EOL
                    . "LEFT JOIN `".PRINT_ASSORTMENT_COLORS_TABLE."` pc ON (pc.`assortment_id` = pa.`id` AND pc.`color_id` = pa.`color_id`) ".PHP_EOL
                    . "LEFT JOIN `".PRINT_ASSORTMENT_SETTINGS_TABLE."` ps ON(ps.`assortment_id`=pa.`id` AND ps.`file_id`=pc.`file_id`) ".PHP_EOL
                    . "LEFT JOIN `".COLORS_TABLE."` c ON c.`id`=pa.`color_id` ".PHP_EOL
                    . "WHERE pa.`print_id`={$printID}  AND pa.`active`=1 AND st.`active`=1 AND pc.`active`=1 AND ps.`active`=1 "
                    . "ORDER BY `selected` DESC, pa.`order`";
            $result = mysql_query($query);
            if ($result and mysql_num_rows($result)) {
                while ($item = mysql_fetch_assoc($result)) {
                    $item['placement'] = $placement;
                    self::setItemImages($item);
                    $items[] = $item;
                }
            }
        } return $items;
    }
    
    /**
     * @param array $item
     * @param string $colname
     */
    public static function setItemImages (&$item, $colname='image') {
        static $imageParams = null;
        if($imageParams === null) $imageParams = self::getSpooledImagesParams();
        foreach ($imageParams as $params) {
            list($prefix, , , $alias) = $params;
            $item[$prefix.$colname] = self::getSpoolUrl($item['print_id'], self::createSpoolFileName(
                $item['print_id'], 
                $item['logo_id'], 
                $item['logo_width'], 
                $item['logo_offset'], 
                $item['substrate_id'], 
                $item['color_id'], 
                $item['placement'], 
                $alias, 
                $item['seo_path']
            ));
            // @fortest
            $item[$prefix.$colname] .= '?t='.time();
        }
    }

    protected static function setItemIdKey (&$item) {
        // set default size
        $item["size_id"]    = 0;
        $item["size_title"] = '';
        if($item["sizes"] && ($size = reset($item["sizes"]))) {
            $item["size_id"]    = $size['id'];
            $item["size_title"] = $size['title'];
        }
        // get size keys
        $sizes_keys  = array_keys($item["sizes"]);
        // make item idKey for colors
        foreach($item["assortment"] as &$assort){
            // with default size
            $assort["idKey"] = self::makeItemIdKey($assort["assortment_id"], $assort["color_id"], $item["size_id"]);
            // set assort size idKeys
            $assort["sizes"] = array();
            foreach($sizes_keys as $k){
                $sizes_id = $item["sizes"][$k]["id"];
                $assort["sizes"]['s'.$sizes_id] = array(
                    "id" => $sizes_id,
                    "idKey" => self::makeItemIdKey($assort["assortment_id"], $assort["color_id"], $sizes_id),
                );
            }
        }
        // make item idKey for sizes 
        foreach($sizes_keys as $key){
            // with default color
            $item["sizes"][$key]["idKey"] = self::makeItemIdKey($item["assortment_id"], $item["color_id"], $item["sizes"][$key]["id"]);
        }
        // make default item idKey
        isset($item["idKey"]) OR $item["idKey"] = self::makeItemIdKey($item["assortment_id"], $item["color_id"], $item["size_id"]);
    }

    protected static function setItemBasketQty(&$item) {
        global $Basket;
        // set for colors
        foreach($item["assortment"] as &$assort){
            // sizes in basket by color
            $assort["basket_qty"] = 0;
            foreach($assort["sizes"] as &$size){
                $size["basket_qty"] = $Basket->qty($size["idKey"]);
                $assort["basket_qty"] += $size["basket_qty"];
            }
        }
        parent::setItemBasketQty($item);
    }

    public static function makeItemIdKey ($assortmentID = 0, $colorID = 0, $sizeID = 0) {
        return self::packItemIdKey(array($assortmentID, $colorID, $sizeID), 'pa%dc%ds%d');
    }

    public static function parseItemIdKey ($idKey) {
        return self::unPackItemIdKey($idKey, '/^pa(\d+)c(\d+)s(\d+)$/i');
    }

    /**
     * @global UrlWL $UrlWL
     * @global DbConnector $DB
     * @param int $assortID
     * @param string $substrateSeoPath
     * @param string $printSeoPath
     * @return string
     */
    public static function getAssortmentSeoPath ($assortID, $substrateSeoPath, $printSeoPath) {
        global $UrlWL, $DB;
        return $UrlWL->strToUniqueUrl($DB, implode('-', array($substrateSeoPath, $printSeoPath)), self::getModule(), PRINT_ASSORTMENT_TABLE, $assortID, false);
    }

    /**
     * @param int $substrateID
     * @param int $printID
     * @return int affected rows
     */
    public static function updateAssortmentSeoPathes ($substrateID, $printID) {
        $affected = 0;
        if($substrateID || $printID) {
            $conditions = array();
            if($substrateID) $conditions[] = 'pa.`substrate_id`='.$substrateID;
            if($printID) $conditions[] = 'pa.`print_id`='.$printID;
            $where = 'WHERE ' . implode(' OR ', $conditions);
            // вначале обнуляем записи для корректной работы подбора уникальных сеопутей
            updateRecords(PRINT_ASSORTMENT_TABLE.' pa', 'pa.`seo_path`=""', $where);
            // выбираем для обработки и установки новых сеопутей только для дефолтных ассортиментов где установлен дефолтный цвет
            $query  = "SELECT pa.`id`, st.`seo_path` `substrate_seo_path`, p.`seo_path` `print_seo_path` " . PHP_EOL
                . "FROM `".PRINT_ASSORTMENT_TABLE."` pa " . PHP_EOL
                . "INNER JOIN `".SUBSTRATES_TABLE."` st ON(st.`id`=pa.`substrate_id`) " . PHP_EOL
                . "INNER JOIN `".PRINTS_TABLE."` p ON(p.`id`=pa.`print_id`) " . PHP_EOL
                . $where;
            $result = mysql_query($query);
            while ($row = mysql_fetch_assoc($result)) {
                $seopath = self::getAssortmentSeoPath ($row['id'], $row['substrate_seo_path'], $row['print_seo_path']);
                if(updateRecords(PRINT_ASSORTMENT_TABLE, '`seo_path`="'.$seopath.'"', 'WHERE `id`='.$row['id'].' LIMIT 1')){
                    $affected++;
                }
            }
        } return $affected;
    }
    
    public static function saveActiveAssortments($printID, $assortment, $substrates, $print_seopath, $defSubstrate = 0) {
        foreach ($assortment as $substrateID => $assort) {
            if($defSubstrate) $assort['isdefault'] = ($substrateID == $defSubstrate ? 1 : 0);
            $assort['order'] = $assort['order']>0 ? $assort['order'] : getMaxPosition($printID, 'order', 'print_id', PRINT_ASSORTMENT_TABLE);
            $assort['seo_path'] = self::getAssortmentSeoPath($assort['id'], $substrates[$substrateID]['seo_path'], $print_seopath);
            $query = 'INSERT INTO '.PRINT_ASSORTMENT_TABLE.' (`print_id`, `substrate_id`, `price`, `seo_path`, `order`, `active`, `isdefault`) '
                    . 'VALUES ("'.$printID.'", "'.$substrateID.'", "'.$assort['price'].'", "'.$assort['seo_path'].'", "'.$assort['order'].'", 1, "'.$assort['isdefault'].'") '
                    . 'ON DUPLICATE KEY UPDATE `price`="'.str_replace(',', '.', $assort['price']).'", `seo_path`="'.$assort['seo_path'].'", `active`=1, `isdefault`="'.$assort['isdefault'].'"';
            //var_dump($query);
            $result = mysql_query($query);
            if($assort['isdefault']) {
                updateRecords(PRINTS_TABLE, 'substrate_id='.$substrateID, 'WHERE id='.$printID);
            }
        } 
    }
    
    public static function getAssortment($printID, $fileID = 0) {
        $table = PRINT_ASSORTMENT_TABLE.' pa LEFT JOIN '.SUBSTRATES_TABLE.' st ON st.`id`=pa.`substrate_id` '
                . 'LEFT JOIN '.PRINT_ASSORTMENT_SETTINGS_TABLE.' ps ON ps.`assortment_id`=pa.`id` AND ps.`file_id`='.$fileID;
        $select = 'pa.*, st.`title`, st.`dimensions`, IFNULL(ps.`id`, 0) `settings_id`, '
                . 'IFNULL(ps.`width`, 0) `width`, IFNULL(ps.`height`, 0) `height`, IFNULL(ps.`offset`, 0) `offset`, IFNULL(ps.`active`, 0) `active`';
        $where = 'WHERE pa.`print_id`='.$printID.' AND pa.`active`=1';
        return getRowItemsInKey('substrate_id', $table, $select, $where);
    }
}
