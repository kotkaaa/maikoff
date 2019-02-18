<?php

/**
 * WEBlife CMS
 * Created on 05.01.2011, 12:20:17
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access


require_once 'product/PrintProduct.php';
require_once 'product/CatalogProduct.php';

if (!defined('BASKET_TABLE')) define('BASKET_TABLE', 'basket'); 

/**
 * Description of Basket class
  Description: This class provides methods for add, remove, update and remove all items
  from a basket. The basket works with a session cookie, saving the product ID
  and quantity in an array for simple accesing it.
  There's an example in this category... called "Basket example using basket class"
 * @author WebLife
 * @copyright 2011
 */
class Basket {
    
    public $kitPrefix         = PRODUCT_KIT_PREFIX;
    public $kitIndicator      = ':';
    public $kitSeparator      = ':';
    public $kitOperator       = ' + ';
    public $optionsIndicator  = "|";
    public $optionsSeparator  = "/";
    public $valueSeparator    = "=";
    public $valueIterator     = ",";
    public $debug             = false;
    private $items            = array();
    private $files            = array();
    private $shipping_types   = array();
    private $payment_types   = array();
    private $products_count   = 0;
    private $files_count      = 0;
    private $files_max_count  = 3;
    private $amount           = 0;
    private $price            = 0.0;
    private $shipping_price   = 0.0;
    private $total_price      = 0.0;
    private $customerID       = 0;
    private $shippingID       = 0;
    private $paymentID        = 0;
    private $orderid          = 0;
    private $basketName       = 'basket';
    private $shippingName     = 'basket_shipping';
    private $paymentName      = 'basket_payment';
    private $cookieName       = 'ckBasket';
    private $cookieExpire     = 86400; // One day In seconds
    private $itemsExpire      = 3600; // In seconds (60 min * 60 sec)
    private $filesExpire      = 86400; // In seconds (60 min * 60 sec)
    private $bIsEmpty         = true;
    private $bSaveCookie      = true;
    private $bSaveDataBase    = true;
    private $basketItemsName  = 'basket_items';
    private $basketFilesName  = 'basket_files';
    private $basketOrderIdName= 'basket_order_id';
    private $basketExpireName = 'basket_items_expire';
    private $files_path;
    private $files_url;

    /**
     * Basket::__construct()
     *
     * Construct function.
     * @return
     */
    public function __construct($customer_id=0, $save_to_cookie=true, $basket_name='', $cookie_name='') {
        $this->customerID       = intval($customer_id);
        $this->bSaveDataBase    = $this->customerID>0 ? true : false;
        $this->bSaveCookie      = $save_to_cookie;
        if (!empty($basketName))
            $this->basketName   = $basket_name;
        if (!empty($cookie_name))
            $this->cookieName   = $cookie_name;
        $this->shippingName     = $this->basketName.'_shipping';
        $this->basketItemsName  = $this->basketName.'_items';
        $this->basketFilesName  = $this->basketName.'_files';
        $this->basketExpireName = $this->basketItemsName.'_expire';
        $this->files_url        = UPLOAD_TEMP_FILES_URL . "basket/";
        $this->files_path       = prepareDirPath($this->files_url, TRUE);
        $this->setShippingTypes();
        $this->setPaymentTypes();
        $this->init();
    }

    /**
     * Basket::__destruct()
     *
     * Destruct function. Set to session basket items.
     * @return
     */
    public function __destruct() {
        if ((time()-$_SESSION[$this->basketExpireName]) > $this->itemsExpire && isset($_SESSION[$this->basketItemsName])) {
            unset($_SESSION[$this->basketItemsName], $_SESSION[$this->basketExpireName]);
        } elseif( $this->amount ) {
            $_SESSION[$this->basketItemsName] = $this->items;
        }
        if ((time()-$_SESSION[$this->basketExpireName]) > $this->filesExpire && isset($_SESSION[$this->basketFilesName])) {
            unset($_SESSION[$this->basketFilesName], $_SESSION[$this->basketExpireName]);
        }
        $_SESSION[$this->basketOrderIdName] = $this->orderid;
        if($this->debug) {
            echo '<br/>'.$this->basketItemsName.' = ';  @print_r($_SESSION[$this->basketItemsName]);
            echo '<br/>'.$this->basketFilesName.' = ';  @print_r($_SESSION[$this->basketFilesName]);
            echo '<br/>'.$this->basketExpireName.' = '; @print_r($_SESSION[$this->basketExpireName]);
            echo '<br/>'.$this->basketOrderIdName.' = '; @print_r($_SESSION[$this->basketOrderIdName]);
        }
    }

    public function getName() {
        return $this->basketName;
    }

    public function getCookieName() {
        return $this->cookieName;
    }

    public function setCookieExpire($seconds) {
        if(is_int($expire)) $this->cookieExpire = $seconds;
    }

    public function getCookieExpire() {
        return $this->cookieExpire;
    }

    public function setSaveCookie($bool = true) {
        $this->bSaveCookie = $bool;
    }

    public function getSaveCookie() {
        return $this->bSaveCookie;
    }

    public function setItemsExpire($seconds) {
        if(is_int($expire)) $this->itemsExpire = $seconds;
    }

    public function getItemsExpire() {
        return $this->itemsExpire;
    }

    public function setSaveDataBase($customer_id = 0) {
        $this->customerID    = intval($customer_id);
        $this->bSaveDataBase = $this->customerID>0 ? true : false;
        $this->init();
    }

    public function getSaveDataBase() {
        return $this->bSaveDataBase;
    }

    public function isEmptyBasket() {
        return $this->bIsEmpty;
    }

    public function getTotalPrice($include_shipping = false) {
        return ($include_shipping ? $this->total_price : $this->price);
    }

    public function getShippingPrice() {
        return $this->shipping_price;
    }

    public function getTotalAmount() {
        return $this->amount;
    }

    public function getProductsCount() {
        return $this->products_count;
    }
    
    public function getItems() {
        return $this->items;
    }

    public function getFiles() {
        return $this->files;
    }

    public function getOrderId() {
        return $this->orderid;
    }

    public function setOrderId($orderid) {
        $this->orderid = intval($orderid);
    }
    
    public function setupKitParams($kitPrefix='Kit') {
        $this->kitPrefix = $kitPrefix;
    }

    public function getFilesUrl(){
        return $this->files_url;
    }

    public function getFilesPath(){
        return $this->files_path;
    }

    public function getFilesCount(){
        return $this->files_count;
    }

    /**
     * Basket::get()
     *
     * Returns the basket session as an array of item => qty
     * @return array
     */
    public function get() {
        return isset($_SESSION[$this->basketName]) ? $_SESSION[$this->basketName] : array();
    }
    
    /**
     * Basket::get()
     *
     * Returns the basket session as an array of item => qty
     * @return array
     */
    public function files() {
        return isset($_SESSION[$this->basketFilesName]) ? $_SESSION[$this->basketFilesName] : array();
    }

    /**
     * Basket::add()
     *
     * Adds item to basket. If $id already exists in array then qty updated
     * @param mixed $id - ID of item
     * @param integer $qty - Qty of items to be added to cart
     * @return bool
     */
    public function add($id, $qty = 1, $setNewQty=0, $options = array()) {
        if (empty($id)) return -1;
        if (isset($_SESSION[$this->basketName][$id]) && !$setNewQty) {
             $_SESSION[$this->basketName][$id] += $qty;
        } else {
            $_SESSION[$this->basketName][$id] = $qty;
        }
        $this->setOrderId(0);
        $this->recalc();
        $this->SetCookie();
        $this->SetDB();
    }

    public function addFile($fileName) {
        if (file_exists($this->files_path.$fileName)) {
            $fileID = md5($fileName);
            $ext = getFileExt($fileName);
            $baseName = basename($fileName, $ext);
            $_SESSION[$this->basketFilesName][$fileID] = array(
                "name"  => $fileName,
                "title" => PHPHelper::shortenColorTitle($baseName, 15, 10, 2, ".").$ext
            );
        }
    }

    /**
     * Basket::deleteFile()
     *
     * Completely removes item from basket
     * @param mixed $id
     * @return bool
     */
    public function deleteFile($fileID) {
        if (isset($_SESSION[$this->basketFilesName][$fileID])) unset($_SESSION[$this->basketFilesName][$fileID]);
    }

    /**
     * Basket::remove()
     *
     * Removes item from basket. If final qty less than 1 then item deleted.
     * @param mixed $id - Id of item
     * @param integer $qty - Qty of items to be removed to cart
     * @see delete()
     * @return bool
     */
    public function remove($id, $qty = 1) {
        if (isset($_SESSION[$this->basketName][$id])) {
            $_SESSION[$this->basketName][$id] = $qty ? $_SESSION[$this->basketName][$id] - $qty : 0;
            if ($_SESSION[$this->basketName][$id] <= 0) $this->delete($id);
            $this->recalc();
            $this->SetCookie();
            $this->SetDB();
        }
    }

    /**
     * Basket::qty()
     *
     * Get item stored qty
     * @param mixed $id - ID of item
     * @return int
     */
    public function qty($id) {
        return isset($_SESSION[$this->basketName][$id]) ? $_SESSION[$this->basketName][$id] : 0;
    }

    /**
     * Basket::updateItem()
     *
     * Updates a basket item with a specific qty
     * @param mixed $id - ID of item
     * @param mixed $qty - Qty of items in basket
     * @return bool
     */
    public function updateItem($id, $qty) {
        $qty = intval($qty);
        if (isset($_SESSION[$this->basketName][$id])) {
        $_SESSION[$this->basketName][$id] = $qty;
        if ($_SESSION[$this->basketName][$id] <= 0)
            $this->delete($id);
            $this->recalc();
            $this->SetCookie();
            $this->SetDB();
            return true;
        } return false;
    }

    /**
     * Basket::update()
     *
     * Updates a basket items
     * @param array $items Contains the array( array($id, $qty), ... )
     * @return bool
     */
    public function update(array $items) {
        if (sizeof($items)>0) {
            foreach($items as $id=>$qty){
                if (isset($_SESSION[$this->basketName][$id])) {
                    $_SESSION[$this->basketName][$id] = $qty;
                    if ($_SESSION[$this->basketName][$id] <= 0) $this->delete($id);
                }
            }
            $this->recalc();
            $this->SetCookie();
            $this->SetDB();
            return true;
        } return false;
    }

    /**
     * Basket::dropBasket()
     *
     * Completely removes the basket from session
     */
    public function dropBasket() {
        if (isset($_SESSION[$this->basketFilesName])) unset($_SESSION[$this->basketFilesName]);
        if (isset($_SESSION[$this->basketName])){
            unset($_SESSION[$this->basketName]);
            $this->recalc();
            $this->SetCookie();
            $this->SetDB();
        }
    }

    /**
     * Basket::isSetKey()
     *
     * Check if key exist in Basket
     * @param mixed $id
     * @param mixed $var_type  - Set if you want to determinate how check BASKET $id variable type
     * @return bool
     */
    public function isSetKey($key, $var_type=false) {
        if (strlen($key)>0 && !empty($_SESSION[$this->basketName])) {
            if($var_type===false) {
                return array_key_exists($key, $_SESSION[$this->basketName]);
            } else {
                foreach ($_SESSION[$this->basketName] as $id=>$qty) {
                    switch($var_type){
                        case 'intval':
                        case 'strval':
                        case 'floatval':
                        case 'doubleval':   if($var_type($id)===$key) return true; break;
                        case 'integer':     if((integer)$id===$key)   return true; break;
                        case 'int':         if((int)$id===$key)       return true; break;
                        case 'string':      if((string)$id===$key)    return true; break;
                        case 'float':       if((float)$id===$key)     return true; break;
                        case 'double':      if((double)$id===$key)    return true; break;
                        case 'boolean':     if((boolean)$id===$key)   return true; break;
                        case 'bool':        if((bool)$id===$key)      return true; break;
                        default:            if($id===$key)            return true; break;
                    }
                }                
            }
        } return false;
    }


    /**
     * Basket::init()
     *
     * Init function. Parses cookie if set and Set to session basket items.
     * @return
     */
    private function init() {
        if (!isset($_SESSION[$this->basketName]) && (isset($_COOKIE[$this->cookieName])))
            $_SESSION[$this->basketName] = unserialize(base64_decode($_COOKIE[$this->cookieName]));
        if (empty($_SESSION[$this->basketName]) && $this->bSaveDataBase)
            $_SESSION[$this->basketName] = $this->getDB();
        if (!isset($_SESSION[$this->basketName]))
            $_SESSION[$this->basketName] = array();
        if (!isset($_SESSION[$this->basketFilesName]))
            $_SESSION[$this->basketFilesName] = array();
        if (!empty($_SESSION[$this->basketItemsName]))
            $this->items = $_SESSION[$this->basketItemsName];
        if (!empty($_SESSION[$this->basketFilesName]))
            $this->files = $_SESSION[$this->basketFilesName];
        if (!isset($_SESSION[$this->basketExpireName]))
            $_SESSION[$this->basketExpireName] = time();
        if (!isset($_SESSION[$this->shippingName]))
            $_SESSION[$this->shippingName] = 1;
        if (!empty($_SESSION[$this->shippingName]))
            $this->shippingID = $_SESSION[$this->shippingName];
        if (!isset($_SESSION[$this->paymentName]))
            $_SESSION[$this->paymentName] = 1;
        if (!empty($_SESSION[$this->paymentName]))
            $this->paymentID = $_SESSION[$this->paymentName];
        $this->setOrderId(isset($_SESSION[$this->basketOrderIdName]) ? $_SESSION[$this->basketOrderIdName] : 0);
        $this->recalc();
    }

    /**
     * Basket::recalc()
     *
     * Returns the total amount of items in the basket
     * @return int quantity of items in basket
     */
    public function recalc() {
        $quantity    = 0;
        $products_count = 0;
        $total_price = (float)0;
        $items       = array();
        if (!empty($_SESSION[$this->basketName])) {
            foreach ($_SESSION[$this->basketName] as $id=>$qty) {
                if (array_key_exists($id, $this->items)) $items[$id] = $this->items[$id];
                else $items[$id] = self::getItemRow($id);
                if($items[$id]===false){
                    unset($items[$id]);
                    $this->delete($id);
                    $this->SetCookie();
                    $this->SetDB();
                    continue;
                }
                $price = isset($items[$id]['price']) ? (float)$items[$id]['price'] : 0;
                $items[$id]['price']    = $price;
                $items[$id]['amount']   = $price*$qty;
                $items[$id]['quantity'] = $qty;
                $total_price += $items[$id]['amount'];
                $quantity    += $qty;
                if (!empty($items[$id]['arKits'])) $products_count += $qty*2; // must be count($items[$id]['arKits']) but...
                else $products_count += $qty;
            }
        }
        $this->items    = $items;
        $this->price    = $total_price;
        $this->amount   = $quantity;
        $this->products_count = $products_count;
        $this->bIsEmpty = !($this->amount>0);
        $this->recalcShipping();
        $this->recalcFiles();
    }

    private function recalcShipping(){
        if (!empty($this->shipping_types)) {
            foreach ($this->shipping_types as $type) {
                if ($type["id"] == $this->shippingID) {
                    $this->shipping_price = ($this->amount >= 4 ? 0 : ($type["price"] * 1));
                }
            }
        } $this->total_price = $this->price + $this->shipping_price;
    }

    /**
     * @todo на будущее можно убрать проверку наличия файла на сервере и сделать подсветку красным файла в корзине (если он удален на сервере)
     */
    public function recalcFiles(){
        if (!empty($_SESSION[$this->basketFilesName])) {
            foreach ($_SESSION[$this->basketFilesName] as $fileID=>$file) {
                $unset = false;
                if (!file_exists($this->files_path.$file["name"])) $unset = true;
                else $this->files_count++;
                if ($this->files_count > $this->files_max_count) $unset = true;
                if ($unset) {
                    unset($_SESSION[$this->basketFilesName][$fileID]);
                    unlinkFile($file["name"], $this->files_path);
                }
            }
        } $this->files = isset($_SESSION[$this->basketFilesName]) ? $_SESSION[$this->basketFilesName] : array();
    }

    /**
     * Basket::delete()
     *
     * Completely removes item from basket
     * @param mixed $id
     * @return bool
     */
    private function delete($id) {
        if(isset($_SESSION[$this->basketName][$id])) {
            unset($_SESSION[$this->basketName][$id]);
        }
    }

    /**
     * Basket::SetCookie()
     *
     * Creates cookie of basket items
     * @return bool
     */
    private function SetCookie() {
        if ($this->bSaveCookie) {
            $basket = @$_SESSION[$this->basketName];
            $string = base64_encode(serialize($basket));
            $expire = time() + ($basket ? $this->cookieExpire : -$this->cookieExpire);
            setcookie($this->cookieName, $string, $expire, '/');
            return true;
        } return false;
    }

    /**
     * Basket::getDB()
     *
     * get Variables from DataBase
     * @return array
     */
    private function getDB() {
        global $DB;
        $basket = array();
        if ($this->bSaveDataBase) {
            $DB->Query("SELECT * FROM `".BASKET_TABLE."` WHERE `uid`=".$this->customerID);
            while($item = $DB->fetchAssoc()){
                $basket[$item['code']] = $item['quantity'];
            }
        } return $basket;
    }

    /**
     * Basket::SetDB()
     *
     * Creates DB Rows of basket items
     * @return bool
     */
    private function SetDB() {
        global $DB;
        if ($this->bSaveDataBase) {
            $DB->Query("DELETE FROM `".BASKET_TABLE."` WHERE `uid`=".$this->customerID);
            $DB->Query("OPTIMIZE TABLE `".BASKET_TABLE);
            if(!empty($_SESSION[$this->basketName])){
                foreach($_SESSION[$this->basketName] as $code=>$qty){
                    $arFields = array(
                        'uid'   => $this->customerID,
                        'code'     => $code,
                        'quantity' => $qty
                    ); $DB->postToDB($arFields, BASKET_TABLE);
                }
            } return true;
        } return false;
    }

    /**
     * Basket::getItemRow()
     *
     * get Item Row For basket From DB : array
     */
    public function getItemRow ($id) {
        global $DB, $objSettingsInfo;
        $item = false;
        if (strlen($id)>0) {
            // определение 
            $idx  = $this->separateIdKey($id);
            // формирование родительского элемента комплекта
            $data = array_shift($idx);
            if($data) {
                if(isset($data['params']['module'])) {
                    if($data['params']['module'] == CatalogProduct::getModule()) {
                        isset($objSettingsInfo) OR $objSettingsInfo = getSettings();
                        $query  = 'SELECT m.*, p.*, p.`id` `product_id`, b.`title` `brand_title`, IF(s.`title` IS NULL, "", s.`title`) `series_title`, '. PHP_EOL
                                . 'c.`title` `color_title`,  c.`hex` `color_hex` FROM `'.CATALOG_TABLE.'` p'. PHP_EOL
                                . 'INNER JOIN `'.MODELS_TABLE.'` m ON (m.`id` = p.`model_id`)' . PHP_EOL
                                . 'INNER JOIN `'.COLORS_TABLE.'` c ON(c.`id`=p.`color_id`)' . PHP_EOL
                                . 'LEFT JOIN `'.BRANDS_TABLE.'` b ON(b.`id`=m.`brand_id`)' . PHP_EOL
                                . 'LEFT JOIN `'.SERIES_TABLE.'` s ON(s.`id`=m.`series_id`)' . PHP_EOL
                                . 'WHERE p.`id`='.$data['id'].' LIMIT 1' . PHP_EOL;
                        if ($DB->Query($query) && ($item = $DB->fetchAssoc())) {
                            $item = array_merge($item, $data['params']);
                            $item["price"]      = CatalogProduct::getItemPrice($item);
                            $item["arCategory"] = UrlWL::getCategoryByIdWithSeoPath(UrlWL::CATALOG_CATID);
                            CatalogProduct::getItemImages(CatalogProduct::getImagesUrl()."/", $item, true, 'image', array(array('small_')));
                        }
                    } elseif ($data['params']['module'] == PrintProduct::getModule()) {
                        $query  = 'SELECT p.*, a.*, CONCAT(st.`title`, " ", p.`title`) `title`, pc.`id` `product_id`, pc.`assortment_id`, st.`title` `substrate_title`, c.`title` `color_title`, c.`hex` `color_hex`, ' . PHP_EOL
                                . 'ps.`file_id` `logo_id`, ps.`width` `logo_width`, ps.`offset` `logo_offset`, ' . PHP_EOL
                                . '"0" `brand_id`, "" `brand_title`, "0" `series_id`, "" `series_title` FROM `'.PRINT_ASSORTMENT_TABLE.'` a ' . PHP_EOL
                                . 'INNER JOIN `'.PRINTS_TABLE.'` p ON p.`id`=a.`print_id` ' . PHP_EOL
                                . 'INNER JOIN `'.PRINT_ASSORTMENT_COLORS_TABLE.'` pc ON(pc.`assortment_id`=a.`id` AND pc.`color_id`='.$data['params']['color_id'].') ' . PHP_EOL
                                . "INNER JOIN `".PRINT_ASSORTMENT_SETTINGS_TABLE."` ps ON(ps.`assortment_id`=a.`id` AND ps.`file_id`=pc.`file_id`) ".PHP_EOL
                                . 'INNER JOIN `'.SUBSTRATES_TABLE.'` st ON(st.`id`=a.`substrate_id`) '.PHP_EOL
                                . 'INNER JOIN `'.COLORS_TABLE.'` c ON(c.`id`=pc.`color_id`) '.PHP_EOL
                                . 'WHERE a.`id`='.$data['id'].' LIMIT 1';
                        if ($DB->Query($query) and ($item = $DB->fetchAssoc())) {
                            $item = array_merge($item, $data['params']);
                            $item['arCategory'] = UrlWL::getCategoryByIdWithSeoPath($item['category_id']);
                            PrintProduct::setItemImages($item);
                        }
                    }
                    if ($item and isset($item['size_id'])) {
                        $size = getSimpleItemRow($item['size_id'], SIZES_TABLE);
                        $item['size_title'] = $size["title"];
                        $item['size_cost']  = $size["cost"];
                        $item['price']     += $item['size_cost'];
                    }
                } else {
                    // родная версия cms
                    $files_url     = UPLOAD_URL_DIR.'catalog/';
                    $files_path    = prepareDirPath($files_url);
                    $query = 'SELECT c.* FROM `'.CATALOG_TABLE.'` c WHERE c.`id`='.$data['id'].' LIMIT 1';
                    $DB->Query($query);
                    $item = $DB->fetchAssoc();
                    if ($item) {
                        $images_url           = $files_url.$item['id'].'/';
                        $item['arCategory']   = UrlWL::getCategoryByIdWithSeoPath($item['cid']);
                        $item['image']        = getValueFromDB(CATALOGFILES_TABLE." t", 'filename', 'WHERE t.`pid`='.$item['id'].' AND t.`isdefault`=1' );
                        $item['middle_image'] = (!empty($item['image']) && is_file(prepareDirPath($images_url).'middle_'.$item['image'])) ? $images_url.'middle_'.$item['image'] : $files_url.'middle_noimage.jpg';
                        $item['brand']        = getItemRow(BRANDS_TABLE, '*', 'WHERE `id`='.$item['bid']);
                        $item["options"] = PHPHelper::getProductOptions($item['id'], $data['options']);
                        $item["selectedOptions"] = $data['options'];
                        $item["price"]   = PHPHelper::recalcItemPriceByOptions($item["price"],  $item["options"]);
                        $item["cprice"]  = PHPHelper::recalcItemPriceByOptions($item["cprice"], $item["options"]);
//                        $itemrow['old_price'] = ($itemrow['isdiscount'] && $itemrow['discount']) ? ($itemrow['price'] - ($itemrow['price']*$itemrow['discount']/100)) : $itemrow['price'];
                        $item['old_price'] =  $item['price'];
                        $item['price'] = ($item['isdiscount'] && $item['discount']) ? ($item['price'] - ($item['price']*$item['discount']/100)) : $item['price'];
                        $item['arKits'] = array();
                        $arTitle = array();
                        if ($idx) {
                            $item['kit_price'] = $item['price'];
                            foreach ($idx as $kititem) {
                                $query = 'SELECT c.* FROM `'.CATALOG_TABLE.'` c WHERE c.`id`='.$kititem["id"];
                                $DB->Query($query);
                                while ($row = $DB->fetchAssoc()) {
                                    $row["options"]      = PHPHelper::getProductOptions($row["id"], $kititem['options']);
                                    $row["selectedOptions"] = $kititem['options'];
                                    $row["price"]        = PHPHelper::recalcItemPriceByOptions($row["price"],  $row["options"]);
                                    $row["cprice"]       = PHPHelper::recalcItemPriceByOptions($row["cprice"], $row["options"]);
                                    $row['old_price']    = $row['price'];
                                    $row['price']        = ($row['cprice'] > 0) ? $row['cprice'] : $row['price'];   
                                    $row['arCategory']   = UrlWL::getCategoryByIdWithSeoPath($row['cid']);
                                    $images_url          = $files_url.$row['id'].'/';
                                    $row['image']        = getValueFromDB(CATALOGFILES_TABLE." t", 'filename', 'WHERE t.`pid`='.$row['id'].' AND t.`isdefault`=1' );
                                    $row['middle_image'] = (!empty($row['image']) && is_file(prepareDirPath($images_url).'middle_'.$row['image'])) ? $images_url.'middle_'.$row['image'] : $files_url.'middle_noimage.jpg';
                                    $row['brand']        = getItemRow(BRANDS_TABLE, '*', 'WHERE `id`='.$row['bid']);
                                    $item['arKits'][] = $row;
                                    $item['price']   += $row['cprice'];
                                    $arTitle[]           = $row['title'];
                                }
                            } 
                            $newTitle = $this->kitPrefix.' '.$item['title'].' '.$this->kitOperator.implode($this->kitOperator, $arTitle);
                            $item['set_title'] = $newTitle;
                        }
                    }
                }
            }
        } return $item;
    }
    
    public function recalcItemPriceByOptions ($price, $options) {
        if (!empty($options)) {
            foreach ($options as $optionID => $option) {
                foreach ($option["values"] as $valueID => $value) {
                    if ($value["operator"]=="+") {
                        $price += $value["price"];
                    } elseif ($value["operator"]=="-") {
                        $price -= $value["price"];
                    }
                }
            }
        } return $price;
    }
    
    public function separateIdKey ($idKey) {
        $idx  = array();
        if(strpos($idKey, $this->kitSeparator)!==false) {
            $parts = explode($this->kitSeparator, $idKey);
        } else {
            $parts = array($idKey);
        }
        foreach ($parts as $part) {
            // определяем опции
            $options = array();
            if (strpos($part, $this->optionsIndicator)!==false) {
                $arr = explode($this->optionsIndicator, $part);
                $part = $arr[0];
                if (count($arr) > 1 AND !empty($arr[1])) {
                    foreach (explode($this->optionsSeparator, $arr[1]) as $val) {
                        $arr2 = explode($this->valueSeparator, $val);
                        if (count($arr2) > 1) {
                            $options[$arr2[0]] = (strpos($arr2[1], $this->valueIterator) ? explode($this->valueIterator, $arr2[1]) : $arr2[1]);
                        }
                    }
                }
            }
            // определяем основные настройки
            $params = array();
            if(is_numeric($part)) {
                $partID = intval($part);
            } else if(($arr = CatalogProduct::parseItemIdKey($part))) {
                $partID = intval($arr[1]);
                $params = array('size_id' => intval($arr[2]), 'module' => $arr[0], 'idKey' => $part);
            } else if(($arr = PrintProduct::parseItemIdKey($part))) {
                $partID = intval($arr[1]);
                $params = array('color_id' => intval($arr[2]), 'size_id' => intval($arr[3]), 'module' => $arr[0], 'idKey' => $part);
            } else {
                $partID = $part;
            }
            // добавляем 
            $idx[] = array('id' => $partID, 'params' => $params, 'options' => $options);
        }
        return $idx;
    }
    
    /**
     * Basket::SetDB()
     *
     * Creates DB Rows of basket items
     * @return bool
     */
    private function setShippingTypes() {
        global $DB;
        $DB->Query("SELECT * FROM `".SHIPPING_TYPES_TABLE."` WHERE `active`>0");
        while ($row = $DB->fetchAssoc()) {
            $this->shipping_types[] = $row;
        }
    }

    public function getShippingTypes() {
        return $this->shipping_types;
    }

    public function getShippingID() {
        return $this->shippingID;
    }

    public function setShippingID($shippingID) {
        $_SESSION[$this->shippingName] = $shippingID;
    }
    
    /**
     * Basket::SetDB()
     *
     * Creates DB Rows of basket items
     * @return bool
     */
    private function setPaymentTypes() {
        global $DB;
        $DB->Query("SELECT * FROM `".PAYMENT_TYPES_TABLE."` WHERE `active`>0");
        while ($row = $DB->fetchAssoc()) {
            $this->payment_types[] = $row;
        }
    }

    public function getPaymentTypes() {
        return $this->payment_types;
    }

    public function getPaymentID() {
        return $this->paymentID;
    }

    public function setPaymentID($paymentID) {
        $_SESSION[$this->paymentName] = $paymentID;
    }
}

class Checkout {
    // Nova poshta API params
    const NP_API_URL = "https://api.novaposhta.ua/v2.0/xml/";
    const NP_API_KEY = "f062bc6a40c2b014512a5030ae2a0e09";
    /**
     * @example Checkout::np_getCities()
     * @param string $raw
     * @return array
     */
    public function np_getCities ($raw) {
        $items = array();
        if (!empty($raw)) {
            $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
                    <file>
                        <apiKey>".self::NP_API_KEY."</apiKey>
                        <modelName>Address</modelName>
                        <calledMethod>getCities</calledMethod>
                        <methodProperties>
                            <FindByString>{$raw}</FindByString>
                        </methodProperties>
                    </file>";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::NP_API_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, PHPHelper::dataConv($xml));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            if (!@curl_errno($ch)) {
                $objXML = simplexml_load_string($response);
                if (is_object($objXML)) {
                    $arrXML = (array)$objXML;
                    if ($arrXML["success"]) {
                        foreach ($arrXML["data"]->item as $item) {
                            $items[] = array(
                                "id"   => $item->CityID,
                                "ref"  => $item->Ref,
                                "name" => PHPHelper::dataConv($item->DescriptionRu, "utf-8", "windows-1251")
                            );
                        }
                    } //saveLogDebugFile($arrXML, "temp/cities.log");
                }
            } else exit (curl_error($ch));
        } return $items;
    }
    /**
     * @example Checkout::np_getWareHouses()
     * @param string $ref
     * @return array
     */
    public function np_getWareHouses ($ref = "", $name = "") {
        $items = array();
        if (!empty($ref) or !empty($name)) {
            $xml    = "<?xml version=\"1.0\" encoding=\"utf-8\"?>"
                    . "<file>"
                    . "<apiKey>".self::NP_API_KEY."</apiKey>"
                    . "<modelName>Address</modelName>"
                    . "<calledMethod>getWarehouses</calledMethod>"
                    . "<methodProperties>"
                    . (!empty($ref) ? "<CityRef>{$ref}</CityRef>" : !empty($name) ? "<CityName>{$name}</CityName>" : "")
                    . "</methodProperties>"
                    . "</file>";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::NP_API_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, PHPHelper::dataConv($xml));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            if (!@curl_errno($ch)) {
                $objXML = simplexml_load_string($response);
                if (is_object($objXML)) {
                    $arrXML = (array)$objXML;
                    if ($arrXML["success"]) {
                        foreach ($arrXML["data"]->item as $item) {
                            $items[] = array(
                                "city_ref" => $item->CityRef,
                                "ref"      => $item->Ref,
                                "name"     => PHPHelper::dataConv($item->DescriptionRu, "utf-8", "windows-1251")
                            );
                        }
                    } //saveLogDebugFile($arrXML, "temp/warehouses.log");
                }
            } else exit (curl_error($ch));
        } return $items;
    }
    /**
     *
     * @param type $orderid
     */
    public static function saveOrderID ($orderid) {
        $_SESSION['purchased_order_id'] = $orderid;
    }
    /**
     *
     * @param type $delete
     * @return int
     */
    public static function getOrderID ($delete = true) {
        $orderid= 0;
        if (isset($_SESSION['purchased_order_id'])) $orderid = intval($_SESSION['purchased_order_id']);
        if ($delete) unset($_SESSION['purchased_order_id']);
        return $orderid;
    }
}

/*
DROP TABLE IF EXISTS `basket`;
CREATE TABLE IF NOT EXISTS `basket` (
  `uid` int(11) unsigned NOT NULL,
  `code` tinytext NOT NULL,
  `quantity` int(11) UNSIGNED NOT NULL DEFAULT '0',
 KEY `idx_uid` (`uid`)
) ENGINE=MyISAM;
 */