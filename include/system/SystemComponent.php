<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

/**
 * Description of SystemComponent class
 * This class contains some System Variables of WebLife CMS
 * @author WebLife
 * @copyright 2010
 */
class SystemComponent {
    const WLCMS_INSTALLED = true;  //IF Your site work OK You must set this parameter to true. If you copy to another hosting - set to false for change folders/files permissions automaticaly
    private static function initDBSettings() { //Put your site DB Connection data here
        return getenv('IS_DEV') ? array(
            "dbhost"     => "localhost",
            "dbusername" => "root",
            "dbpassword" => "",
            "dbname"     => "maikoff_trunk"
        ) : array(
            "dbhost"     => "localhost",
            "dbusername" => "u_maikoffN", // u_maikoff | u_maikoffN
            "dbpassword" => "uA30U8aV",   // 7Vh3BCfc  | uA30U8aV
            "dbname"     => "maikoffN"    // maikoff   | maikoff
        );
    }
    private static function initAcceptLangs() { //Put your site languages here. Cooment some langs if not need them
        return array(   // First is default
            "ru"        => array("name"=>"Русский",    "title"=>"Русский",    "image"=>"ru.gif", "charset"=>"utf-8"),
//            "ua"        => array("name"=>"Українська", "title"=>"Українська", "image"=>"ua.gif", "charset"=>"utf-8"),
//            "en"        => array("name"=>"English",    "title"=>"English",    "image"=>"en.gif", "charset"=>"utf-8"),
        );
    }
    private static function initTestCookie() { //Put your Test Cookie values to array here
        return array(
            'name'  => 'WLCMS_CHECKED', //set unique name of test cookie
            'val'   => md5($_SERVER['REMOTE_ADDR']), //set unique value of test cookie from md5 of client
            'exp'   => time()+57533 // set time to will be expire test cookie
        );
    }
    public static function getDBSettings() {
        return self::initDBSettings();
    }
    public static function getAcceptLangs() {
        return self::initAcceptLangs();
    }
    public static function getAcceptLangsKeys() {
        return array_keys(self::initAcceptLangs());
    }
    public static function getDBName() {
        $arDBSettings = self::initDBSettings();
        return $arDBSettings["dbname"];
    }
    public static function getTestCookie() {
        return self::initTestCookie();
    }
    public static function checkTestCookie($arCookie){
        return (!empty($_COOKIE[$arCookie['name']]) && $_COOKIE[$arCookie['name']]==$arCookie['val']) ? true : false;
    }
    public static function setTestCookie($arCookie, $isAdd=true){
        $obj = new CCookie();
        if($isAdd) $obj->add($arCookie['name'], $arCookie['val'], $arCookie['exp']);
        else       $obj->del($arCookie['name']);
        $obj->process();
    }    
    
    //images params
    public static function getArImgAliases(){
        return array( 'big', 'middle', 'small', 'thumb' );
    }
    
    public static function prepareImagesParams($images_params) {
        $params = array();
        if(!empty($images_params)){
            $images_params = unserialize(unScreenData($images_params));
            foreach($images_params as $alias => $param) {
               $params[] = array($alias.'_', $param['width'], $param['height'], $alias);
            }
        } return $params;
    } 
        
    public static function getUserIP() {
        // ключи дл¤ проверки ip из $_SERVER
        $arIpKeys = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_PROXY_USER', 'REMOTE_ADDR');
        foreach($arIpKeys as $key) {
            if (isset($_SERVER[$key])) {     
                foreach (array_reverse(explode(',', $_SERVER[$key])) as $value) {
                    $value = trim($value);
                    if (preg_match('#^(\d{1,3}).(\d{1,3}).(\d{1,3}).(\d{1,3})$#', $value, $matches)) {
                        $value = $matches[1] . '.' . $matches[2] . '.' . $matches[3] . '.' . $matches[4];
                        if ($value != '...') {
                            return $value;
                        }
                    }
                }
            }
        }
        return '127.0.0.1';
    }
}

///////////////// OTHER PARAMETERS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
if(!defined('DS')) define('DS',         DIRECTORY_SEPARATOR); // set WebLife CMS DIRECTORY SEPARATOR
define('WLCMS_IS_DEV',                  getenv('IS_DEV')); // set WebLife CMS IS DEV server
define('WLCMS_ABS_ROOT',                rtrim($_SERVER['DOCUMENT_ROOT'], '/\\').DS); // set WebLife CMS absolute root DIR
define('WLCMS_TMP_DIR',                 WLCMS_ABS_ROOT.'temp'); // set WebLife CMS TEMP DIR
define('WLCMS_RUNTIME_DIR',             WLCMS_TMP_DIR.DS.'runtime'); // set WebLife CMS RUNTIME DIR
define('WLCMS_VERSION',                 'СMS v3.0'); // set WebLife CMS Version
define('WLCMS_JQUERY_VERSION',          '1.10.1'); // set jQuery Library Version http://jquery.com/download/
define('WLCMS_DB_ENCODING',             'utf8'); // DB encoding. This value can not be changed.
define('WLCMS_SYSTEM_ENCODING',         'UTF-8'); // File system encoding. This value can not be changed.
define('WLCMS_WRITABLE_CHMOD',          '0775'); // [ 0775 | 0777 ] SET Chmod to files and directories to can change them
define('WLCMS_ERROR_REPORTING',         E_ALL); // [ E_ALL | 0 ] SET WAY TO SHOW ERRORS
define('WLCMS_SMARTY_ERROR_REPORTING',  E_ALL); // [ E_ALL | 0 ] SET WAY TO SHOW ERRORS
define('WLCMS_DEBUG',                   0); // [1 - true | 0 - false ] SET Script Debug Status
define('WLCMS_SESSION_NAME',            'WLCMSSESSID'); // SET Weblife session name for Request array
define('WLCMS_SMARTY_DIR',              WLCMS_ABS_ROOT.'include'.DS.'smarty'.DS); // set WebLife CMS SMARTY absolute path DIR
define('WLCMS_USE_CACHE',               true); // [true|false ] use cache or not
define('WLCMS_USE_HTTPS',               (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')); // [ 0 | 1 ] SET Chmod to files and directories to can change them
define('WLCMS_HTTP_PROTOCOL',           "http".(WLCMS_USE_HTTPS ? "s" : "")); // [ 0 | 1 ] SET Chmod to files and directories to can change them
define('WLCMS_HTTP_PREFIX',             WLCMS_HTTP_PROTOCOL."://"); // [ 0 | 1 ] SET Chmod to files and directories to can change them
define('WLCMS_HTTP_HOST',               WLCMS_HTTP_PREFIX.$_SERVER["HTTP_HOST"]); // [ 0 | 1 ] SET Chmod to files and directories to can change them

define('MDATA_KNAME',                   'modulesData'); // SET string Modules key name for array data storage in SESSION
define('DBTABLE_LANG_SEP',              '_'); // SET separate char for use in DataBase Table Names

define('URL_INLUDES_THE_LANG',          0); // set 1 to include lang code to URL client browes or 0 to not
define('AUTODETECT_CLIENT_LANG',        0); // set 1 to auto detect client browes lang or 0 to not
define('LANG_LIVE_IN_COOKIE',           3600 * 24 * 7); // set live period for lang in cookie in seconds Like (3600 * 24 * 7) - for one week

define('URL_ENABLE_SUFFIX',             1); // set 1 to include seo suffix like .html to URL or 0 to not
define('URL_SEO_SUFFIX',                '.html'); // set String text suffix for append to Url

define('UPLOAD_DIR',                    'uploaded'); // set WebLife CMS UPLOAD DIR
define('UPLOAD_URL_DIR',                '/'.UPLOAD_DIR.'/'); // set WebLife CMS UPLOAD URL DIR
define('MAIN_CATEGORIES_DIR',           UPLOAD_DIR.DS.'main'); // set WebLife CMS MAIN CATEGORIES DIR FROM DB
define('MAIN_CATEGORIES_URL_DIR',       UPLOAD_URL_DIR.'main/'); // set WebLife CMS MAIN CATEGORIES URL DIR FROM DB
define('UPLOAD_TEMP_FILES_URL',         UPLOAD_URL_DIR.'temp/'); // set WebLife CMS TEMP_FILES URL DIR FROM DB
define('UPLOAD_ORDER_FILES_URL',        UPLOAD_URL_DIR.'orders/'); // set WebLife CMS ORDER_FILES URL DIR FROM DB
define('UPLOAD_MEDIA_FILES_URL',        UPLOAD_URL_DIR.'media/'); // set WebLife CMS ORDER_FILES URL DIR FROM DB

define('SESSION_INACTIVE',              30*60); // set timeout period in seconds
define('MAX_WRONG_PASSWORDS',           5); // set maximum inputed passwords by user per same time
define('WRONG_SUBMITS_TO_CAPTCHA',      2); // set maximum submited wrong auth data by user per same time to show captcha
define('BANNED_TIME',                   3*60); // set banned timeout period in seconds
define('IVALIDATOR_MAX_LENTH',          7); // Set count charachters for Ivalidator Check Code
define('CHECKWORD_SALT_LENTH',          7); // Set count charachters for User CheckWord Salt
define('CHECKW_CONFIRMCODE_DAYS_KEEP',  3); // Set count of days to keep checkword or ConfirmCode alive

define('ENABLE_TRACKING_ECOMMERCE',     true); // set ecommerce operations true|false
define('CATALOG_PRODUCT_PCODE_SEPARATOR',     '/'); // set ecommerce operations true|false

// Admin Area constans
define('TPL_BACKEND_FORSE_COMPILE',     0); // [ 0 | 1 ] set the forse compile smarty template
define('TPL_BACKEND_CHECK_COMPILE',     WLCMS_IS_DEV ? 1 : 0); // [ 0 | 1 ] set the check compile smarty template
define('TPL_BACKEND_CACHING',           0); // [ 0 | 1 ] set the caching smarty template is enabled
define('TPL_BACKEND_NAME',              'weblife'); // set name of smarty tpl folder

// Site Area constans
define('TPL_FRONTEND_FORSE_COMPILE',    0); // [ 0 | 1 ] set the forse compile smarty template
define('TPL_FRONTEND_CHECK_COMPILE',    WLCMS_IS_DEV ? 1 : 0); // [ 0 | 1 ] set the check compile smarty template
define('TPL_FRONTEND_CACHING',          0); // [ 0 | 1 ] set the caching smarty template is enabled
define('TPL_FRONTEND_NAME',             'smart'); // [ simple | simple_wide | smart ] set name of smarty tpl folder

// Users Types
define('USER_TYPE_ADMINISTRATOR',       'Administrator');
define('USER_TYPE_INDASTRIALIST',       'Industrialist');
define('USER_TYPE_MANAGER',             'Manager');
define('USER_TYPE_USER',                'User');
define('USER_TYPE_PUBLISHER',           'Publisher');

// Recaptcha
define('RECAPTCHA_SITE_KEY',            '6LfvN1EUAAAAAB-pUZ9jaS5DhlL4gK4ADrPaFvUa');
define('RECAPTCHA_SECRET',              '6LfvN1EUAAAAAJjVApglCJ878V08VpJSWm0tScjo');

// Yui compressor
define("YUI_JAR_VERSION",               "2.4.8");
define("YUI_JAR_PATH",                  $_SERVER["DOCUMENT_ROOT"].DS."include".DS."classes".DS."yuicompressor".DS."yuicompressor-".YUI_JAR_VERSION.".jar");
define("YUI_TMP_PATH",                  "js".DS."min");

///////////////// CHECK SYSTEM \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
require_once(WLCMS_ABS_ROOT.'include/sys_verifications.php');
