<?php

/**
 * WEBlife CMS
 * Created on 25.04.2012, 12:20:17
 * Developed by http://weblife.ua/
 */
defined('WEBlife') or die('Restricted access'); // no direct access


/**
 * Description of Url class
 * This class provides methods for create and manage SEO Url for simple accesing it.
 * @author WebLife
 * @copyright 2012
 */
class Url {
    
    const URL_SEPARATOR  = '-';
        
    protected $url       = '';
    protected $suffix    = '.html';
    protected $incSuffix = false;
    protected $anchor    = '';
    protected $arPath    = array();
    protected $arParams  = array();

    /**
     * Url::__construct()
     *
     * Object Construct function.
     * @return
     */
    public function __construct($link, $incSuffix, $suffix) {
        if($incSuffix) $this->enableSuffix($suffix);
        $this->url = trim($link);
        $arr = self::ParseUrl($this->url, $this->incSuffix, $this->suffix);
        $this->anchor   = $arr['anchor'];
        $this->arPath   = $arr['arPath'];
        $this->arParams = $arr['arParams'];
    }

    /**
     * Url::__destruct()
     *
     * Object Destruct function.
     * @return
     */
    public function __destruct() {
        ;
    }

    /**
     * UrlWL::getParams()
     *
     * Get Current Class Params function.
     * @return
     */
    public function getParams() {
        return $this->arParams;
    }

    /**
     * UrlWL::getAnchor()
     *
     * Get Current Class anchor function.
     * @return
     */
    public function getAnchor() {
        return $this->anchor;
    }

    /**
     * UrlWL::getUrl()
     *
     * Get Current Class Url function.
     * @return
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * UrlWL::setUrl()
     *
     * Set Class Url function.
     * @return
     */
    public function setUrl($url='') {
        $this->url = $url;
    }

    /**
     * UrlWL::setParam()
     *
     * Set param to Class arParam function.
     * @return
     */
    public function setParam($key, $value) {
        if(!empty($key)){
            $this->arParams[$key] = $value;
        }
    }

    /**
     * UrlWL::getParam()
     *
     * Get param from Class arParam.
     * @return mixed
     */
    public function getParam($key, $defval=null, $unset=false) {
        if(array_key_exists($key, $this->arParams)){
            $defval = $this->arParams[$key];
            if($unset){
                unset($this->arParams[$key]);
            }
        }
        return $defval;
    }

    /**
     * UrlWL::issetParam()
     *
     * Get bool result if $key isset in Class arParam.
     * @param mixed $key
     * @return bool
     */
    public function issetParam($key) {
        return array_key_exists($key, $this->arParams);
    }

    /**
     * UrlWL::emptyParam()
     *
     * Get bool result if $key empty in Class arParam.
     * @param mixed $key
     * @return bool
     */
    public function emptyParam($key) {
        return empty($this->arParams[$key]);
    }

    /**
     * UrlWL::unsetParam()
     *
     * Get bool result if $key empty in Class arParam.
     * @param mixed $key
     * @return bool
     */
    public function unsetParam($key) {
        if(array_key_exists($key, $this->arParams)){
            unset($this->arParams[$key]);
            return true;
        }
        return false;
    }

    /**
     * UrlWL::enableSuffix()
     *
     * Enable Url Suffix Like .html
     */
    public function enableSuffix($suffix) {
        $suffix = trim($suffix);
        if(!empty($suffix)){
            $this->suffix    = $suffix;
            $this->incSuffix = true;
        }
    }

    /**
     * UrlWL::disableSuffix()
     *
     * Disable Url Suffix Like .html
     */
    public function disableSuffix() {
        $this->incSuffix = false;
    }

    /**
     * UrlWL::getSuffix()
     *
     * Get Url Suffix Like .html If Enabled
     */
    public function getSuffix() {
        return $this->incSuffix ? $this->suffix : '';
    }

    /**
     * UrlWL::getPath()
     *
     * Get array of url path
     */
    public function getPath() {
        return $this->arPath;
    }

    /**
     * UrlWL::setPath()
     *
     * Set Path Array function.
     * @return $this
     */
    public function setPath( array $arPath = array() ) {
        $this->arPath = $arPath;
        return $this;
    }

    /**
     * UrlWL::setPath()
     *
     * Set Path Array function.
     * @return $this
     */
    public function addToPath( $seo_path ) {
        $seo_path = trim($seo_path);
        if($seo_path!='') $this->arPath[] = $seo_path;
        return $this;
    }

    /**
     * Url::buildUrl()
     *
     * Url Build function.
     * @return String
     */
    public function buildUrl($left_slashed=true, $right_slashed=false) {
        return self::requestToUrl($this->arPath, $this->incSuffix, $this->suffix, $this->arParams, $this->anchor, $left_slashed, $right_slashed);
    }

    /**
     * Url::requestToUrl()
     *
     * Url Build function.
     * @param array $arPath
     * @param bool $incSuffix
     * @param string $suffix
     * @param array $arParams
     * @param string $anchor
     * @param boolean $left_slashed
     * @param boolean $right_slashed
     * @return String
     */
    public static function requestToUrl(array $arPath, $incSuffix, $suffix, array $arParams, $anchor='', $left_slashed=true, $right_slashed=false) {
        if($incSuffix and $right_slashed) $right_slashed = false;
        $ishome = empty($arPath);
        $params = self::buildParams($arParams);
        $url  = ($ishome or $left_slashed) ? '/' : '';
        $url .= implode('/', $arPath);
        if($incSuffix) $url .= $suffix;
        if($right_slashed and !$ishome) $url .= '/';
        if($params!='') $url .= '?'.$params;
        if($anchor!='') $url .= '#'.$anchor;
        return $url;
    }

    /**
     * Url::ParseUrl()
     *
     * Url Parse function.
     * @return array
     */
    public static function ParseUrl($url='', $incSuffix=false, $suffix='') {
        $arr = array('arPath'=>array(), 'arParams'=>array(), 'anchor'=>'');
        if($url!=''){
            if(strpos($url, "#")!==false){
                $arr['anchor'] = end(explode('#', $url));
                $url = str_replace("#{$arr['anchor']}", '', $url);
            }
            $sep = strpos($url, "?");
            if ($sep!==false) {
                $path   = trim(mb_substr($url, 0, $sep));
                $params = (strlen($url)>$sep+1) ? trim(mb_substr($url, $sep+1)) : '';
                if($params!='') $arr['arParams'] = self::parseParams($params, $arr['arParams']);
            } else $path = trim($url);
            $ar = explode('/', $path);
            $cn = count($ar);
            for($i=0; $i<$cn; $i++){
                $ar[$i] = trim($ar[$i]);
                if($ar[$i]=='') continue;
                if($incSuffix and $i==$cn-1) $ar[$i] = str_replace($suffix, '', $ar[$i]);
                $arr['arPath'][] = $ar[$i];
            }
        } return $arr;
    }

    /**
     * Url::buildParams()
     *
     * Build Params String Url function.
     * @return String
     */
    public static function buildParams(array $arParams=array()) {
        $url = array();
        foreach($arParams as $k=>$v){
            if (is_array($v)) {
                $url[] = http_build_query(array($k=>$v));
            } else {
                $url[] = $k.($v!='' ? '='.$v : '');
            }
        } return (string)implode('&', $url);
    }

    /**
     * Url::parseParams()
     *
     * Parse Params Url String 
     * @param string $params
     * @param array $arr parsed variables are stored in this variable as array elements
     * @return array
     */
    public static function parseParams($params, array $arr = array()) {
        if ($params!=''){
            parse_str($params, $arr);
        } return $arr;
    }

    /**
     * Url::encodeString()
     *
     * Encode String to Url view function.
     * @return String
     */
    public static function encodeString ($str) {
        $str = html_entity_decode($str, ENT_QUOTES, WLCMS_SYSTEM_ENCODING);
        // Сначала заменяем "односимвольные" фонемы.
        $str = mb_strtr($str, "абвгдеёзийклмнопрстуфхъыэ", "abvgdeeziyklmnoprstufh'ie");
        $str = mb_strtr($str, "АБВГДЕЁЗИЙКЛМНОПРСТУФХЪЫЭ", "ABVGDEEZIYKLMNOPRSTUFH'IE");
        // Затем - "многосимвольные".
        $arChars = array (
            'Г?'=>'A',  'Г‚'=>'A',  'Д‚'=>'A',  'Г„'=>'A', 'Д†'=>'C', 'Г‡'=>'C', 'ДЊ'=>'C',
            'ДЋ'=>'D',  'Д?'=>'D',  'Г‰'=>'E','Д?'=>'E', 'Г‹'=>'E', 'Дљ'=>'E', 'ГЌ'=>'I',
            'ГЋ'=>'I',  'Д№'=>'L', 'Е?'=>'N', 'Е‡'=>'N', 'Г“'=>'O', 'Г”'=>'O', 'Е?'=>'O',
            'Г–'=>'O',  'Е”'=>'R',  'Е?'=>'R', 'Е '=>'S', 'Ељ'=>'O', 'Е¤'=>'T', 'Е®'=>'U',
            'Гљ'=>'U',  'Е°'=>'U', 'Гњ'=>'U', 'Гќ'=>'Y', 'ЕЅ'=>'Z', 'Е№'=>'Z', 'ГЎ'=>'a',
            'Гў'=>'a',  'Д?'=>'a', 'Г¤'=>'a', 'Д‡'=>'c', 'Г§'=>'c', 'ДЌ'=>'c', 'ДЏ'=>'d',
            'Д‘'=>'d',  'Г©'=>'e', 'Д™'=>'e', 'Г«'=>'e', 'Д›'=>'e',  'Г­'=>'i', 'Г®'=>'i',
            'Дє'=>'l',  'Е„'=>'n', 'Е?'=>'n', 'Гі'=>'o', 'Гґ'=>'o', 'Е‘'=>'o',  'Г¶'=>'o',
            'ЕЎ'=>'s',  'Е›'=>'s', 'Е™'=>'r', 'Е•'=>'r', 'ЕҐ'=>'t', 'ЕЇ'=>'u', 'Гє'=>'u',
            'Е±'=>'u',  'Гј'=>'u', 'ГЅ'=>'y', 'Еѕ'=>'z', 'Еє'=>'z', 'Л™'=>'-', 'Гџ'=>'Ss',
            'Д„'=>'A',  'Вµ'=>'u', 'ый'=>"iy", 'ЫЙ'=>"IY", 'ыЙ'=>"iY", 'Ый'=>"Iy",'Ґ'=>'G',
            'Ё'=>'Yo', 'Є'=>'E',  'Ї'=>'Yi',  'І'=>'I',
            'і' =>'i',  'ґ'=>'g',  'ё'=>'yo', '№'=>'#', 'є'=>'e',  'ї'=>'yi',  'А'=>'A',
            'Б' =>'B',  'В'=>'V',  'Г'=>'G',  'Д'=>'D',  'Е'=>'E',  'Ж'=>'Zh',  'З'=>'Z',
            'И' =>'I',  'Й'=>'Y',  'К'=>'K',  'Л'=>'L',  'М'=>'M',  'Н'=>'N',   'О'=>'O',
            'П' =>'P',  'Р'=>'R',  'С'=>'S',  'Т'=>'T',  'У'=>'U',  'Ф'=>'F',   'Х'=>'H',
            'Ц' =>'Ts', 'Ч'=>'Ch', 'Ш'=>'Sh', 'Щ'=>'Sch','Ъ'=>'',   'Ы'=>'Y',   'Ь'=>'',
            'Э' =>'E',  'Ю'=>'Yu', 'Я'=>'Ya', 'а'=>'a',  'б'=>'b',  'в'=>'v',   'г'=>'g',
            'д' =>'d',  'е'=>'e',  'ж'=>'zh', 'з'=>'z',  'и'=>'i',  'й'=>'y',   'к'=>'k',
            'л' =>'l',  'м'=>'m',  'н'=>'n',  'о'=>'o',  'п'=>'p',  'р'=>'r',   'с'=>'s',
            'т' =>'t',  'у'=>'u',  'ф'=>'f',  'х'=>'h',  'ц'=>'ts', 'ч'=>'ch',  'ш'=>'sh',
            'щ' =>'sch','ъ'=>'',   'ы'=>'y',  'ь'=>'',   'э'=>'e',  'ю'=>'yu',  'я'=>'ya'
        );
        foreach ($arChars as $search => $replace) {
            $str = str_replace($search, $replace, $str);
        } return $str;
    }
    /**
     * Url::stringToUrl()
     *
     * Prepare String to Url function.
     * @return String
     */
    public static function stringToUrl($str) {
        // Кодируем строку и преобразовываем к нижнему регистру
        $str = mb_strtolower(self::encodeString($str));
        // удаляем Экранирующие последовательности
        $str = str_replace(array("\r", "\n", "\t", "\0"), ' ', $str);
        // Удаляем все лишние символы
        $str = str_replace(array( '?','!',':',';','#','@','$','&','\'','"','~','`','’','%','^','*',
                                  '(',')','«','»','<','>','+','/','\\',',','.','…','{','}','[',']','|',
                                  "&quot;","&raquo;","&nbsp;","&amp;","&hellip;","“","”","?","€","•","®",
                                  "¶","§","©","®","™","°","±","…","‚","’","‘","”","„" ), '', $str);
        // удаляем все не "те" символы
        $str = preg_replace('/[^a-zA-Z0-9=\s—–-]+/u', '', $str);
        // заменяем все пробелы и "тире" на правильный символ
        $str = preg_replace('/[=\s—–]+/u', self::URL_SEPARATOR, $str); // значение берется из self::URL_SEPARATOR
        // удаляем все повторяющиеся
        $str = preg_replace('/[-]{2,}/u', self::URL_SEPARATOR, $str); // значение берется из self::URL_SEPARATOR
        //удаляем из начала и конца строки
        $str = trim($str, self::URL_SEPARATOR); // значение берется из self::URL_SEPARATOR
        // Возвращаем результат
        return $str;
    }
    
    /**
    * Returns whether this is an AJAX (XMLHttpRequest) request.
    * @return boolean whether this is an AJAX (XMLHttpRequest) request.
    */
    public static function isAjaxRequest(){
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
    }

}



/**
 * Description of UrlWL class. This class extend Url class
 * This class provides methods for create and manage SEO UrlWL. 
 * @author WebLife
 * @copyright 2012
 */
class UrlWL extends Url {
    
    const LANG_KEY_NAME  = 'lang';
    const USER_SEOPREFIX = 'user';
    const CAT_KEY_NAME   = 'catid';
    const SORT_KEY_NAME  = 'sort';
    const VIEW_KEY_NAME  = 'view';
    const PAGES_KEY_NAME = 'pages';
    const PAGES_ALL_VAL  = 'all';
    
    const LANG_PATH_IDX  = 0;
    const HOME_CATID     = 1;
    const ERROR_CATID    = 2;
    const PRINT_CATID    = 9;
    const CATALOG_CATID  = 11;
    const PAGE_TYPE_STATIC = 0;
    const PAGE_TYPE_AUTO   = 1;
    const PAGE_TYPE_AJAX   = 2;

    const SEOGROUP_UNDEFINED = 0;
    const SEOGROUP_MASTER    = 1;
    const SEOGROUP_SLAVE     = 2;
    const SEOGROUP_BOTH      = 3;
    
    private $lang         = '';
    private $defl         = '';
    private $base         = '';
    private $ajax         = null;
    private $page         = null;
    private $module       = null;
    private $itemID       = null;
    private $assortID     = null;
    private $parentID     = null;
    private $categoryID   = null;
    private $Filters      = null;
    private $showLang     = false;
    private $arLangs      = array();
    private $arCatPath    = array();
    private $arNavPath    = array();
    private $arBreadCrumb = array();
    private $_arPath      = array();

    public static $EXLUDE_PATHES = array(/*'katalogtovarov'*/);  
            
    /**
     * UrlWL::__construct()
     *
     * Object Construct function.
     * @return
     */
    public function __construct(array $arLangs, $showLang=false, $incSuffix=false, $suffix='') {
        parent::__construct((isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''), $incSuffix, $suffix);
        $this->base     = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        $this->showLang = (bool)$showLang;
        $this->arLangs  = $arLangs;
        $this->setDefaultLang();
        switch(WLCMS_ZONE){
            case 'BACKEND':
                if (array_key_exists(self::LANG_KEY_NAME, $_GET))
                    $this->setLang($_GET[self::LANG_KEY_NAME]);
                break;
            case 'FRONTEND':
                if (count($this->arParams)) $HTTP_GET_VARS = $_GET = $this->arParams;
                if (array_key_exists(self::LANG_PATH_IDX, $this->arPath))
                    $this->setLang($this->arPath[self::LANG_PATH_IDX]);
                if (array_key_exists(self::LANG_KEY_NAME, $this->arParams))
                    $this->setLang($this->arParams[self::LANG_KEY_NAME]);
                break;
            default: break;
        }
        // копируем данные по фильтрам
        $data = array();
        if(isset($this->arParams[UrlFilters::KEY_URL_CASE])){
            $data = $this->arParams[UrlFilters::KEY_URL_CASE];
            unset($this->arParams[UrlFilters::KEY_URL_CASE]);
        }
        $this->Filters = new UrlFilters($data);
    }

    /**
     * UrlWL::__destruct()
     *
     * Object Destruct function.
     * @return
     */
    public function __destruct() {
        parent::__destruct();
    }

    /**
     * UrlWL::__clone()
     *
     * Object Clone function.
     * @return
     */
    public function __clone() {
        $this->url          = '';
        $this->lang         = '';
        $this->anchor       = '';
        $this->arPath       = array();
        $this->arCatPath    = array();
        $this->arNavPath    = array();
        $this->arBreadCrumb = array();
        $this->Filters      = clone $this->Filters;
    }

    /**
     * UrlWL::copy()
     *
     * Object Clone function.
     * @return \UrlWL cloned
     */
    public function copy() {
        $cloned = clone $this;
        $cloned->url          = $this->url;
        $cloned->lang         = $this->lang;
        $cloned->arPath       = $this->arPath;
        $cloned->arCatPath    = $this->arCatPath;
        $cloned->arNavPath    = $this->arNavPath;
        $cloned->arBreadCrumb = $this->arBreadCrumb;
        return $cloned;
    }

    /**
     * UrlWL::init()
     *
     * Object Init function.
     * @return
     */
    public function init( DbConnector $DB ) {
        // сохраняем оригинальный путь
        $this->_arPath = $this->arPath;
        // инициализация остальных параметров
        $arCategory = $arItem = false;
        $BreadcrumbsTemplates = new BreadcrumbsTemplates();
        // определяем сеогруппу
        $seogroup = self::SEOGROUP_MASTER;
        // пробегаемся по пути от корня
        foreach($this->arPath as $idx=>$part){
            $founded = false;
            // язык и страница
            if (($idx==self::LANG_PATH_IDX and $this->setLang($part)) or (is_numeric($part) and $arCategory and $this->setPage($part))){
                $founded = true;
                $this->unSetPathItem($part, $idx, false);
            } else {
                // ищем по первому уровню (master ссылки) 
                if ($seogroup == self::SEOGROUP_MASTER && ($row = $this->getUsedSeoPathRow($DB, $part, $seogroup)) && ($this->module = $row['mdl']) && ($tbl = strtoupper($this->module).'_TABLE') && defined($tbl)){
                    unset($row['mdl'], $row['tbl']);
                    if($this->module == 'main'){
                        if($row['active'] && ($row['module'] != 'prints' || $row['id'] == self::PRINT_CATID)){
                            $founded    = true;
                            $arCategory = $row;
                            $this->initCategoryBreadCrumbs($arCategory);
                            $this->initCategory($arCategory);
                            if($this->module == 'catalog' || $this->module == 'prints'){
                                $arCategory['selectedFilters'] = array();
                                $arCategory['filters'] = self::getCategoryFilters($arCategory['id'], UrlFilters::LIST_TYPE_SEO, $this->module);
                                $this->Filters->setCategoryFilters($arCategory['filters']);
                            }
                        }
                    } else {
                        // здесь нужно будет описывать отдельные модули в которых нет поля 'active'
                        if (!isset($row['active'])){
                            switch ($this->module) {
                                case "series":
                                    $row['active'] = 1;
                                    if ($series = getSimpleItemRow($this->itemID, SERIES_TABLE)) {
                                        $this->brandID = $series["brand_id"];
                                        $arCategory['selectedFilters'] = array();
                                        $arCategory['filters'] = self::getCategoryFilters($arCategory['id'], UrlFilters::LIST_TYPE_SEO);
                                        $this->Filters->setCategoryFilters($arCategory['filters']);
                                        $this->addToBreadCrumbs($this->buildItemUrl($arCategory, $row), $row['title']);
                                        $this->addToNavPath($row['seo_path']);
                                    }
                                    break;
                                default:
                                    $row['active'] = 0;
                                    break;
                            }
                        }
                        if ($row['active']){
                            // здесь нужно будет описывать отдельные модули в которых нет поля 'cid'
                            // также нужно установить id,title и seo_path
                            if(empty($row['cid'])){
                                switch ($this->module) {
                                    case 'prints':
                                        if(($print = getSimpleItemRow($row['print_id'], PRINTS_TABLE))) {
                                            $row = array_merge($print, array('cid' => $print['category_id']), $row);
                                            $arCategory = self::getCategoryRow($row['cid'], true, MAIN_TABLE, false);
                                        }
                                        break;
                                    default:
                                        isset($row['cid']) OR $row['cid'] = 0;
                                        $arCategory = getModule($this->module);
                                        break;
                                }
                            } else {
                                $arCategory = self::getCategoryRow($row['cid'], true, MAIN_TABLE, false);
                            }
                            if ($arCategory && $arCategory['active']){
                                $founded      = true;
                                $arItem       = $row;
                                $this->itemID = intval($row['id']);
                                $this->initCategoryBreadCrumbs($arCategory);
                                $this->initCategory($arCategory, false);
                                if($this->module == 'users'){
                                    $row['title'] = $row["firstname"];
                                } elseif($this->module == 'brands'){
                                    $this->brandID = $this->itemID;
                                    $arCategory['selectedFilters'] = array();
                                    $arCategory['filters'] = self::getCategoryFilters($arCategory['id'], UrlFilters::LIST_TYPE_SEO);
                                    $this->Filters->setCategoryFilters($arCategory['filters']);
                                } elseif($this->module == 'catalog'){
                                }
                                $this->addToBreadCrumbs($this->buildItemUrl($arCategory, $row), $row['title']);
                                $this->addToNavPath($row['seo_path']);
                            }
                        }
                    }
                    // ставим поментку что первый уровень найден
                    $seogroup = self::SEOGROUP_SLAVE;
                }
                // Если по master нашли и категория установлена то ищем по фильтрам (slave) исходя из настройки категории
                elseif($seogroup == self::SEOGROUP_SLAVE && $arCategory) {
                    // вначале проверяем на Range сеопуть 
                    $range = UrlFiltersRange::parseSeoPath($part);
                    // также ищем среди slave ссылок
                    if($range || (($row = $this->getUsedSeoPathRow($DB, $part, $seogroup)) && ($tbl = strtoupper($row['mdl']).'_TABLE') && defined($tbl))){
                        // смотрим по настройкам категории
                        while(NULL !== ($val = array_shift($arCategory['filters']))) {
                            $val['seo_path'] = $part;
                            if(array_key_exists($part, $val['values'])){
                                $founded = true;
                                $title   = $row ? $row['title'] : $val['values'][$part]['title'];
                                $arCategory['selectedFilters'][$val['id']][] = $val['values'][$part]['alias'];
                            } elseif($range && !$range->auto && $range->id==$val['id']){
                                $founded = true;
                                $title = $val['title'] . ' '.LABEL_FROM.' '.$range->min.($range->max ? ' '.LABEL_TO.' ' . $range->max: '');
                                $arCategory['selectedFilters'][$val['id']][UrlFiltersRange::KEY_MIN] = $range->min;
                                $arCategory['selectedFilters'][$val['id']][UrlFiltersRange::KEY_MAX] = $range->max;
                            }
                            if($founded){ 
                                $url = $this->buildCategoryUrl($arCategory, null, '', 1, false, $this->copyFilters()->setSelected($arCategory['selectedFilters']));
                                $BreadcrumbsTemplates->addUrl($url);
                                $BreadcrumbsTemplates->addAlias($title, $val, $part); 
                                $this->addToBreadCrumbs($url, ucfirst($title));
                                $this->addToNavPath($val['seo_path']);
                                $this->unSetPathItem($val['seo_path'], $idx, false);
                                break;
                            }
                        }
                    }
                }
            }
            // если не найдено - прерываем и выдаем ошибку
            if(!$founded){
                $this->redirectToErrorPage(true);
                break;
            }
        }
        // определяем все ли в порядке?
        $founded = ($this->getCategoryId() and $this->getCategoryId() != self::ERROR_CATID);
        // если  найдено произмодим необходимые манипуляции
        if($founded){
            // инициализация переменной ajax
            $this->ajax = array_key_exists('ajax', $this->arParams) ? 1 : 0;
            // донастройка категории если модуль каталог
            if(($this->module == 'catalog' || $this->module == 'prints' || $this->module == 'brands') && !empty($arCategory['selectedFilters'])){
                $this->Filters->prependAttributes($arCategory['selectedFilters']);
                $BreadcrumbsTemplates->prepare($this->arBreadCrumb);
            }
        } return $founded;
    }
 
    /**
     * UrlWL::buildUrl()
     *
     * UrlWL Build function.
     * @return String
     */
    public function buildUrl($left_slashed=true, $right_slashed=false) {
        $data = self::prepareUrlData(array('arPath'=>$this->arPath, 'arParams'=>$this->arParams), $this->page, false, $this->copyFilters());
        return self::toUrl($data['arPath'], $this->showLang, $this->lang, $this->incSuffix, $this->suffix, $data['arParams'], $this->anchor, $left_slashed, $right_slashed);
    }

    /**
     * UrlWL::toUrl()
     *
     * UrlWL Build function.
     * @param array $arPath
     * @param type $showLang
     * @param type $lang
     * @param bool $incSuffix
     * @param string $suffix
     * @param array $arParams
     * @param string $anchor
     * @param boolean $left_slashed
     * @param boolean $right_slashed
     * @return String
     */
    public static function toUrl(array $arPath, $showLang, $lang, $incSuffix, $suffix, array $arParams, $anchor='', $left_slashed=true, $right_slashed=false) {
        if ($showLang) {
            array_unshift($arPath, $lang);
        }
        return parent::requestToUrl($arPath, $incSuffix, $suffix, $arParams, $anchor, $left_slashed, $right_slashed);
    }

    /**
     * UrlWL::buildQuery()
     *
     * Build Params String Url function.
     * @return String
     */
    public function buildQuery() {
        $data = self::prepareUrlData(array('arPath'=>$this->arPath, 'arParams'=>$this->arParams), $this->page, false, $this->copyFilters());
        return parent::toUrl(array(), $this->incSuffix, $this->suffix, $data['arParams'], $this->anchor, false, false);
    }

    /**
     * UrlWL::buildSingletonUrl()
     *
     * Build Menu Item Url function.
     * @param string $seopath
     * @param mixed $params array or string
     * @param string $anchor
     * @param int $page
     * @param bool $forPager
     * @param UrlFilters $Filters
     * @return String
     */
    public function buildSingletonUrl($seopath, $params=null, $anchor='', $page=1, $forPager=false, UrlFilters $Filters = null) {
        $data = self::prepareUrlData(array(
            'arPath' => array($seopath), 
            'arParams' => is_array($params) ? $params : self::parseParams((string)$params),
        ), $page, $forPager, $Filters);
        return self::toUrl($data['arPath'], $this->showLang, $this->lang, ($forPager ? false : $this->incSuffix), $this->suffix, $data['arParams'], $anchor);
    }

    /**
     * UrlWL::buildItemUrl()
     *
     * Build Menu Item Url function.
     * @param array $arCategory
     * @param array $arItem
     * @param mixed $params array or string
     * @param string $anchor
     * @param int $page
     * @param bool $forPager
     * @param UrlFilters $Filters
     * @return String
     */
    public function buildItemUrl(array $arCategory, array $arItem, $params=null, $anchor='', $page=1, $forPager=false, UrlFilters $Filters = null) {
        $data = self::prepareCategoryData($arCategory, $params, !empty($arItem['id']));
        if(empty($data['url'])){
            if(!empty($arItem['seo_path'])){
                $data['arPath'][] = $arItem['seo_path'];
            }
            $data = self::prepareUrlData($data, $page, $forPager, $Filters);
            $data['url'] = self::toUrl($data['arPath'], $this->showLang, $this->lang, ($forPager ? false : $this->incSuffix), $this->suffix, $data['arParams'], $anchor);
        }
        return $data['url'];
    }

    /**
     * UrlWL::buildCategoryUrl()
     *
     * Build Menu Category Item Url function.
     * @param array $arCategory
     * @param mixed $params array or string
     * @param string $anchor
     * @param int $page
     * @param bool $forPager
     * @param UrlFilters $Filters
     * @return String
     */
    public function buildCategoryUrl(array $arCategory, $params=null, $anchor='', $page=1, $forPager=false, UrlFilters $Filters = null) {
        $data = self::prepareCategoryData($arCategory, $params);
        if (empty($data['url'])) {
            $data = self::prepareUrlData($data, $page, $forPager, $Filters);
            $data['url'] = self::toUrl($data['arPath'], $this->showLang, $this->lang, ($forPager ? false : $this->incSuffix), $this->suffix, $data['arParams'], $anchor);
        } return $data['url'];
    }

    /**
     * UrlWL::prepareCategoryData()
     *
     * Build Category Data Url function.
     * @param array $arCategory
     * @param mixed $params array or string
     * @param bool $forItem preparing for item or no (default false)
     * @return array
     */
    private static function prepareCategoryData(array & $arCategory, $params, $forItem = false) {
        $data = array('url'=>'');
        if (!$forItem){
            if (!empty($arCategory['redirectid'])){
                $arCategory = self::getCategoryByIdWithSeoPath($arCategory['redirectid']);
            }
            if (!empty($arCategory['redirecturl'])){
                $data['url'] = $arCategory['redirecturl'];
            } else if($arCategory['id'] == self::HOME_CATID){
                $data['url'] = '/';
            }
        } 
        if (empty($data['url'])) {
            $data['arPath'] = self::buildCategoryPath($arCategory);
            $data['arParams'] = is_array($params) ? $params : self::parseParams((string)$params);
            if($arCategory['pagetype'] == self::PAGE_TYPE_AJAX) {
                $data['arParams']['ajax']='';
            }
        } return $data;
    }

    /**
     * UrlWL::prepareUrlData()
     *
     * Build Category Data Url function.
     * @param array $arData
     * @param int $page
     * @param bool $forPager
     * @param UrlFilters $Filters
     * @param bool $singletonUrl
     * @return array
     */
    private static function prepareUrlData(array $arData, $page, $forPager=false, UrlFilters $Filters = null, $singletonUrl=true) {
        // Проверяем чтобы в массиве был только один путь
        if ($singletonUrl && count($arData['arPath']) > 1) {
            $arData['arPath'] = array(end($arData['arPath']));
        }
        // Добавляем фильтры
        if ($Filters) {
            foreach($Filters->slicePath() as $val) {
                $arData['arPath'][] = $val;
            } $filterData = $Filters->toArray();
            if ($filterData) {
                $arData['arParams'][UrlFilters::KEY_URL_CASE] = $filterData;
            }
        }
        // Добавляем страницу
        if ($page > 1 and !$forPager) {
            $arData['arPath'][] = $page;
        } return $arData;
    }

    /**
     * UrlWL::buildCategoryPath()
     *
     * Build Category array Path from Root
     * @return Array
     */
    public static function & buildCategoryPath(array & $arCategory, $table=MAIN_TABLE, $forse=false, $singletonUrl=true) {
        $arPath = array();
        if (!empty($arCategory['arPath'])) {
            $arPath = $arCategory['arPath'];
        } else {
            // если не одноуровневая ссылка
            if (!$singletonUrl) {
                static $arPathes = array();
                $id = $arCategory['id'];
                if($forse or !isset($arPathes[$table]) or !isset($arPathes[$table][$id])){
                    $arr = $arPathes[$table][$id] = array();
                    $pid = $arCategory['pid'];
                    array_push($arr, $arCategory['seo_path']);
                    while ($pid > 0) {
                        if (isset($arPathes[$table][$pid])){
                            $arPathes[$table][$id] = $arPathes[$table][$pid];
                            $pid = 0;
                        } else if (($row = self::getCategoryRow($pid, false, $table, $forse))) {
                            array_push($arr, $row['seo_path']);
                            $pid = $row['pid'];
                        } else {
                            $pid = 0;
                        }
                    }
                    foreach(array_reverse($arr) as $seo_path){
                        if (!empty($seo_path) && !in_array($seo_path, self::$EXLUDE_PATHES)) {
                            array_push($arPathes[$table][$id], $seo_path);
                        }
                    }
                }
                // получаем из статики
                $arPath = $arPathes[$table][$id];
            }
            // добавляем текущий если не пустой
            else if(!empty($arCategory['seo_path']) && !in_array($arCategory['seo_path'], self::$EXLUDE_PATHES)){
                $arPath[] = $arCategory['seo_path'];
            } $arCategory['arPath'] = $arPath;
        } return $arPath;
    }

    /**
     * UrlWL::buildPagerUrl()
     *
     * Build Menu Item Url function for Pager.
     * @return String
     */
    public function buildPagerUrl(array $arCategory) {
        return $this->buildCategoryUrl($arCategory, null, '', 1, true);
    }
    /**
     * UrlWL::getCategoryRow()
     *
     * Get Category Row Array 
     * @return array
     */
    private static function getCategoryRow($id, $withText, $table, $forse) {
        $arCategory = array();
        if ($id > 0){
            static $categories = array();
            if ($forse || !isset($categories[$table][$id]) || ($withText && !isset($categories[$table][$id]['text']))){
                $query = "SELECT `id`, `pid`, `redirectid`, `title`, `menutype`, `separator`, `pagetype`".($withText ? ", IFNULL(`text`,'') `text`" : "").",
                            `module`, `image`, TRIM(`redirecturl`) `redirecturl`, TRIM(`seo_path`) `seo_path`, `active`
                          FROM `".$table."` WHERE `id` = '{$id}' LIMIT 1";
                $result = mysql_query($query);
                if ($result AND mysql_num_rows($result)>0 AND ($row = mysql_fetch_assoc($result))){
                    if ($withText && !isset($row['text'])) $row['text'] = '';
                    $arCategory = $row;
                } else {
                    $row = array('id' => 0, 'text' => '');
                } $categories[$table][$id] = $row;
            } else if($categories[$table][$id]['id']) {
                $arCategory = $categories[$table][$id];
            } if (!$withText) unset($arCategory['text']);
        } return $arCategory;
    }

    /**
     * UrlWL::getCategoryById()
     *
     * Get Category Array with SEO Path Array From Root function.
     * @return array
     */
    public function getCategoryById($id, $withText=false, $forse=false) {
        return self::getCategoryByIdWithSeoPath($id, $withText, MAIN_TABLE, $forse);
    }

    /**
     * UrlWL::getCategoryByIdWithSeoPath()
     *
     * Get Category Array with SEO Path Array From Root function.
     * @return array
     */
    public static function getCategoryByIdWithSeoPath($id, $withText=false, $table=MAIN_TABLE, $forse=false) {
        if(($arCategory = self::getCategoryRow($id, $withText, $table, $forse))){
            self::buildCategoryPath($arCategory, $table, $forse);
        } return $arCategory;
    }

    /**
     * UrlWL::getCategoryFilters()
     *
     * Get Category Array with SEO Path Array From Root function.
     * @param int $id
     * @param int $type : 1 - default list; 2 - seo settings
     * @param string $module 
     * @param array $types
     * @return array
     */
    public static function getCategoryFilters($id, $type=UrlFilters::LIST_TYPE_DEFAULT, $module='catalog', $types=array()) {
        $filters = array();
        if(($id = intval($id)) > 0 and ($type = intval($type)) > 0){
            $query = 'SELECT f.`id`, f.`tid`, f.`aid`, IFNULL(a.`gid`, "") `gid`, f.`title`, IFNULL(a.`title`, "") `atitle`, f.`alias`, cf.`order` `seq`
                FROM `'.CATEGORY_FILTERS_TABLE.'` cf
                JOIN `'.FILTERS_TABLE.'` f ON f.`id`=cf.`fid`
                LEFT JOIN `'.ATTRIBUTES_TABLE.'` a ON a.`id`=f.`aid` 
                LEFT JOIN `'.ATTRIBUTE_GROUPS_TABLE.'` ag ON ag.`id`=a.`gid` 
                WHERE cf.`cid` = "'.$id.'"'.($types ? ' AND f.`tid` IN ('.implode(',',$types).')' : '').' AND  cf.`type` = "'.$type.'" AND (f.`aid`=0 OR ag.`active`=1) 
                ORDER BY cf.`order`';
            $result1 = mysql_query($query);
            while($row1 = mysql_fetch_assoc($result1)){
                // получаем возможные значения с сеопутями
                $row1['values'] = array();
                switch ($row1['tid']) {
                    case UrlFilters::TYPE_BRAND:
                        $query = 'SELECT `id`, `title`, "" `title_single`, "" `title_multi`, `seo_path`, `id` `alias` FROM `'.BRANDS_TABLE.'` WHERE `active`=1 AND `seo_path`<>"" ORDER BY `order`';
                        $result2 = mysql_query($query);
                        while($row2 = mysql_fetch_assoc($result2)){
                            $row1['values'][$row2['seo_path']] = $row2;
                        }
                        break;
                    case UrlFilters::TYPE_CATEGORY:
                        $query = 'SELECT `id`, `title`, "" `title_single`, "" `title_multi`, `seo_path`, `id` `alias` FROM `'.MAIN_TABLE.'` WHERE `active`=1 AND `module`="'.$module.'" AND `id` NOT IN('.self::CATALOG_CATID.','.self::PRINT_CATID.') AND `seo_path`<>"" ORDER BY `order`';
                        $result2 = mysql_query($query);
                        while($row2 = mysql_fetch_assoc($result2)){
                            $row1['values'][$row2['seo_path']] = $row2;
                        }
                        break;
                    case UrlFilters::TYPE_COLOR:
                        $query = 'SELECT `id`, `title`, "" `title_single`, "" `title_multi`, `seo_path`, `id` `alias` FROM `'.COLORS_TABLE.'` WHERE `seo_path`<>"" ORDER BY `order`';
                        $result2 = mysql_query($query);
                        while($row2 = mysql_fetch_assoc($result2)){
                            $row1['values'][$row2['seo_path']] = $row2;
                        }
                        break;
                    case UrlFilters::TYPE_PRICE:
                    case UrlFilters::TYPE_NUMBER:
                        $query = 'SELECT `id`, `title`, "" `title_single`, "" `title_multi`, IF(`vmin` IS NULL, 0, `vmin`) `vmin`, IF(`vmax` IS NULL, 0, `vmax`) `vmax`, `id` `alias` FROM `'.RANGES_TABLE.'` WHERE `fid`="'.$row1['id'].'" ORDER BY `order`';
                        $result2 = mysql_query($query);
                        while($row2 = mysql_fetch_assoc($result2)){
                            $row2['seo_path'] = UrlFiltersRange::generateSeoPath(($row1['tid']==UrlFilters::TYPE_PRICE ? UrlFiltersRange::SEO_AUTO_PRICE : UrlFiltersRange::SEO_AUTO_RANGE), $row2['id'], $row2['vmin'], $row2['vmax']);
                            $row1['values'][$row2['seo_path']] = $row2;
                        }
                    default:
                        $query = 'SELECT av.`id`, av.`title`, av.`title_single`, av.`title_multi`, av.`seo_path`, av.`id` `alias`
                             FROM `'.ATTRIBUTES_VALUES_TABLE.'` av '.($module == 'catalog' ? '
                             JOIN `'.MODEL_ATTRIBUTES_TABLE.'` t ON t.`value`=av.`id` ' : ($module == 'prints' ? '
                             JOIN (
                                SELECT `aid` `id`, `value` FROM `'.SUBSTRATES_ATTRIBUTES_TABLE.'` WHERE `aid`="'.$row1['aid'].'"
                              UNION 
                                SELECT `aid` `id`, `value` FROM `'.PRINT_ATTRIBUTES_TABLE.'` WHERE `aid`="'.$row1['aid'].'"
                             ) t ON(t.`value`=av.`id`) ' : '')).'
                             WHERE av.`aid`="'.$row1['aid'].'" AND `seo_path`<>""
                             GROUP BY av.`id`
                             HAVING COUNT(t.`id`)>0
                             ORDER BY av.`order`
                        ';
                        $result2 = mysql_query($query);
                        while($row2 = mysql_fetch_assoc($result2)){
                            isset($row2['alias']) or $row2['alias'] = '0';
                            $row1['values'][$row2['seo_path']] = $row2;
                        }
                        break;
                }
                $filters[$row1['id']] = $row1;
            }
        } return $filters;
    }

    /**
     * UrlWL::initCategory()
     *
     * Get Category Array with SEO Path Array From Root function.
     */
    public function initCategory(array & $arCategory, $addToNavPath = true) {
        $this->module     = $arCategory['module'];
        $this->parentID   = intval($arCategory['pid']);
        $this->categoryID = intval($arCategory['id']);
        // если нужно - то добавляем в навигацию
        if($addToNavPath){
            $this->addToCategoryNavPath($arCategory['seo_path']);
            $this->addToNavPath($arCategory['seo_path']);
            $arCategory['arPath'] = $this->getNavPath();
        }
        isset($arCategory['arPath']) or $arCategory['arPath'] = array($arCategory['seo_path']);
    }

    /**
     * UrlWL::initCategoryBreadCrumbs()
     *
     * Init Category BreadCrumbs
     */
    public function initCategoryBreadCrumbs(array $arCategory, $addFullPath = true) {
        $arCategory['arPath'] = array();
        if($addFullPath){
            $pid  = $arCategory['pid'];
            $rows = array();
            while ($pid > 0) {
                if (($row = self::getCategoryRow($pid, false, MAIN_TABLE, false))) {
                    $pid = intval($row['pid']);
                    $rows[] = $row;
                } else {
                    $pid = 0;
                }
            }
            foreach (array_reverse($rows) as $row){
                if(!in_array($row['seo_path'], self::$EXLUDE_PATHES)){
                    $arCategory['arPath'][] = $row['seo_path'];
                    $row['arPath'] = $arCategory['arPath'];
                    $this->addToBreadCrumbs($this->buildCategoryUrl($row), (empty($row['navtitle']) ? $row['title'] : $row['navtitle']));
                }
            }
        }
        $arCategory['arPath'][] = $arCategory['seo_path'];
        $this->addToBreadCrumbs($this->buildCategoryUrl($arCategory), (empty($arCategory['navtitle']) ? $arCategory['title'] : $arCategory['navtitle']));
    }

    public function redirectToErrorPage($checkForward = false) {
        if($checkForward && ($target = ForwardsCached::getCompatibilityTarget($this->getUrl()))) {
            $url = $target;
        } else  {
            $url = $this->buildCategoryUrl($this->getCategoryById(self::ERROR_CATID, false));
        }
        Redirect($url);
    }

    /**
     * UrlWL::strToUrl()
     *
     * Prepare String to Url function.
     * @return String
     */
    public function strToUrl($str, $prefix='item') {
        $str = trim(parent::stringToUrl($str));
        // Проверяем, является ли item seo_path числом, если да, то дописываем значение $prefix
        if (is_numeric($str)) $str = strtolower(trim($prefix)).self::URL_SEPARATOR.$str;
        // Возвращаем результат
        return $str;
    }

    /**
     * UrlWL::strToUniqueUrl()
     * Prepare String to Url function.
     * @param DbConnector $connector
     * @param string $seo_str
     * @param string $num_prefix
     * @param string $item_tbl
     * @param int $item_id
     * @param bool $lang_sync
     * @return String
     */
    public function strToUniqueUrl(DbConnector $connector, $seo_str, $num_prefix='item', $item_tbl='', $item_id=0, $lang_sync=true) {
        // Возвращаем результат
        return $this->getUniqueSeoPath($connector, $this->strToUrl($seo_str, $num_prefix), $item_tbl, $item_id, $lang_sync);
    }

    /**
     * UrlWL::cleanUrlFromLangs()
     *
     * Clean Url From Langs function.
     * @return String
     */
    public function & cleanUrlFromLangs($url=''){
        $url = empty($url) ? $this->url : trim($url);
        if(empty($url)) return $url;

        $arReplaceLangs = array();
        switch(WLCMS_ZONE){
            case 'BACKEND':
                foreach($this->arLangs as $ln){
                    $arReplaceLangs[] = '?lang='.$ln;
                    $arReplaceLangs[] = '&lang='.$ln;
                }
                break;
            case 'FRONTEND':
                foreach($this->arLangs as $ln)
                    $arReplaceLangs[] = "/$ln/";
                break;
            default: break;
        } 
        $url = str_replace($arReplaceLangs, '', $url);
        return $url;
    }

    /**
     * UrlWL::createLangsUrls()
     *
     * Create Langs Url Array to redirect function.
     * @return String
     */
    public function & createLangsUrls(DbConnector $DB){
        $arLangsUrls = array();
        switch(WLCMS_ZONE){
            case 'BACKEND':
                $url = $this->cleanUrlFromLangs();
                $lpref = (strpos($url, '?')===FALSE) ? '?' : '&';
                foreach($this->arLangs as $ln)
                    $arLangsUrls[$ln] = $url.$lpref.'lang='.$ln;
                break;
            case 'FRONTEND':
                foreach($this->arLangs as $ln){
                    $url = '/'.$ln.'/';
                    if($this->lang==$ln) $url = $this->url;
                    elseif($this->categoryID > self::HOME_CATID) {
                        $cloned  = clone $this;
                        $cloned->setLang($ln);
                        $arCategory = self::getCategoryByIdWithSeoPath($cloned->categoryID, false, DbConnector::replaceLang(MAIN_TABLE, $this->lang, $cloned->lang, DBTABLE_LANG_SEP));
                        if($arCategory){
                            $cloned->module = $arCategory['module'];
                            // ищем среди модулей
                            if($cloned->itemID and $cloned->module and ($tbl = strtoupper($cloned->module).'_TABLE') and defined($tbl)){
                                $query = 'SELECT * FROM `'.DbConnector::replaceLang(constant($tbl), $this->lang, $cloned->lang, DBTABLE_LANG_SEP).'` WHERE `id`=\''.$cloned->itemID.'\' LIMIT 1';
                                if($DB->Query($query) and ($row = $DB->fetchAssoc()) and !empty($row['id']) and $row['active']){
                                    switch($cloned->module) {
                                        case 'users':
                                            $row['seo_path'] = self::USER_SEOPREFIX.$row['id'];
                                            $arCategory['arPath'][] = $row['seo_path'];
                                            break;
                                        default:
                                            $arCategory['arPath'][] = $row['seo_path'];
                                            break;
                                    }
                                } else {
                                    $arCategory = null;
                                }
                            }
                            /** @todo здесь нужно будет доделать формирование ссылки с фильтрами. пока они очищаются */
                        }
                        if($arCategory){
                            $cloned->Filters->reset();
                            $cloned->setPath($arCategory['arPath']);
                            $url = $cloned->buildUrl();
                        }
                    }
                    $arLangsUrls[$ln] = $url;
                } break;
            default: break;
        } return $arLangsUrls;
    }

    
    /**
     * @param DbConnector $connector
     * @param bool $all_langs
     * @param bool $by_module
     * @return array
     */
    private function getSeoPathTables (DbConnector $connector, $all_langs=true, $by_module=false) {
        $tables = array();
        $allTables = $connector->getTables();
        $connector->Query('SELECT `module`,`seotable` FROM `' . MODULES_PARAMS_TABLE . '` WHERE `seogroup`=1');
        while ($row = $connector->fetchAssoc()) {
            if(defined($row['seotable']) && ($table = constant($row['seotable']))){
                $key = $by_module ? $row['module'] : $table;
                if($all_langs){
                    foreach($this->arLangs as $ln){
                        $tbl = ($this->lang == $ln ? $table : DbConnector::replaceLang($table, $this->lang, $ln, DBTABLE_LANG_SEP));
                        if (in_array($tbl, $allTables)){
                            $tables[$key][$ln] = $tbl;
                        }
                    }
                } else if(in_array($table, $allTables)){
                    $tables[$key][$this->lang] = $table;
                }
            }
        }
        return $tables;
    }

    /**
     * function provided for quick getting used seopath table name
     * @param DbConnector $connector
     * @param string $seopath
     * @param int $seogroup
     * @return array founded row
     */
    public function getUsedSeoPathRow (DbConnector $connector, $seopath, $seogroup) {
        /**
         * @notice сделал массивом для ускорения процесса, но немешало бы это реализовать в админке
         * Настройки таблиц по сео - таблица, модуль, в какой сеогруппе находится
         * self::SEOGROUP_MASTER - сеопути участвующие в первом уровне
         * self::SEOGROUP_SLAVE - сеопути участвующие в фильтрации (второй и далее уровни)
         * self::SEOGROUP_BOTH - и в первом уровне и в последующих (например в бренды, категории с подкатегориями (не в текущем проекте))
         * @var array  
         */
        static $tables = array(
            ATTRIBUTES_VALUES_TABLE => array('attributes', self::SEOGROUP_SLAVE),
            BRANDS_TABLE => array('brands', self::SEOGROUP_BOTH),
            CATALOG_TABLE => array('catalog', self::SEOGROUP_MASTER),
            COLORS_TABLE => array('colors', self::SEOGROUP_SLAVE),
            MAIN_TABLE => array('main', self::SEOGROUP_BOTH),
            NEWS_TABLE => array('news', self::SEOGROUP_MASTER),
            PRINT_ASSORTMENT_TABLE => array('prints', self::SEOGROUP_MASTER),
            PRINT_TYPES_TABLE => array('print_types', self::SEOGROUP_MASTER),
            SUBSTRATES_TABLE => array('substrates', self::SEOGROUP_SLAVE),
            SERIES_TABLE => array('series', self::SEOGROUP_MASTER),
        );
        // если переменные корректны - продолжаем поиск
        if($seopath && $seogroup){
            $queries = array(); 
            foreach($tables as $table => $config){
                list($module, $group) = $config;
                if($group == $seogroup || $group == self::SEOGROUP_BOTH) {
                    $queries[] = 'SELECT `id`, "'.$module.'" `mdl`, "'.$table.'" `tbl` FROM `'.$table.'` WHERE `seo_path`="'.$connector->ForSql($seopath).'" LIMIT 1';
                }
            }
            $query = count($queries)>1 ? '('.implode(') UNION (', $queries).')' : reset($queries);
            if($queries && $connector->Query($query) && $connector->getNumRows() > 0 && ($row = $connector->fetchAssoc())){
                $query = 'SELECT *, "'.$row['mdl'].'" `mdl`, "'.$row['tbl'].'" `tbl` FROM `'.$row['tbl'].'` WHERE `id`='.$row['id'].' LIMIT 1';
                if($connector->Query($query) && $connector->getNumRows() > 0){
                    return $connector->fetchAssoc();
                }
            }
        }
        return null;
    }
    
    
    /**
     * function provided for quick getting count of existing items
     * with the same seo_path useful in creating or editing operations
     * @param DbConnector $connector
     * @param string $seopath
     * @param string $seotable
     * @param int $itemID
     * @param bool $lang_sync
     * @return bool
     */
    public function isUsedSeoPath (DbConnector $connector, $seopath, $seotable='', $itemID=0, $lang_sync=true) {
        if(UrlFiltersRange::checkSeoPath($seopath)){
            $used = true;
        } else {
            $queries = array();
            foreach($this->getSeoPathTables($connector, $lang_sync) as $key=>$data){
                foreach($data as $lang=>$table){
                    $queries[] = 'SELECT COUNT(`id`) FROM `'.$table.'` WHERE `seo_path`="'.$seopath.'"'.(($itemID and $seotable==$key) ? ' AND `id`<>'.$itemID : '');
                }
            }
            $used = ($queries and $connector->Query('SELECT (('.implode(') + (', $queries).')) AS `cnt`') and $connector->fetchResult());
        }
        return $used;
    }
    
    /*
     * function provided for creating unique seo path for item
     * @param DbConnector $connector
     * @param string $seopath
     * @param string $seotable
     * @param int $itemID
     * @param bool $lang_sync
     * @return string
     */
    public function getUniqueSeoPath (DbConnector $connector, $seopath, $seotable='', $itemID=0, $lang_sync=true) {
        do {
            $used = $this->isUsedSeoPath($connector, $seopath, $seotable, $itemID, $lang_sync);
            if($used){
                $seopath = self::generateUniqueSeoPath($seopath, true);
            }
        } while($used);
        return $seopath;
    }
    
    /*
     * function provided for creating unique seo path for item
     * @param string $seopath
     * @param bool $update_ts
     * @return string
     */
    public static  function generateUniqueSeoPath ($seopath, $update_ts=true) {
        $ts_curr = date('ymdHis'); $ts_sep = self::URL_SEPARATOR; $ts_length = strlen($ts_curr); 
        $ts_mask = '/^.*?('.$ts_sep.'[\d]{'.$ts_length.'})/u'; $matches = array();
        if(preg_match($ts_mask, $seopath, $matches)){
            if($update_ts){
                $seopath = mb_substr($seopath, 0, strpos($seopath, $matches[1]));
            } else {
                $ts_curr = trim($matches[1], $ts_sep);
            }
        }
        $seopath .= $ts_sep.$ts_curr;
        return $seopath;
    }
    
    /*
     * function provided for creating seo path identified by ID for item
     * @param int $id
     * @param string $prefix
     * @return string
     */
    public static  function generateIdentifySeoPath ($id, $prefix) {
        return strtolower($prefix).self::URL_SEPARATOR.$id;
    }


    /**
     * Get id from identified SeoPath
     * @param string $seopath
     * @param string $prefix
     * @return int
     */
    public static function parseIdentifySeoPath($seopath, $prefix) {
        return intval(str_replace($prefix.self::URL_SEPARATOR, '', $seopath));
    }


    /**
     * Check SeoPath is identified 
     * @param string $seopath
     * @param string $prefix
     * @return bool
     */
    public static function checkIdentifySeoPath($seopath, $prefix) {
        return (strpos($seopath, self::generateIdentifySeoPath('', $prefix)) === 0);
    }
    
    /**
     * UrlWL::setDefaultLang()
     *
     * Set Default Site Lang from arLangs.
     * @return
     */
    public function setDefaultLang() {
        $this->defl = reset($this->arLangs);
    }

    /**
     * UrlWL::getDefaultLang()
     *
     * Get Default Site Lang.
     * @return String
     */
    public function getDefaultLang() {
        return $this->defl;
    }

    /**
     * UrlWL::setLang()
     *
     * Set Current Site Lang function.
     * @return
     */
    public function setLang( $lang ) {
        if(in_array($lang, $this->arLangs)){
            $this->lang = $lang;
            return true;
        } return false;
    }

    /**
     * UrlWL::getLang()
     *
     * Get Current Site Lang function.
     * @return
     */
    public function getLang() {
        return $this->lang;
    }

    /**
     * UrlWL::setLangs()
     *
     * Set Array Site Langs Keys function.
     * @return
     */
    public function setLangs( array $arLangs = array() ) {
        $this->arLangs = $arLangs;
    }

    /**
     * UrlWL::getLangs()
     *
     * Get array Site Langs Keys function.
     * @return
     */
    public function getLangs() {
        return $this->arLangs;
    }

    /**
     * UrlWL::setPage()
     *
     * Set Current Site Lang function.
     * @return
     */
    public function setPage( $page ) {
        if(($page = intval($page))>0){
            $this->page = $page ;
            return true;
        } return false;
    }

    /**
     * UrlWL::getPage()
     *
     * Get Current Page number function.
     * @return
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * UrlWL::getBaseUrl()
     *
     * Get Current Base site url function.
     * @return
     */
    public function getBaseUrl($protocol='https://', $setSep=false) {
        return $protocol.$this->base.($setSep ? '/' : '');
    }
    /**
     * UrlWL::getBreadCrumbs()
     *
     * Get array Bread Crumbs function. 
     * @return
     */
    public function getBreadCrumbs() {
        return $this->arBreadCrumb;
    }

    /**
     * UrlWL::addToBreadCrumbs()
     *
     * Add item array to Bread Crumbs Array function. 
     * @return
     */
    public function addToBreadCrumbs($url, $title) {
        $this->arBreadCrumb = self::addBreadCrumbs($this->arBreadCrumb, $title, $url);
    }

    /**
     * UrlWL::addBreadCrumbs()
     *
     * Add item array to Bread Crumbs Array function. 
     * @param array $arBreadCrumb
     * @param type $title
     * @param type $url
     * @return array
     */
    /**
     * 
     */
    public static function addBreadCrumbs(array $arBreadCrumb, $title, $url) {
        if($title){
            $arBreadCrumb[$url] = $title;
        }
        return $arBreadCrumb;
    }

    /**
     * UrlWL::addToBreadCrumbs()
     *
     * Add item to Bread Crumbs Array function at first position. 
     * @return $this
     */
    public function unShiftToBreadCrumbs($title, $url) {
        if($title){
            $arr = array($url => $title);
            foreach($this->arBreadCrumb as $key=>$val){
                $arr[$key] = $val;
            }
            $this->arBreadCrumb = $arr;
        }
    }

    /**
     * UrlWL::getCategoryParentId()
     *
     * Get Current Category Parent ID function.
     * @return int
     */
    public function getCategoryParentId() {
        return $this->parentID===null ? 0 : $this->parentID;
    }

    /**
     * UrlWL::setCategoryParentId()
     *
     * Set Current Category Parent ID function.
     */
    public function setCategoryParentId($pid) {
        $this->parentID = intval($pid);
    }

    /**
     * UrlWL::getCategoryId()
     *
     * Get Current Category ID function.
     * @return int
     */
    public function getCategoryId() {
        return $this->categoryID===null ? self::HOME_CATID : $this->categoryID;
    }

    /**
     * UrlWL::setCategoryId()
     *
     * Set Current Category ID function.
     */
    public function setCategoryId($catid) {
        $this->categoryID = $catid>0 ? intval($catid) : null;
    }

    /**
     * UrlWL::getCategoryNavPath()
     *
     * Get Current Category Navigation Path Array function.
     * @return int
     */
    public function getCategoryNavPath() {
        return $this->arCatPath;
    }

    /**
     * UrlWL::addToCategoryNavPath()
     *
     * Add string to Category Navigation Path Array function.
     * @return int
     */
    public function addToCategoryNavPath($seo_path) {
        $seo_path = trim($seo_path);
        if($seo_path!==''){
            $this->arCatPath[] = $seo_path;
        }
    }

    /**
     * UrlWL::unShiftToCategoryNavPath()
     *
     * Add string to begin Category Navigation Path Array function.
     * @return int
     */
    public function unShiftToCategoryNavPath($seo_path) {
        $seo_path = trim($seo_path);
        if($seo_path!='') array_unshift($this->arCatPath, $seo_path);
    }

    /**
     * UrlWL::getNavPath()
     *
     * Get Current Navigation Path Array function.
     * @return int
     */
    private function getNavPath() {
        return $this->arNavPath;
    }

    /**
     * UrlWL::getNavPath()
     *
     * Get Current Navigation Path Array function.
     * @return int
     */
    private function setNavPath(array $arPath = array()) {
        $this->arNavPath = $arPath;
    }

    /**
     * UrlWL::addToNavPath()
     *
     * Add string to Navigation Path Array function.
     * @return int
     */
    private function addToNavPath($seo_path) {
        $seo_path = trim($seo_path);
        if($seo_path!==''){
            $this->arNavPath[] = $seo_path;
        }
    }

    /**
     * UrlWL::unShiftToNavPath()
     *
     * Add string to begin Navigation Path Array function.
     * @return int
     */
    private function unShiftToNavPath($seo_path) {
        $seo_path = trim($seo_path);
        if($seo_path!=''){
            array_unshift($this->arNavPath, $seo_path);
        }
    }

    /**
     * UrlWL::_unSetPathItem()
     *
     * UnSet Path Item from $arr Array
     * @return array
     */
    private static function _unSetPathItem(array $arr, $path) {
        if(($path = trim($path)) !== ''){
            $ar = array_values($arr); $arr = array();
            for ($i = 0; $i < count($ar); $i++) {
                if($ar[$i] != $path){
                    $arr[] = $ar[$i];
                }
            }
        }
        return $arr;
    }

    /**
     * UrlWL::unSetPathItem()
     *
     * UnSet Path Item from $arPath Array
     */
    public function unSetPathItem($path, $idx=null, $reindex=true) {
        if($reindex){
            $this->arPath = self::_unSetPathItem($this->arPath, $path);
        } elseif($idx !== null and isset($this->arPath[$idx])){
            unset($this->arPath[$idx]);
        } elseif(($path = trim($path)) !== ''){
            foreach($this->arPath as $idx => $part){
                if($part == $path){
                    unset($this->arPath[$idx]);
                }
            }
        }
    }

    /**
     * UrlWL::unSetCategoryNavPathItem()
     *
     * UnSet Path Item from $arPath Array
     * @return bool
     */
    public function unSetCategoryNavPathItem($path) {
        $this->arCatPath = self::_unSetPathItem($this->arCatPath, $path);
    }

    /**
     * UrlWL::unSetNavPathItem()
     *
     * UnSet Navigation Path Item from $arNavPath Array
     * @return bool
     */
    public function unSetNavPathItem($path) {
        $this->arNavPath = self::_unSetPathItem($this->arNavPath, $path);
    }

    /**
     * UrlWL::getItemId()
     *
     * Get Current Item ID function.
     * @return int
     */
    public function getItemId() {
        return $this->itemID===null ? 0 : $this->itemID;
    }

    /**
     * UrlWL::getAssortId()
     *
     * Get Current Assort Item ID function.
     * @return int
     */
    public function getAssortId() {
        return $this->assortID===null ? 0 : $this->assortID;
    }

    /**
     * UrlWL::getPageNumber()
     *
     * Get Current Page Number function.
     * @return int
     */
    public function getPageNumber() {
        return $this->page===null ? 1 : $this->page;
    }

    /**
     * UrlWL::getAjaxMode()
     *
     * Get Current Page Ajax Mode status value function.
     * @return bool
     */
    public function getAjaxMode() {
        return empty($this->ajax) ? 0 : 1;
    }

    /**
     * UrlWL::getModuleName()
     *
     * Get Current Category Module Name function.
     * @return String
     */
    public function getModuleName() {
        return $this->module===null ? false : $this->module;
    }

    /**
     * UrlWL::getFilters()
     *
     * Get Filters objects function.
     * @return \UrlFilters
     */
    public function getFilters() {
        return $this->Filters;
    }

    /**
     * UrlWL::copyFilters()
     *
     * Get copied Filters objects function.
     * @return \UrlFilters
     */
    public function copyFilters() {
        return $this->Filters->copy();
    }

    /**
     * UrlWL::setFilters()
     *
     * Set Filters objects function.
     * @return \UrlFilters
     */
    public function setFilters(UrlFilters $Filters) {
        return $this->Filters = $Filters;
    }

    /**
     * UrlWL::resetFilters()
     *
     * Get Filters objects function.
     * @return \UrlWL
     */
    public function resetFilters() {
        $this->Filters->reset();
        return $this;
    }

    /**
     * UrlWL::resetPage()
     *
     * Get Filters objects function.
     * @return \UrlWL
     */
    public function resetPage() {
        $this->setPage(1);
        return $this;
    }

    /**
     * UrlWL::storeLang()
     * 
     * Save Lang Parameter to $_SESSION And $_COOKIE.
     * @param string $langKeyName Lang key in $_SESSION And $_COOKIE
     * @param string $lang Lang value
     * @param int $storePeriod Live period for lang in cookie in seconds Like (24 * 7 * 3600) - for one week
     * @param bool $redirect Redirect to new url
     * @param CCookie $Cookie object
     */
    public function storeLang($langKeyName, $lang, $storePeriod, $redirect, CCookie $Cookie) {
        $_SESSION[$langKeyName] = $lang;
        $Cookie->add($langKeyName, $lang, time() + $storePeriod);
        if($redirect){ Redirect('/'.ltrim($this->cleanUrlFromLangs(), '/')); }
        else $Cookie->setCookie($langKeyName, $lang); //Set Cookie Directly for use in this server session
    }
}

/**
 * Description of UrlFilters class
 * This class provides methods for create and manage SEO Filters in UrlWL.
 * @author WebLife
 * @copyright 2015
 */
class UrlFilters {

    /**
     * brand id
     * @var int
     */
    protected $brandID;

    /**
     * category id
     * @var int
     */
    protected $categoryID;

    /**
     * filter type
     * @var int
     */
    protected $filterType;

    /**
     * show filters method
     * @var int
     */
    protected $showType;

    /**
     * selected filters where key = filterID, value = attribute values where key is attributeID and value is attributeAlias
     * @var array
     */
    protected $selected;

    /**
     * category filters id array for seo url
     * @var array
     */
    protected $categoryFilters;

    /**
     * mode type fo ranges
     * @var bool
     */
    protected $maskRanges;
    
    // все фильтры
    const SHOW_ALL = 1;
    // важные фильтры
    const SHOW_IMPORTANT = 2;
    // не важные фильтры
    const SHOW_UNIMPORTANT = 3;

    // типы фильтров
    const TYPE_BRAND = 1; //Производитель
    const TYPE_PRICE = 2; //Цена
    const TYPE_TEXT = 3; //Текстовый
    const TYPE_NUMBER = 4; //Числовой
    const TYPE_COLOR = 5; //Цвет
    const TYPE_CATEGORY = 6; //Категория
    
    // типы списков фильтров
    const LIST_TYPE_DEFAULT = 1;
    const LIST_TYPE_SEO = 2;

    // keys
    const KEY_URL_CASE = 'filter';
    const KEY_CATEGORY = 'category';
    const KEY_BRAND = 'brand';
    const KEY_SELECTED = 'selected';
    const KEY_FIILTER_TYPE = 'type';
    const KEY_SHOW_TYPE = 'show';

    /**
     * @param array $data
     */
    public function __construct($data=array()) {
        $this->init($data);
    }
    
    /**
     * независимые от атрибутов фильтра
     */    
    public static function isIndependentFilter($typeID) {
        return in_array($typeID, array(self::TYPE_BRAND, self::TYPE_PRICE, self::TYPE_COLOR));
    }
    
    /**
     * get new Instance
     * @param array $data
     * @return UrlFilters
     */
    public static function getNewInstance($data=array()) {
        $instance = new UrlFilters($data);
        return $instance;
    } 
    /**
     *
     * @param array $data
     * @return \UrlFilters
     */
    protected function parse(array $data=array()) {
        foreach($data as $key=>$val){
            switch ($key) {
                case self::KEY_CATEGORY:
                    $this->setCategoryID($val);
                    break;
                case self::KEY_BRAND:
                    $this->setBrandID($val);
                    break;
                case self::KEY_FIILTER_TYPE:
                    $this->setFilterType($val);
                    break;
                case self::KEY_SHOW_TYPE:
                    $this->setShowType($val);
                    break;
                case self::KEY_SELECTED:
                    $this->setSelected($val);
                    break;
                default:
                    break;
            }
        }
        return $this;
    }
    /**
     * init from data array
     * @param array $data
     * @return \UrlFilters
     */
    public function init($data=array()) {
        $this->reset();
        if(is_array($data)) {
            $this->parse($data);
        }
        return $this;
    }
    /**
     * @return \UrlFilters
     */
    public function copy() {
        $cloned = clone $this;
        return $cloned;
    }
    /**
     * set to default
     * @return \UrlFilters
     */
    public function reset() {
        $this->brandID = $this->categoryID = $this->filterType = $this->showType = 0;
        $this->selected = $this->categoryFilters = array();
        $this->maskRanges = false;
        return $this;
    }
    /**
     * @return int
     */
    public function getBrandID() {
        return $this->brandID;
    }
    /**
     * @return int
     */
    public function getCategoryID() {
        return $this->categoryID;
    }
    /**
     * @return int
     */
    public function getFilterType() {
        return $this->filterType;
    }
    /**
     * @return int
     */
    public function getShowType() {
        return $this->showType;
    }
    /**
     * @return array
     */
    public function getSelected() {
        return $this->selected;
    }
    /**
     * @return array
     */
    public function getSelectedTitles() {
        $selected = array();
        foreach($this->sortSelected()->selected as $key=>$val){
            if(isset($this->categoryFilters[$key])){
                $title = $this->categoryFilters[$key]['title'];
                if(isset($this->selected[$key][UrlFiltersRange::KEY_MIN]) or isset($this->selected[$key][UrlFiltersRange::KEY_MAX])){
                    $min = isset($this->selected[$key][UrlFiltersRange::KEY_MIN]) ? $this->selected[$key][UrlFiltersRange::KEY_MIN] : 0;
                    $max = isset($this->selected[$key][UrlFiltersRange::KEY_MAX]) ? $this->selected[$key][UrlFiltersRange::KEY_MAX] : 0;
                    $title .= ' '.LABEL_FROM.' '.$min.($max ? ' '.LABEL_TO.' ' . $max: '');
                } else {
                    $alias = reset($val);
                    foreach($this->categoryFilters[$key]['values'] as $value){
                        if($value['alias'] == $alias){
                            $title = $value['title'];
                            break;
                        }
                    }
                }
                $selected[$key] = $title;
            }
        }
        return $selected;
    }
    /**
     * @return array
     */
    public function getCategoryFilters() {
        return $this->categoryFilters;
    }
    /**
     * @param int $brandID
     * @return \UrlFilters
     */
    public function setBrandID($brandID) {
        $this->brandID = intval($brandID);
    }
    /**
     * @param int $categoryID
     * @return \UrlFilters
     */
    public function setCategoryID($categoryID) {
        $this->categoryID = intval($categoryID);
    }
    /**
     * @param int $filterType
     * @return \UrlFilters
     */
    public function setFilterType($filterType) {
        $this->filterType = $filterType;
    }
    /**
     * @param int $showType
     * @return \UrlFilters
     */
    public function setShowType($showType) {
        $this->showType = $showType;
        return $this;
    }
    /**
     * @param array $selected
     * @return \UrlFilters
     */
    public function setSelected($selected) {
        $this->selected = ($selected and is_array($selected)) ? $selected : array();
        return $this;
    }
    /**
     * @param array $categoryFilters
     * @return \UrlFilters
     */
    public function setCategoryFilters($categoryFilters) {
        $this->categoryFilters = ($categoryFilters and is_array($categoryFilters)) ? $categoryFilters : array();
        return $this;
    }
    /**
     * @param int $categoryFilter
     * @return bool
     */
    public function issetCategoryFilter($categoryFilter) {
        return isset($this->categoryFilters[$categoryFilter]);
    }
    /**
     * @param int $categoryFilter
     * @return \UrlFilters
     */
    public function unsetCategoryFilter($categoryFilter) {
        if($this->issetCategoryFilter($categoryFilter)){
            unset($this->categoryFilters[$categoryFilter]);
        }
        return $this;
    }
     /**
      * @return int
      */
    public function countSelectedFilters() {
        return count($this->selected);
    }    
     /**
      * @return int
      */
    public function countSelectedFilterAttributes() {
        $cnt = 0;
        foreach ($this->selected as $val) {
            if(isset($val[UrlFiltersRange::KEY_MIN]) or isset($val[UrlFiltersRange::KEY_MAX])){
                $cnt++;
            } else {
                $cnt += count($val);
            }
        }
        return $cnt;
    }    
    /**
     * @param int $filterID
     * @param array $selected
     * @return bool
     */
    private static function issetSelectedFilter($filterID, array $selected) {
        return isset($selected[$filterID]);
    }
    /**
     * @param int $filterID
     * @return array
     */
    public function getFilter($filterID, $defval=array()) {
        return isset($this->selected[$filterID]) ? $this->selected[$filterID] : $defval;
    }
    /**
     * @param int $filterID
     * @return bool
     */
    public function issetFilter($filterID) {
        return isset($this->selected[$filterID]);
    }
    /**
     * @param int $filterID
     * @param mixed $attributeAlias int or string
     * @param array $selected
     * @return bool
     */
    private static function issetSelectedFilterAttribute($filterID, $attributeAlias, array $selected) {
        return (isset($selected[$filterID]) and in_array($attributeAlias, $selected[$filterID]));
    }
    /**
     * @param int $filterID
     * @param mixed $attributeAlias int or string
     * @return bool
     */
    public function issetAttribute($filterID, $attributeAlias) {
        return (isset($this->selected[$filterID]) and in_array($attributeAlias, $this->selected[$filterID]));
    }
    /**
     * @param int $filterID
     * @param mixed $attributeKey int or string
     * @return mixed
     */
    public function getAttribute($filterID, $attributeKey, $defval=null) {
        return isset($this->selected[$filterID][$attributeKey]) ? $this->selected[$filterID][$attributeKey] : $defval;
    }
    /**
     * @param int $filterID
     * @return int
     */
    public function countAttributes($filterID) {
        return isset($this->selected[$filterID]) ? count($this->selected[$filterID]) : 0;
    }
    /**
     * @param int $filterID
     * @param mixed $attributeAlias int or string
     * @param mixed $attributeKey int or string
     * @return \UrlFilters
     */
    public function appendAttribute($filterID, $attributeAlias, $attributeKey=FALSE) {
        if($filterID){
            if($attributeKey){
                $this->selected[$filterID][$attributeKey] = $attributeAlias;
            } else if($attributeAlias and (!isset($this->selected[$filterID]) or !in_array($attributeAlias, $this->selected[$filterID]))){
                $this->selected[$filterID][] = $attributeAlias;
            }
        }
        return $this;
    }
    /**
     * @param int $filterID
     * @param mixed $attributeAlias int or string
     * @param mixed $attributeKey int or string
     * @return \UrlFilters
     */
    public function prependAttribute($filterID, $attributeAlias, $attributeKey=FALSE) {
        if($filterID){
            $key = $attributeKey ? $attributeKey : array_search($attributeAlias, $this->selected);
            if($key!==FALSE and isset($this->selected[$filterID][$key])){
                unset($this->selected[$filterID][$key]);
            }
            if(is_numeric($attributeKey)){
                $attributeKey = 0;
            }
            $selected = array($filterID => array($attributeKey => $attributeAlias));
            foreach($this->selected as $key=>$val){
                foreach($val as $k=>$v){
                    if(is_numeric($k)){
                        $selected[$key][]=$v;
                    } else {
                        $selected[$key][$k]=$v;
                    }
                }
            }
            $this->selected = $selected;
        }
        return $this;
    }
    /**
     * @param array $selectedFilters
     * @return \UrlFilters
     */
    public function appendAttributes(array $selectedFilters) {
        foreach($selectedFilters as $key=>$val){
            foreach($val as $k=>$v){
                if(is_numeric($k)){
                    if(!isset($this->selected[$key]) or !in_array($v, $this->selected[$key])){
                        $this->selected[$key][]=$v;
                    }
                } else {
                    $this->selected[$key][$k]=$v;
                }
            }
        }
        return $this;
    }
    /**
     * @param array $selectedFilters
     * @return \UrlFilters
     */
    public function prependAttributes(array $selectedFilters) {
        $selected = array();
        foreach($selectedFilters as $key=>$val){
            foreach($val as $k=>$v){
                if(is_numeric($k)){
                    if(!isset($selected[$key]) or !in_array($v, $selected[$key])){
                        $selected[$key][]=$v;
                    }
                } else {
                    $selected[$key][$k]=$v;
                }
            }
        }
        foreach($this->selected as $key=>$val){
            foreach($val as $k=>$v){
                if(is_numeric($k)){
                    if(!isset($selected[$key]) or !in_array($v, $selected[$key])){
                        $selected[$key][]=$v;
                    }
                } else if(!isset($selected[$key][$k])){
                    $selected[$key][$k]=$v;
                }
            }
        }
        $this->selected = $selected;
        return $this;
    }
    /**
     * @param int $filterID
     * @return \UrlFilters
     */
    public function removeFilter($filterID) {
        if(isset($this->selected[$filterID])){
            unset($this->selected[$filterID]);
        }
        return $this;
    }
    /**
     * @param int $filterID
     * @param mixed $attributeAlias int or string
     * @param mixed $attributeKey int or string
     * @return \UrlFilters
     */
    public function removeAttribute($filterID, $attributeAlias, $attributeKey=FALSE) {
        if($filterID and isset($this->selected[$filterID])){
            $key = $attributeKey ? $attributeKey : array_search($attributeAlias, $this->selected[$filterID]);
            if($key!==FALSE and isset($this->selected[$filterID][$key])){
                if(count($this->selected[$filterID]) > 1){
                    unset($this->selected[$filterID][$key]);
                    if(!isset($this->selected[$filterID][UrlFiltersRange::KEY_MIN]) and !isset($this->selected[$filterID][UrlFiltersRange::KEY_MAX])){
                        $this->selected[$filterID] = array_values($this->selected[$filterID]);
                    }
                } else {
                    unset($this->selected[$filterID]);
                }
            }
        }
        return $this;
    }
    /**
     * @return \UrlFilters
     */
    public function removeAttributes() {
        $this->selected = array();
        return $this;
    }
    /**
     * @return \UrlFilters
     */
    public function setMaskRangesOn() {
        $this->maskRanges = true;
        return $this;
    }
    /**
     * @return \UrlFilters
     */
    public function setMaskRangesOff() {
        $this->maskRanges = false;
        return $this;
    }
    /**
     * @return \UrlFilters
     */
    public function sortSelected() {
        $selected = array();
        foreach(array_keys($this->categoryFilters) as $filterID){
            if(isset($this->selected[$filterID])){
                $selected[$filterID] = $this->selected[$filterID];
                unset($this->selected[$filterID]);
            }
        }
        foreach(array_keys($this->selected) as $filterID){
            $selected[$filterID] = $this->selected[$filterID];
        }
        $this->selected = $selected;
        return $this;
    }
    /**
     * @return \UrlFilters
     */
    public function slicePath() {
        $arPath = array();
        foreach($this->sortSelected()->selected as $key=>$val){
            if(isset($this->categoryFilters[$key])){
                if(isset($this->selected[$key][UrlFiltersRange::KEY_MIN]) or isset($this->selected[$key][UrlFiltersRange::KEY_MAX])){
                    $prefix = '';
                    switch($this->categoryFilters[$key]['tid']){
                        case self::TYPE_PRICE:
                            $prefix = UrlFiltersRange::SEO_HAND_PRICE;
                            break;
                        case self::TYPE_NUMBER:
                            $prefix = UrlFiltersRange::SEO_HAND_RANGE;
                            break;
                        default:
                            break;
                    }
                    if($prefix){
                        if($this->maskRanges){
                            $arPath[] = UrlFiltersRange::maskSeoPath($prefix, $key);
                        } else {
                            $min = isset($this->selected[$key][UrlFiltersRange::KEY_MIN]) ? $this->selected[$key][UrlFiltersRange::KEY_MIN] : 0;
                            $max = isset($this->selected[$key][UrlFiltersRange::KEY_MAX]) ? $this->selected[$key][UrlFiltersRange::KEY_MAX] : 0;
                            $arPath[] = UrlFiltersRange::generateSeoPath($prefix, $key, $min, $max);
                        }
                        $this->removeFilter($key);
                    }
                } else {
                    $alias = reset($val);
                    foreach($this->categoryFilters[$key]['values'] as $value){
                        if($value['alias'] == $alias){
                            $arPath[] = $value['seo_path'];
                            $this->removeAttribute($key, $alias);
                            break;
                        }
                    }
                }
            }
        }
        return $arPath;
    }
    /**
     * @return array
     */
    public function toUrlParams() {
        return array(self::KEY_URL_CASE => $this->toArray());
    }
    /**
     * @return array
     */
    public function toArray() {
        $data = array();
        if($this->getCategoryID()){
            $data[self::KEY_CATEGORY] = $this->getCategoryID();
        }
        if($this->getBrandID()){
            $data[self::KEY_BRAND] = $this->getBrandID();
        }
        if($this->getFilterType()){
            $data[self::KEY_FIILTER_TYPE] = $this->getFilterType();
        }
        if($this->getShowType()){
            $data[self::KEY_SHOW_TYPE] = $this->getShowType();
        }
        if($this->getSelected()){
            $selected = $this->sortSelected()->getSelected();
            if($this->maskRanges){
                foreach($selected as $key => &$val){
                    if(isset($val[UrlFiltersRange::KEY_MIN]) or isset($val[UrlFiltersRange::KEY_MAX])){
                        $val[UrlFiltersRange::KEY_MIN] = UrlFiltersRange::maskKey(UrlFiltersRange::KEY_MIN);
                        $val[UrlFiltersRange::KEY_MAX] = UrlFiltersRange::maskKey(UrlFiltersRange::KEY_MAX);
                    }
                }
            }
            $data[self::KEY_SELECTED] = $selected;
        }
        return $data;
    }
}



/**
 * Description of UrlFiltersRange class
 * This class provides methods for create and manage SEO Filters Ranges in UrlWL.
 * @author WebLife
 * @copyright 2015
 */
class UrlFiltersRange {
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $min;
    /**
     * @var int
     */
    public $max;
    /**
     * @var int
     */
    public $type;
    /**
     * @var bool
     */
    public $auto;
    /**
     * @var string
     */
    public $prefix;
    
    // keys
    const KEY_MIN = 'min';
    const KEY_MAX = 'max';
    const KEY_SEP_MAX = 'maxsep';
    
    // url seo replacements
    const SEO_SEP = '-';
    const SEO_SIGN = '_';
    const SEO_AUTO_RANGE = 'range';
    const SEO_AUTO_PRICE = 'price';
    const SEO_HAND_RANGE = 'handrange';
    const SEO_HAND_PRICE = 'handprice';
    
    /**
     * @return string
     */
    public function generate() {
        return self::generateSeoPath($this->prefix, $this->id, $this->min, $this->max);
    }
    /**
     * @param string $seopath
     * @return \UrlFiltersRange
     */
    public function parse($seopath) {
        return self::parseSeoPath($seopath, $this);
    }
    /**
     * @param string $seopath
     * @return bool
     */
    public function check($seopath) {
        return self::checkSeoPath($seopath);
    }
    /**
     * 
     * @param string $key
     * @return string
     */
    public static function maskKey($key) {
        return '{'.$key.'}';
    }
    /**
     * 
     * @param string $max
     * @return string
     */
    public static function generateMaxPart($max) {
        return self::SEO_SEP.$max;
    }
    /**
     * 
     * @param string $prefix
     * @param int $id
     * @return string
     */
    public static function maskSeoPath($prefix, $id) {
        return $prefix.$id.self::SEO_SIGN.self::maskKey(self::KEY_MIN).self::generateMaxPart(self::maskKey(self::KEY_MAX));
    }
    /**
     * 
     * @param string $prefix
     * @param int $id
     * @param float $min
     * @param float $max
     * @return string
     */
    public static function generateSeoPath($prefix, $id, $min, $max) {
        return $prefix.$id.self::SEO_SIGN.intval($min).(($max = intval($max))>0 ? self::generateMaxPart($max) : '');
    }
    /**
     * @param string $seopath
     * @param \UrlFiltersRange $Range
     * @return \UrlFiltersRange
     */
    public static function parseSeoPath($seopath, self $Range=null) {
        if($seopath){
            foreach(array(self::SEO_AUTO_RANGE=>UrlFilters::TYPE_NUMBER, self::SEO_HAND_RANGE=>UrlFilters::TYPE_NUMBER, self::SEO_AUTO_PRICE=>UrlFilters::TYPE_PRICE, self::SEO_HAND_PRICE=>UrlFilters::TYPE_PRICE) as $prefix=>$type){
                $matches = array();
                if(preg_match('/^'.$prefix.'(\d+)'.self::SEO_SIGN.'(\d+)'.self::SEO_SEP.'?(\d*)$/i', $seopath, $matches)){
                    ($Range instanceof self) OR $Range = new self();
                    $Range->id = intval($matches[1]);
                    $Range->min = intval($matches[2]);
                    $Range->max = intval($matches[3]);
                    $Range->type = $type;
                    $Range->auto = ($prefix==self::SEO_AUTO_RANGE or $prefix==self::SEO_AUTO_PRICE);
                    $Range->prefix = $prefix;
                    return $Range;
                }
            }
        }
        return null;
    }
    /**
     * @param string $seopath
     * @return bool
     */
    public static function checkSeoPath($seopath) {
        return (self::parseSeoPath($seopath) instanceof self);
    }
}

/**
 * Description of BreadcrumbsTemplates class
 * This class replaces part of breadcrumb with filters to patterned 
 * @author WebLife
 * @copyright 2018
 */
class BreadcrumbsTemplates {
    
    // aliases
    const ALIAS_SEX = 'sex';
    const ALIAS_TYPE = 'type';    
    const ALIAS_CATEGORY = 'category';    
    
    private $arUrls;
    private $arAliases;
    
    public static function getAliases() {
        return array(
            self::ALIAS_SEX      => 'пол',
            self::ALIAS_TYPE     => 'тип',
            self::ALIAS_CATEGORY => 'категория',
        );
    }
    
    public function addUrl($url) {
        $this->arUrls[] = $url;
    }
    
    public function addAlias($title, $data, $part) {        
        $this->arAliases[$data['alias']] = array(
            't' => $title, 
            's' => $data['values'][$part]['title_single'] ? $data['values'][$part]['title_single'] : $title, 
            'm' => $data['values'][$part]['title_multi'] ? $data['values'][$part]['title_multi'] : $title,
        );  
    }
    
    public function prepare(&$breadcrumbs) {
        $arTemplates = $this->getTemplates();
        // перехерачиваем бредкрабы
        foreach ($this->arUrls as $step => $url) {
            if(isset($arTemplates[$step]) && isset($breadcrumbs[$url])) {
                $breadcrumbs[$url] = $arTemplates[$step];
            }
        }
    } 
    
    public function getCode($sep) {
        $code = '';
        foreach(self::getAliases() as $alias => $name) {
            if(isset($this->arAliases[$alias])) {
                $code .= ($code ? $sep : '').$alias;
            }
        }  
        return $code;
    }
        
    public function getTemplates($sep = '|') {        
        $arTemplates = array();
        $code = $this->getCode($sep);
        switch ($code) {
            case self::ALIAS_SEX:
                $arTemplates = array(
                    //'{Sex} одежда',
                    0 => ucfirst($this->arAliases[self::ALIAS_SEX]['s']).' одежда', 
                );
                break;
            case self::ALIAS_TYPE:
                $arTemplates = array(
                    //'{Type} c принтами',
                    0 => ucfirst($this->arAliases[self::ALIAS_TYPE]['m']).' с принтами',
                );
                break;
            case self::ALIAS_CATEGORY:
                $arTemplates = array(
                    //'Одежда {category}',
                    0 => 'Одежда '.$this->arAliases[self::ALIAS_CATEGORY]['t'],
                );
                break;
            
            case self::ALIAS_SEX.$sep.self::ALIAS_TYPE:
                $arTemplates = array(
                    //'{Type} c принтами',
                    0 => ucfirst($this->arAliases[self::ALIAS_TYPE]['m']).' с принтами',
                    //'{Sex} {type}'
                    1 => ucfirst($this->arAliases[self::ALIAS_SEX]['m']).' '.mb_strtolower($this->arAliases[self::ALIAS_TYPE]['m']),
                );   
                break;
            case self::ALIAS_SEX.$sep.self::ALIAS_CATEGORY:
                $arTemplates = array(
                    //'{Sex} одежда'
                    0 => ucfirst($this->arAliases[self::ALIAS_SEX]['s']).' одежда',
                    //'{Sex} одежда {category}'
                    1 => ucfirst($this->arAliases[self::ALIAS_SEX]['s']).' одежда '.mb_strtolower($this->arAliases[self::ALIAS_CATEGORY]['t']),
                );
                break;
            case self::ALIAS_TYPE.$sep.self::ALIAS_CATEGORY: 
                $arTemplates = array(
                    //'{Type} c принтами'
                    0 => ucfirst($this->arAliases[self::ALIAS_TYPE]['m']).' c принтами',
                    //'{Type} {category}'
                    1 => ucfirst($this->arAliases[self::ALIAS_TYPE]['m']).' '.$this->arAliases[self::ALIAS_CATEGORY]['t'],
                );
                break;
            
            case self::ALIAS_SEX.$sep.self::ALIAS_TYPE.$sep.self::ALIAS_CATEGORY:
                $arTemplates = array(
                    //'{Type} c принтами'
                    0 => ucfirst($this->arAliases[self::ALIAS_TYPE]['m']).' c принтами',
                    //'{Sex} {type}'
                    1 => ucfirst($this->arAliases[self::ALIAS_SEX]['m']).' '.mb_strtolower($this->arAliases[self::ALIAS_TYPE]['m']),
                    //'{Sex} {type} {category}'
                    2 => ucfirst($this->arAliases[self::ALIAS_SEX]['m']).' '.mb_strtolower($this->arAliases[self::ALIAS_TYPE]['m']).' '.$this->arAliases[self::ALIAS_CATEGORY]['t'],
                );  
                break;
        }
        return $arTemplates;
    }
}