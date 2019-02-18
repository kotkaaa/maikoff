<?php

/**
 * WEBlife CMS
 * Created on 10.10.2011, 12:20:17
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access

require_once __DIR__.'/../classes/Cache.php';  
require_once __DIR__.'/../classes/Selections.php';
require_once __DIR__.'/../classes/Pager.php';
require_once __DIR__.'/../classes/ImportExport.php';
require_once __DIR__.'/MetaHelper.php';
require_once __DIR__.'/OrderHelper.php';

/**
 * Description of PHPHelper class
 * This class provides methods for help you to change php data, or do sumsing dunamicaly
 * You can add new methods for your needs
 * @author WebLife
 * @copyright 2011
 */
class PHPHelper {
    
    const SORT_UNDEFINED  = 0;
    const SORT_DEFAULT    = 1;
    const SORT_PRICE_ASC  = 2;
    const SORT_PRICE_DESC = 3;

    /**
     * Список сортировок
     * @var array
     */
    static $SORT_COLUMNS = array(
        self::SORT_DEFAULT => array(
            'active' => true,
            'title'  => 'По популярности',
            'column' => '`sequence`',
            'show'   => 0,
        ),
        self::SORT_PRICE_ASC => array(
            'active' => false,
            'title' => 'От дешевых к дорогим',
            'column' => '`price` ASC',
            'show'   => 1,
        ),
        self::SORT_PRICE_DESC => array(
            'active' => false,
            'title'  => 'От дорогих к дешевым',
            'column' => '`price` DESC',
            'show'   => 1,
        ),
    );
    
    static $meta_template = "{filter_%s}";
    
    private static $dummyCache = NULL;
    private static $redisCache = NULL;
    private static $memCache = NULL;
    /**
     * @return DummyCacheWL
     */
    private static function getDummyCache() {
        if(self::$dummyCache === NULL) {
            self::$dummyCache = new DummyCacheWL();
        }
        return self::$dummyCache;
    } 
    /**
     * @return RedisCacheWL|DummyCacheWL
     */
    public static function getRedisCache() {
        if(WLCMS_USE_CACHE && self::$redisCache === NULL) {
            self::$redisCache = new RedisCacheWL();
        }
        return WLCMS_USE_CACHE ? self::$redisCache : self::getDummyCache();
    } 
    /**
     * @return MemCacheWL|DummyCacheWL
     */
    public static function getMemCache() {
        if(WLCMS_USE_CACHE && self::$memCache === NULL) {
            self::$memCache = new MemCacheWL();
        }
        return WLCMS_USE_CACHE ? self::$memCache : self::getDummyCache();
    } 

    
    /**
     * <p>Функция clearModulesData - CLEAR CATEGORY MODULES DATA. <br/>
     * Данная информация должна соответсвовать данным из модуля админки при удалении елемента. <br/>
     *  ! $params обязательно нужно чтобы массив из модуля админки $arrPageData['images_params']; <br/>
     *  ! логика удаления была полностью скопирована в соответствующий case switchа с модуля в папке admin; <br/>
     *  ! путь к файлам модуля должен быть правильный;<br/>
     * </p>
     * 
     * @param int $id           идентификатор удаляемой категории
     * @param String $module    модуль категории, которую нужно удалить
     * @param String $filepath  путь папки с файлами данного модуля
     */
    public static function clearModulesData($id, $module, $filepath){
        // Получаем путь к файлам модуля
        $filepath = prepareDirPath($filepath);
        if(!$filepath) return;
            
        if($id AND $module){
            
            switch ($module) {

                case 'catalog': // CATALOG_TABLE
                    $items = getRowItems(CATALOG_TABLE, '`id`', "`cid` = $id ");
                    while($item = each($items)){
                        $itemID = $item['value']['id'];
                        self::deleteProduct($itemID, $filepath.$itemID.'/');         
                    }
                    //delete category atribute groups
                    deleteRecords(CATEGORY_ATTRIBUTE_GROUPS_TABLE, ' WHERE `cid`='.$id);  
                    //delete category atributes
                    deleteRecords(CATEGORY_ATTRIBUTES_TABLE, ' WHERE `cid`='.$id);
                    //delete category filters
                    deleteRecords(CATEGORY_FILTERS_TABLE, ' WHERE `cid`='.$id);
                    //update banners redirectid
                    updateRecords(BANNERS_TABLE, '`redirectid`=0', ' WHERE `redirectid`='.$id);
                    //update main redirectid
                    updateRecords(MAIN_TABLE, '`redirectid`=0', ' WHERE `redirectid`='.$id);
                    break;

                case 'gallery': // GALLERY_TABLE
                    $items  = getRowItems(GALLERY_TABLE, '`id`, `title`', "`cid` = $id ");
                    while($item = each($items)){
                        $itemID = $item['value']['id'];
                        self::deleteImages($itemID, $filepath, $module);
                        deleteFileFromDB_AllLangs($itemID, GALLERY_TABLE, 'filename', ' WHERE `id`='.$itemID, $filepath);
                        deleteDBLangsSync(GALLERY_TABLE, ' WHERE id='.$itemID);
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item['value']['title'].'"', $key, $item['value']['title'], 0, 'gallery');
                    } break;

                case 'news': // NEWS_TABLE
                    $items  = getRowItems(NEWS_TABLE, '`id`, `title`', "`cid` = $id ");
                    while($item = each($items)){
                        $itemID = $item['value']['id'];
                        self::deleteImages($itemID, $filepath, $module);
                        deleteDBLangsSync(NEWS_TABLE, ' WHERE id='.$itemID);
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item['value']['title'].'"', $key, $item['value']['title'], 0, 'news');              
                    } break;

                case 'video': // VIDEOS_TABLE
                    $items  = getRowItems(VIDEOS_TABLE, '`id`, `title`', "`cid` = $id ");
                    while($item = each($items)){
                        $itemID = $item['value']['id'];
                        self::deleteImages($itemID, $filepath, $module);
                        deleteFileFromDB_AllLangs($itemID, VIDEOS_TABLE, 'filename', ' WHERE `id`='.$itemID, $filepath);
                        deleteDBLangsSync(VIDEOS_TABLE, ' WHERE id='.$itemID);
                        foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$item['value']['title'].'"', $key, $item['value']['title'], 0, 'video');
                    } break;
                    
                default: break;
            }
        }
    }

    public static function addToLastWatched($itemID){
        global $Cookie;
        $itemsIDX = ($alias = $Cookie->getCookie('watched')) ? array_filter(unserialize($alias), 'intval') : array();
        // add itemID to watched
        if (!in_array($itemID, $itemsIDX))
            array_unshift($itemsIDX, intval($itemID));
        $itemsIDX = array_slice($itemsIDX, 0, 10);
        if($alias != ($newalias = serialize($itemsIDX))) {
            $Cookie->add('watched', $newalias, time()+(3600*24*7));
        }
    }

    public static function getLastWatched(UrlWL $UrlWL) {
        global $Cookie;
        if(($alias = $Cookie->getCookie('watched')) && ($itemsIDX = array_filter(unserialize($alias), 'intval')) && (($data = self::getMemCache()->get(CacheWL::KEY_WATCHED_ROWS)) === false || $data['alias'] != $alias) ) {
            require_once __DIR__.'/../classes/product/PrintProduct.php';
            $data  = array (
                'alias' => $alias, 
                'rows' => PrintProduct::getItems($itemsIDX, $UrlWL, true, false),
            );
            self::getMemCache()->set(CacheWL::KEY_WATCHED_ROWS, $data, (3600*24*7));
        }
        return isset($data['rows']) ? $data['rows'] : array();
    }
    
    public static function deleteImages($itemID, $files_path, $module) {
        $images_params = getRowItems(IMAGES_PARAMS_TABLE, '*', '`module`="'.$module.'"');
        foreach ($images_params as $param) {
            $aliases = SystemComponent::prepareImagesParams($param['aliases']);
            if ($param['ftable']) {
                unlinkImageLangsSynchronize($itemID, constant($param['ftable']), $files_path, $aliases, $param['column']);
                deleteRecords(constant($param['ftable']), 'WHERE `pid`='.$itemID);
            } else {
                unlinkImageLangsSynchronize($itemID, constant($param['ptable']), $files_path, $aliases, $param['column']);
            }
        }
    }
        
    public static function deleteModel($modelID) {
        //удаляем модель
        if(deleteDBLangsSync(MODELS_TABLE, 'WHERE `id`='.$modelID)) {
            //удаляем связанные атрибуты модели
            deleteDBLangsSync(MODEL_ATTRIBUTES_TABLE, 'WHERE `mid`='.$modelID);
            //удаляем товары          
            $error = false;
            if(($products = getArrValueFromDB(CATALOG_TABLE, 'id', 'WHERE `model_id`='.$modelID))) {
                $catalogfile_path = prepareDirPath(UPLOAD_URL_DIR.'catalog/', true);
                foreach($products as $productID) {
                    if(!PHPHelper::deleteProduct($productID, $catalogfile_path.DS.$productID)) {
                        $error = true;
                        break;
                    }
                } 
            } return $error ? false : true;
        } return false;
    }
        
    public static function deletePrint($printID) {
        //удаляем принт
        if (deleteDBLangsSync(PRINTS_TABLE, 'WHERE id='.$printID)) {
            //удаляем связанные ассортименты принта
            deleteDBLangsSync(PRINT_ASSORTMENT_TABLE, 'WHERE print_id='.$printID);   
            //удаляем связанные атрибуты принта
            deleteDBLangsSync(PRINT_ATTRIBUTES_TABLE, 'WHERE pid='.$printID);     
            //удаляем ярлыки
            deleteDBLangsSync(SHORTCUTS_TABLE, 'WHERE pid='.$printID); 
            //удаляем логотипы
            $files_path = prepareDirPath(UPLOAD_URL_DIR.'prints/');
            if (($logos = getArrValueFromDB(PRINTFILES_TABLE, 'id', 'WHERE `print_id`='.$printID))) {
                foreach($logos as $logoID) {
                    self::deletePrintFile($logoID, $printID, $files_path);
                }
            } return true;            
        } return false;
    }
    
    public static function deletePrintFile($fileID, $printID, $files_path) {      
        //удаляем настройки по ассортименту логотипа
        deleteRecords(PRINT_ASSORTMENT_SETTINGS_TABLE, 'WHERE `file_id`='.$fileID);
        //удаляем цвета по ассортименту логотипа
        deleteRecords(PRINT_ASSORTMENT_COLORS_TABLE, 'WHERE `file_id`='.$fileID);
        //удаляем файл фиически и из базы
        unlinkImagesLnSync($files_path, PRINTFILES_TABLE, 'filename',  'WHERE `print_id`='.$printID.' AND `id`='.$fileID, 'filename', false, true);
        return true;
    }
    
    public static function deleteProduct($productID, $catalogfiles_path, $module='catalog') {
        if($productID) {
            $title = getValueFromDB(CATALOG_TABLE, 'title', 'WHERE `id`='.$productID);
            //удаляем запись из каталога
            if(deleteDBLangsSync(CATALOG_TABLE, ' WHERE `id`='.$productID)) {
                //удаляем картинки и папку
                unlinkImagesLnSync($catalogfiles_path, CATALOGFILES_TABLE, 'filename', 'WHERE `pid`='.$productID);                
                removeDir($catalogfiles_path);
                //удаляем размеры
                deleteRecords(PRODUCT_SIZES_TABLE, 'WHERE `pid`='.$productID);
                //история
                ActionsLog::getInstance()->save(ActionsLog::ACTION_DELETE, 'Удалено "'.$title.'"', SystemComponent::getAcceptLangs(), $title, 0, $module);
                return true;
            } 
        } return false;
    }
    
    public static function saveAttributes($itemID, $DB, $table, $column, $arPostData) {
        $arValuesIDX = array(0);
        if (!empty($arPostData['attributes'])) {
            foreach ($arPostData['attributes'] as $aid => $arValues) {
                foreach ($arValues as $value_id) {
                    // create new product attribute or update if exists
                    if(!($attrValueID = (int)getValueFromDB($table, "id", "WHERE `{$column}`='{$itemID}' AND `aid`='{$aid}' AND `value`='{$value_id}'"))) {
                        $resID = $DB->postToDB(array(
                            'aid'     => $aid,
                            $column   => $itemID,                                
                            'value'   => $value_id,
                            'created' => date('Y-m-d H:i:s'),                                
                        ), $table);
                        if ($resID && is_int($resID)) {
                            $attrValueID = $resID;                                
                        }                            
                    } if($attrValueID) $arValuesIDX[] = $attrValueID;
                }
            }
        } deleteDBLangsSync($table, "WHERE `{$column}`={$itemID} AND `id` NOT IN(".implode(",", $arValuesIDX).")");
    }

    public static function prepareItemAttributes(&$item, $table, $column, $attrGroups) {
        $item["attributes"] = array();
        $squery = 'SELECT * FROM '.$table.' WHERE '.$column.'='.$item['id'];
        $result = mysql_query($squery);
        if ($result && mysql_num_rows($result)>0) {
            while (($row = mysql_fetch_assoc($result))) {
                if (!array_key_exists($row["aid"], $item["attributes"])) $item["attributes"][$row["aid"]] = array();
                array_push($item["attributes"][$row["aid"]], $row["value"]);
            }
        }
        $item['attrGroups']  = array();
        foreach ($attrGroups as $key=>$value) {
            if (!empty($attrGroups[$key]['attributes'])) {
                foreach ($attrGroups[$key]['attributes'] as $k=>$v) {
                    if (array_key_exists($attrGroups[$key]['attributes'][$k]['id'], $item['attributes'])) {
                        $item['attrGroups'][] = $attrGroups[$key]['id'];
                    }
                }
            }
        }
    }
    
    public static function prepareAttrGroups(&$arrPageData) {
        $arrPageData['attrGroups'] = getRowItems(ATTRIBUTE_GROUPS_TABLE.' ag', 'ag.*', 'ag.`active`=1', 'ag.`order`');
        if(!empty($arrPageData['attrGroups'])) {
            foreach ($arrPageData['attrGroups'] as $key => $value) {
                $arrPageData['attrGroups'][$key]['attributes'] = getRowItems(ATTRIBUTES_TABLE, '*', '`gid`='.$arrPageData['attrGroups'][$key]['id'], '`order`');
                foreach ($arrPageData['attrGroups'][$key]['attributes'] as $k=>$v){
                    $arrPageData['attrGroups'][$key]['attributes'][$k]['values'] = getRowItems(ATTRIBUTES_VALUES_TABLE, '*', '`aid`='.$v['id'], '`order`');
                }
            }
        }
    }
    
    public static function getProductItem($item, $UrlWL, $images_path, $arAliases, $inList=true, $shortcutCid=false, $module='catalog', $isKitItem = false) {
        global $objSettingsInfo;
        if (!empty($item)) {
            isset($item['arCategory']) or $item['arCategory'] = $UrlWL->getCategoryById($shortcutCid);
            // Set vars
            $item['descr']        = unScreenData(@$item['descr']);
            $item['fulldescr']    = unScreenData(@$item['fulldescr']);
//            // item options
//            $item['options']         = array();//self::getProductOptions($item['id']);
//            $item['selectedOptions'] = array();//self::getSelectedOptions($item['options']);
            $item["idKey"]           = self::makeProductIdKey($item["id"]);
            // Recalc Item price by currency rate
            $item["price"]  = $item["price"] * $objSettingsInfo->eurRate;
            if($objSettingsInfo->pricePrecision >= 0)
                $item["price"] = round($item["price"], $objSettingsInfo->pricePrecision);
            // set price with discount
            $item['new_price'] = 0;
            // get brand
            $item['brand'] = $inList ? array() : getSimpleItemRow($item['brand_id'], BRANDS_TABLE);
            // get series
            $item['series'] = $inList ? array() : getSimpleItemRow($item['series_id'], SERIES_TABLE);
            // get type
            $item['substrate'] = $inList ? array() : getSimpleItemRow($item['substrate_id'], SUBSTRATES_TABLE);
            // get item attribute groups
            self::getProductAttributes($item, false, true, $item['arCategory']["id"], $inList);
            // get images
            self::getProductImages($images_path, $item, $inList, 'image', $arAliases);
            // get colors
            $item["colors"] = self::getProductColors($item, $images_path, $inList);
            // get sizes
            $item["sizes"]  = self::getProductSizes($item["id"], $inList);
        } return $item;
    }
    /**
     * @example PHPHelper::getProductColors()
     * @param array $item
     * @param String $images_path
     * @param boolean $inList
     * @return array
     */
    public static function getProductColors($item, $images_path, $inList=true) {
        $colors = array();
        $query  = "SELECT t.`id`, ct.`title`, t.`title` AS `product_title`, t.`seo_path`, ct.`id` AS `color_id`, "
                . "ct.`hex`, cf.`filename` AS `src`, t.`id` AS `product_id`, "
                . "IF(t.`id` = '{$item["id"]}', 1, 0) AS `checked` "
                . "FROM `".CATALOG_TABLE."` t "
                . "INNER JOIN `".COLORS_TABLE."` ct ON(ct.`id` = t.`color_id`) "
                . "LEFT JOIN `".CATALOGFILES_TABLE."` cf ON(cf.`pid` = t.`id` AND cf.`isdefault`=1) "
                . "WHERE t.`active`>0 AND t.`price`>0 AND t.`model_id`={$item["model_id"]} "
                . "ORDER BY `checked` DESC, ct.`order`";
        $result = mysql_query($query);
        if ($result and mysql_num_rows($result)) {
            while ($color = mysql_fetch_assoc($result)) {
                $item_img_path     = $images_path.$color["id"].'/';
                $prepared_img_path = prepareDirPath($item_img_path);
                $img_prefix = $inList ? "middle_" : "";
                $color["image"] = (!empty($color["src"]) and file_exists($prepared_img_path.$img_prefix.$color["src"])) ? $item_img_path.$img_prefix.$color["src"] : $images_path.$img_prefix."noimage.jpg";
                $colors[] = array_merge($item, $color);
            }
        } return $colors;
    }
    /**
     * @example PHPHelper::getProductSizes()
     * @param int $itemID
     * @return array
     */
    public static function getProductSizes($itemID, $inList = true) {
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
     * @example PHPHelper::getProductImages()
     * @param String $img_path
     * @param array $item
     * @param boolean $list
     * @param string $colname
     * @param array $arAliases
     */
    public static function getProductImages ($img_path, &$item, $list = false, $colname='image', $arAliases=array('')) {
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
    
    public static function makeProductIdKey ($id = 0, $options = array()) {
        global $Basket;
        $idKey = !empty($id) ? $id : "";
        return $idKey;
    }
    /**
     * @example PHPHelper::getProductAttributes()
     * @param array $item
     * @param boolean $showEmptyAttr
     * @param boolean $showAll
     * @param boolean $shortcutCid
     * @param boolean $inList
     */
    public static function getProductAttributes(&$item, $showEmptyAttr=false, $showAll = false, $shortcutCid=false, $inList=true){
        $cid = $shortcutCid ? $shortcutCid : $item["cid"];
        $files_url  = UPLOAD_URL_DIR."attributes/";
        $files_path = prepareDirPath($files_url);
        $item["attributes"] = array();
        if (!$inList) {
            $query  = "SELECT a.*, CONCAT(GROUP_CONCAT(DISTINCT pa.`value`), ',', 0) AS `vals`, "
                    . "IF(ca.`order` IS NULL, 0, ca.`order`) AS `itemorder`, "
                    . "IF(cag.`order` IS NULL, 0, cag.`order`) AS `grouporder` "
                    . "FROM `".ATTRIBUTES_TABLE."` a "
                    . "LEFT JOIN `".MODEL_ATTRIBUTES_TABLE."` pa ON(pa.`aid`=a.`id`) "
                    . "LEFT JOIN `".ATTRIBUTE_GROUPS_TABLE."` ag ON(ag.`id`=a.`gid`) "
                    . "LEFT JOIN `".CATEGORY_ATTRIBUTES_TABLE."` ca ON(ca.`aid`=a.`id` AND ca.`cid`='{$cid}') "
                    . "LEFT JOIN `".CATEGORY_ATTRIBUTE_GROUPS_TABLE."` cag ON(cag.`gid`=ag.`id` AND cag.`cid`='{$cid}') "
                    . "WHERE pa.`mid`={$item["model_id"]} ".(!$showAll ? "AND ca.`cid`='{$cid}' AND cag.`cid`='{$cid}' " : "")
                    . "GROUP BY a.`id` ".(!$showEmptyAttr ? "HAVING CHAR_LENGTH(vals)>0 " : "")
                    . "ORDER BY `grouporder`, `itemorder` ASC";
            $result = mysql_query($query);
            if ($result and mysql_num_rows($result)>0) {
                while ($row = mysql_fetch_assoc($result)) {
                    $row['values'] = getComplexRowItems(ATTRIBUTES_VALUES_TABLE, "id, title, title_single, title_multi, image", "WHERE `id` IN({$row['vals']})", "`order`");
                    for ($i=0; $i<count($row['values']); $i++) {
                        $row['values'][$i]["image"] = (!empty($row['values'][$i]["image"]) and file_exists($files_path.$row['values'][$i]["image"])) ? $files_url.$row['values'][$i]["image"] : "";
                    } $item['attributes'][$row['id']] = $row;
                }
            }
        }
    }
    /**
     * @param type $sortID
     * @return type
     */
    public static function getCatalogSorting(UrlWL $UrlWL, $sortID=0) {
        $columns = self::$SORT_COLUMNS;
        $_UrlWL = $UrlWL->copy()->resetPage();
        $_isDef = (!self::checkCatalogSort($sortID) || $sortID == self::getDefaultCatalogSort());
        foreach(array_keys($columns) as $key){
            $columns[$key]['active'] = ($sortID == $key);
            if($columns[$key]['active'] && $_isDef){
                $_UrlWL->unsetParam(UrlWL::SORT_KEY_NAME);
            } else {
                $_UrlWL->setParam(UrlWL::SORT_KEY_NAME, $key);
            }
            $columns[$key]['url'] = $_UrlWL->buildUrl();
        }
        unset($_UrlWL);
        return $columns;
    }
    /**
     * @param type $sortID
     * @return bool
     */
    public static function checkCatalogSort($sortID) {
        return ($sortID && array_key_exists($sortID, self::$SORT_COLUMNS));
    }
    /**
     * @param type $sortID
     * @return int
     */
    public static function getCorrectCatalogSort($sortID) {
        return self::checkCatalogSort($sortID) ? $sortID : self::getDefaultCatalogSort() ;
    }
    /**
     * @param type $sortID
     * @return type
     */
    public static function getDefaultCatalogSort() {
        $keys = array_keys(self::$SORT_COLUMNS);
        return reset($keys);
    }
    
    public static function getSliderItems() {
        static $items = array();
        if(empty($items)){
            $query  = "SELECT * FROM `".HOMESLIDER_TABLE."` WHERE `active`=1 AND `image`<>'' ORDER BY `order`, `id`";
            $result = mysql_query($query);
            while ($row = mysql_fetch_assoc($result)) {
                $row['path']  = UPLOAD_URL_DIR.'homeslider/';
                $row['title'] = unScreenData($row['title']);
                $items[] = $row;
            }
        } return $items;
    }
    
    public static function dataConv($item, $from = "windows-1251", $to = "utf-8", $translit = false, $bApplyTrim = false) {
        if (is_object($item) AND $item instanceof stdClass) {
            $item = (array) $item;
        }
        if (is_array($item)) {
            foreach ($item as $key => $value) {
                $item[$key] = self::dataConv($value, $from, $to, $translit, $bApplyTrim);
            }
        } else if (!is_bool($item) AND $item) {
            if ($bApplyTrim) $item = trim($item);
            if ($item)  $item = iconv($from, $to . ($translit ? "//TRANSLIT" : ''), $item);
        } return $item;
    }
    
    public static function mb_dataConv($item, $to = "CP1251", $from = 'UTF-8') {
        if (is_object($item) AND $item instanceof stdClass) {
            $item = (array) $item;
        }
        if (is_array($item)) {
            foreach ($item as $key => $value) {
                $item[$key] = self::mb_dataConv($value, $to, $from);
            }
        } else if (!is_bool($item) AND $item) {
            if ($item)  $item = mb_convert_encoding($item, $to, $from);
        } return $item;
    }
    /**
     * @example PHPHelper::prepareSearchText()
     * @param type $stext
     * @return string
     */
    public static function prepareSearchText ($stext, $addslashes = true) {
        $stext = urldecode($stext);
        $stext = trim(strip_tags($stext)); // add from xss attack
        if($addslashes) $stext = addslashes($stext);
        return $stext;
    }
    
    /**
     * This function emulates php internal function basename
     * but does not behave badly on broken locale settings
     * @param string $path
     * @param string $ext
     * @return string
     */
    function BaseName($path, $ext="") {
        $path = rtrim($path, "\\/");
        if (preg_match("#[^\\\\/]+$#", $path, $match))
            $path = $match[0];
        if ($ext) {
            $ext_len = strlen($ext);
            if (strlen($path) > $ext_len AND mb_substr($path, -$ext_len) == $ext)
                $path = mb_substr($path, 0, -$ext_len);
        } return $path;
    }

    function StrRPos($haystack, $needle) {
        $idx = strpos(strrev($haystack), strrev($needle));
        if($idx === false) return false;
        $idx = strlen($haystack) - strlen($needle) - $idx;
        return $idx;
    }
    
    public static function BuildFilterMetaData ($meta, $filters) {
        foreach ($filters as $key=>$title) {
            $replace = sprintf(self::$meta_template, $key);
            $meta = str_replace($replace, $title, $meta);
        }
        $meta = preg_replace("/".sprintf(self::$meta_template, "\d+")."/", "", $meta);
        return trim(str_replace("  ", " ", $meta));
    }

    public static function BuildProductMetaData($metaTemplate, $item) {
        return ProductMetaHelper::generate($metaTemplate, $item);
    }
    
    public static function removeScriptHost ($url) {
        return preg_replace("/^https?\:\/{2}{$_SERVER["HTTP_HOST"]}/", "", trim($url));
    }

    public static function generatePageScripts (&$scripts, $filename = "common.js") {
        if (file_exists(YUI_TMP_PATH.DS.$filename)) {
            $filename = ltrim(cleanDirPath(YUI_TMP_PATH.DS.$filename, "/"), "/");
            $version  = self::getCurrentScriptVersion($filename);
            $strlen   = mb_strlen($filename);
            $filename = str_pad($filename, $strlen+1, "/", STR_PAD_LEFT)."?$version";
            $scripts  = [$filename];
            return;
        } else {
            $js = [];
            foreach ($scripts as $script) {
    //            $yui = new YUICompressor(YUI_JAR_PATH, YUI_TMP_PATH);
                $prefix = !preg_match("/^http/", $script) ? $_SERVER["DOCUMENT_ROOT"] : "";
    //            $yui->addFile($prefix.$script);
                $js[] = file_get_contents($prefix.$script);
    //            $js[] = $yui->compress();
    //            unset($yui);
            }
            $fh = fopen(YUI_TMP_PATH.DS.$filename, 'w') or die("Can't create new file");
            fwrite($fh, implode("\n\r\n\r", $js));
            fclose($fh);
            if (file_exists(YUI_TMP_PATH.DS.$filename)) {
                $filename = ltrim(cleanDirPath(YUI_TMP_PATH.DS.$filename, "/"), "/");
                $version  = self::getCurrentScriptVersion($filename);
                $strlen   = mb_strlen($filename);
                $filename = str_pad($filename, $strlen+1, "/", STR_PAD_LEFT)."?$version";
                $scripts  = [$filename];
            } return;
        }
    }
    
    public static function getCurrentScriptVersion($script){
        $scriptPath = ltrim($script, "/");
        if (file_exists($scriptPath)) {
            $time = filemtime($scriptPath);
            return $time;
        } return false;
    }

    public static function getContactPhones($phones){
        if (empty($phones)) return array();
        $phones = array_map("trim", explode(",", $phones));
        foreach ($phones as $i=>$phone) {
            $phones[$i] = array(
                "tel" => self::getCorrectPhoneFormat($phone),
                "num" => $phone
            );
        } return $phones;
    }
    
    public static function getCorrectPhoneFormat($phone) {
        $arPhone = self::getExplodedAttribute($phone, ",");
        if (!empty($arPhone) and is_array($arPhone) and !empty($arPhone[0])) {
            $phone = preg_replace("/[\)\(\+\-\s\/\\\|\_A-zА-яЁё\@\*\!\.\,]/", "", $arPhone[0]);
            $phone = preg_replace("/^(\+)?(3)?8/", "", $phone);
            $phone = str_pad($phone, 13, "+380000000000", STR_PAD_LEFT);
            $phone = substr($phone, 0, 13);
            if ($phone=="+380000000000") $phone = "";
        } else $phone = "";
        return $phone;
    }
    
    private static function getExplodedAttribute($string, $delimiter = " ") {
        $attribute = explode($delimiter, $string);
        if (!empty($attribute) and is_array($attribute)) {
            foreach ($attribute as $key => $value) {
                $value = preg_replace("/\s{2,}/", "", $value);
                $value = trim($value);
                if (preg_match("/\|/", $value)) $attribute = array_merge($attribute, self::getExplodedAttribute($value, "|"));
                else $attribute[$key] = $value;
            }
        } return $attribute;
    }

    public static function shortenColorTitle($title = "", $max = 10, $pre = 5, $pad = 2, $sep = "-") {
        if (mb_strlen($title, WLCMS_SYSTEM_ENCODING) > $max) {
            $sep = str_repeat($sep, $max - ($pre + $pad));
            $pad = mb_substr($title, -$pad, $pad, WLCMS_SYSTEM_ENCODING);
            $pre = mb_substr($title, 0, $pre, WLCMS_SYSTEM_ENCODING);
            $title = $pre.$sep.$pad;
        } return $title;
    }
        
    public static function clearePhone($phone) {
        return str_replace(array('+38', ' ', '(', '-', ')', '_', '+'), '', trim($phone));
    }
    
    public static function createLogin($phone, $prefix = 'user_') {
        return $prefix.$phone;
    }
    
    public static function prepareDateFilter(&$filters, $alias, $column, &$url, &$where, $preset = false) {
        if($preset && !isset($filters[$column])) {
            $filters[$column]['from'] = date('01.m.Y 00:00:00'); // first day month
            $filters[$column]['to'] = '';
        }
        if(!empty($filters[$column]) && (!empty($filters[$column]['from']) || !empty($filters[$column]['to']))){
            $from = !empty($filters[$column]['from']) ? date("Y-m-d", strtotime($filters[$column]['from'])).' 00:00:00' : '';
            $to = !empty($filters[$column]['to']) ? date("Y-m-d", strtotime($filters[$column]['to'])).' 23:59:59' : '';            
            if($from == $to) $to = '';
            if($from || $to) {
                if($from && $to) {                    
                    $where .= ($where ? ' AND ' : ' ').' ('.$alias.'.`'.$column.'` BETWEEN "'.$from.'" AND  "'.$to.'") ';
                    $url.= '&filters['.$column.'][from]='.$filters[$column]['from'];
                    $url.= '&filters['.$column.'][to]='.$filters[$column]['to'];
                } else if ($from) {
                    $where .= ($where ? ' AND ' : ' '). ' '.$alias.'.`'.$column.'` >= "'.$from.'" ';
                    $url.= '&filters['.$column.'][from]='.$filters[$column]['from'];
                    unset($filters[$column]['to']);
                } else if ($to) {
                    $where .= ($where ? ' AND ' : ' '). ' '.$alias.'.`'.$column.'` <= "'.$to.'" ';
                    $url.= '&filters['.$column.'][to]='.$filters[$column]['to'];
                    unset($filters[$column]['from']);
                }
            }
        }         
    }
};