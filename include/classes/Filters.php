<?php defined('WEBlife') or die('Restricted access'); // no direct access

/**
 * WEBlife CMS
 * Created on 05.01.2011, 12:20:17
 * Developed by http://weblife.ua/
 */

require dirname(__FILE__).DS."Filters/FilterQuery.php";
require dirname(__FILE__).DS."Filters/FilterElement.php";

use Filters\FilterQuery;
use Filters\FilterElement;
/**
 * relation division http://www.sql-tutorial.ru/ru/book_relational_division.html
 */
abstract class Filters {

    const OPERATOR_AND = 'AND';
    const OPERATOR_OR = 'OR';
    const OPERATOR_PLUS = '+';
    const OPERATOR_MINUS = '-';
    const WORDS_SEPARATOR = ' ';
    const MAX_FILTERS_IN_BLOCK = 8;

    // UrlWL
    protected $UrlWL = null;
    // category id
    private $categoryID = 0;
    // brand id
    protected $brandID = 0;
    // children cats idx
    protected $categoriesIdSet = array();
    // filtered children cats idx
    private $_filterSubCategories = array();
    // show only preset products
    private $productsIdSet = array();
    // Index table
    protected $_table = null;
    // UrlWl for url generate
    private $_UrlWL = null;
    // Filter rows
    private $_rows = null;
    // Ranges rows
    private $_ranges = array();
    // show only available products
    private $onlyAvailable = false;
    // separateByType
    protected $separateByType = false;
    // separateByColor
    protected $separateByColor = false;
    
    private $count = 0;
    private $minPrice = 0;
    private $maxPrice = 0;
    private $total = 0;
    private $totalMinPrice = 0;
    private $totalMaxPrice = 0;
    
    /**
     * 
     * @param UrlWL $UrlWL
     * @param int $categoryID     
     */
    public function __construct($UrlWL = null, $categoryID = 0) {
        if(!$categoryID || !($UrlWL instanceof UrlWL)) die('Нет категории и урлов - нет фильтров!');  
        $this->UrlWL  = $UrlWL;
        $this->categoryID  = $categoryID;
        $this->setIndexTable();
    }
    
    abstract protected function setIndexTable();
    abstract protected function getModuleCategoryID();
    abstract protected function getSelectColumn();
    abstract protected function getGroupColumn($tid=null);
    
    protected function getProductColumn() {
        return 'product_id';
    }
    
    private function initRows() {
        if($this->_rows === null){
            $this->_rows = getRowItemsInKey('id', FILTERS_TABLE . ' f ' . PHP_EOL
                 . 'LEFT JOIN `'.ATTRIBUTES_TABLE.'` a ON a.`id`=f.`aid`' . PHP_EOL
                 . 'LEFT JOIN `'.ATTRIBUTE_GROUPS_TABLE.'` ag ON ag.`id`=a.`gid`' . PHP_EOL
            , 'f.*', 'WHERE f.`aid`=0 OR ag.`active`=1');
        }
        return $this;
    }
    
    public function getRows() {
        return $this->initRows()->_rows;
    }
    
    public function getRow($filterID) {
        return isset($this->initRows()->_rows[$filterID]) ? $this->_rows[$filterID] : null;
    }
    
    private function initRanges($filterID) {
        if(!array_key_exists($filterID, $this->_ranges)){
            $this->_ranges[$filterID] = getRowItemsInKey('id', RANGES_TABLE, '*', 'WHERE `fid`='.intval($filterID), 'ORDER BY `order`');
        }
        return $this;
    }
    
    public function getRanges($filterID, array $rangesIdSet = null) {
        $this->initRanges($filterID);
        if($rangesIdSet === null) {
            return $this->_ranges[$filterID];
        } else {
            $ranges = array();
            foreach ($rangesIdSet as $rangeID) {
                if(isset($this->_ranges[$filterID][$rangeID])){
                    $ranges[$rangeID] = $this->_ranges[$filterID][$rangeID];
                }
            } return $ranges;
        }
    }
    
    public function getRange($filterID, $rangeID) {
        return isset($this->initRanges($filterID)->_ranges[$filterID][$rangeID]) ? $this->_ranges[$filterID][$rangeID] : null;
    }
    
    public function getOnlyAvailable() {
        return $this->onlyAvailable;
    }

    public function setOnlyAvailable($onlyAvailable) {
        $this->onlyAvailable = $onlyAvailable;
        return $this;
    }
    
    public function getCount() {
        return $this->count;
    }

    public function getMinPrice() {
        return $this->minPrice;
    }

    public function getMaxPrice() {
        return $this->maxPrice;
    }
    
    public function getTotal() {
        return $this->total;
    }
    
    public function getTotalMinPrice() {
        return $this->totalMinPrice;
    }

    public function getTotalMaxPrice() {
        return $this->totalMaxPrice;
    }
    
    public function getSeparateByColor() {
        return $this->separateByColor;
    }
    
    public function getSeparateByType() {
        return $this->separateByType;
    }
    /**
     * @return self
     */
    public function init(array $productsIdSet = null, $logQuery = WLCMS_IS_DEV) {
        $this->productsIdSet = $productsIdSet;
        $pricedSelectedFilters = null;
        $selectedFilters = $this->UrlWL->getFilters()->getSelected();
        foreach (array_keys($selectedFilters) as $filterID) {
            if(($frow = $this->getRow($filterID))) {
                if ($frow['tid'] == UrlFilters::TYPE_COLOR){
                    $this->separateByColor = true;
                } else if ($frow["separate"] > 0) {
                    $this->separateByType = true;
                } else if ($frow['tid'] == UrlFilters::TYPE_PRICE){
                    $pricedSelectedFilters = $selectedFilters;
                    unset($selectedFilters[$filterID]);
                }
            }
        }
        // получаем данные по количеству и по ценам
        // вначале отправляем запрос без цены даже если ее и нет в фильтрах
        $query  = $this->buildCountQuery($selectedFilters);
        $result = mysql_query($query);
        if($logQuery) $this->logQuery($query);
        if($result && ($std = mysql_fetch_object($result)) && $std->cnt){
            $this->total = $this->count = $std->cnt;
            $this->totalMinPrice = $this->minPrice = $std->min;
            $this->totalMaxPrice = $this->maxPrice = $std->max;      
            // Далее если есть фильтрация по ценам - то переопределяем данные
            if($pricedSelectedFilters){
                $query  = $this->buildCountQuery($pricedSelectedFilters);
                $result = mysql_query($query);
                if($logQuery) $this->logQuery($query);
                if($result && ($std = mysql_fetch_object($result)) && $std->cnt){
                    $this->count = $std->cnt;
                    $this->minPrice = $std->min;
                    $this->maxPrice = $std->max;
                }
            }
        } return $this;
    }
    /**
     * @param array $filters
     * @param bool $forCount
     * @param string $alias
     * @return FilterQuery
     */
    public function prepareQuery(array $filters = array(), $forCount = false, $alias = 'ti.') {
        $FQ = new FilterQuery;
        $FQ->having = $FQ->where = $arAttr = $arCats = array();
        if($this->brandID){
            $FQ->where[] = $alias.'`brand_id`='.$this->brandID;
        }
        if($this->productsIdSet) {
            $FQ->where[] = $alias.'`'.$this->getProductColumn().'` IN('.implode(',', $this->productsIdSet).') ';
        }
        if($this->categoriesIdSet){
            $arCats = $this->categoriesIdSet;
        }
        foreach ($filters as $filterID => $fdata) {
            $frow = $this->getRow($filterID);
            if (!empty($frow) && !empty($fdata) && (!$this->UrlWL->getFilters()->getFilterType() || $this->UrlWL->getFilters()->getFilterType() == $frow['tid'])) {
                $ftype = (int)$frow['tid'];
                // filtering by brand
                if(!$this->brandID and $ftype==UrlFilters::TYPE_BRAND) {
                    $FQ->where[] = $alias.'`brand_id` IN('.implode(',', $fdata).')';
                }
                // filtering by category
                if($ftype==UrlFilters::TYPE_CATEGORY) {
                    $arCats = $this->getFilterSubCategories($fdata);
                }
                // filtering by color
                elseif($ftype==UrlFilters::TYPE_COLOR) {
                    $FQ->where[] = $alias.'`color_id` IN('.implode(',', $fdata).')';
                }
                // filtering by price range or min/max price
                elseif ($ftype==UrlFilters::TYPE_PRICE) {
                    // min/max
                    if(isset($fdata[UrlFiltersRange::KEY_MIN]) OR isset($fdata[UrlFiltersRange::KEY_MAX])){
                        $condition = $alias.'`product_price`';
                        if(!empty($fdata[UrlFiltersRange::KEY_MIN]) AND !empty($fdata[UrlFiltersRange::KEY_MAX])) {
                            $condition .= ' BETWEEN '.floatval($fdata[UrlFiltersRange::KEY_MIN]).' AND '.floatval($fdata[UrlFiltersRange::KEY_MAX]);
                        } else if (!empty($fdata[UrlFiltersRange::KEY_MAX])) {
                            $condition .= '<'.floatval($fdata[UrlFiltersRange::KEY_MAX]);
                        } else {
                            $condition .= '>'.floatval($fdata[UrlFiltersRange::KEY_MIN]);
                        }
                        $FQ->where[] = $condition;
                    } else {
                        $ranges = $this->getRanges($filterID, $fdata);
                        // price range
                        if($ranges) {
                            $ranges_conds = array();  
                            foreach ($ranges as $range) {
                                $condition = $alias.'`product_price`';
                                if($range['vmin'] > 0 AND $range['vmax'] > 0) {
                                    $condition .= ' BETWEEN '.floatval($range['vmin']).' AND '.floatval($range['vmax']);
                                } else if ($range['vmax'] > 0) {
                                    $condition .= '<'.floatval($range['vmax']);
                                } else {
                                    $condition .= '>'.floatval($range['vmin']);
                                }
                                $ranges_conds[] = $condition;
                            }
                            if(($condition = self::mergeConditions($ranges_conds, self::OPERATOR_OR))) {
                                $FQ->where[] = $condition;
                            }
                        }
                    }
                }
                // filtering by attributes (text or number) without range
                elseif (($ftype==UrlFilters::TYPE_TEXT || $ftype==UrlFilters::TYPE_NUMBER) AND !empty($frow['aid'])) {
                    $arAttr[] = '('.$alias.'`attribute_id`='.$frow['aid'].' AND '.$alias.'`value_id` IN('.implode(',', $fdata).'))';
                }
            }
        }
        if($arCats) {
            $FQ->where[] = $alias.'`category_id` IN('.implode(',', $arCats).') ';
        }
        if ($arAttr) {
            $FQ->where[] = self::mergeConditions($arAttr, self::OPERATOR_OR, true, $cnt);
            if($cnt > 1){
                $FQ->having[] = 'COUNT(DISTINCT '.$alias.'`attribute_id`)='.$cnt;
            }
        }
        if ($this->onlyAvailable) {
            $FQ->where[] = $alias.'`is_available`=1 ';
        }
        $FQ->where[] = $alias.'`is_deleted`=0';
        
        $FQ->select = $alias.($forCount ? '`'.$this->getSelectColumn().'` `id`' : '*');
        $FQ->where = self::mergeConditions($FQ->where, self::OPERATOR_AND, false);
        $FQ->group = $alias.'`'.$this->getGroupColumn().'`';
        $FQ->having = self::mergeConditions($FQ->having, self::OPERATOR_AND, false);
        return $FQ;
    }
    /**
     * @param array $filters
     * @return string
     */
    protected function buildCountQuery(array $filters) {
        $FQ = $this->prepareQuery($filters, true);
        return 'SELECT /*SQL_NO_CACHE*/ ' . PHP_EOL
             . '     COUNT(DISTINCT t.id) `cnt`, ' . PHP_EOL
             . '     MIN(t.`min`) `min`, ' . PHP_EOL
             . '     MAX(t.`max`) `max` ' . PHP_EOL
             . 'FROM ( ' . PHP_EOL
                   . $FQ->getSelect('     ', ',') . PHP_EOL
             . '          MIN(IF(ti.`product_price`>0 AND ti.`is_available`>0, ti.`product_price`, NULL)) `min`, ' . PHP_EOL
             . '          MAX(IF(ti.`product_price`>0 AND ti.`is_available`>0, ti.`product_price`, NULL)) `max` ' . PHP_EOL
             . '     FROM `' . $this->_table . '` ti ' . PHP_EOL
                   . $FQ->getWhere('     ')
                   . $FQ->getGroup('     ')
                   . $FQ->getHaving('     ')
             . ') t ' . PHP_EOL
        ;
    }
    
    protected function getFilterSubCategories(array $arCats) {
        sort($arCats, SORT_NUMERIC);
        $catkey = implode(',', $arCats);
        if(!array_key_exists($catkey, $this->_filterSubCategories)) {
            $this->_filterSubCategories[$catkey] = array_merge($arCats, getRowItemsValue(MAIN_TABLE, 'id', 'WHERE `pid` IN('.$catkey.') AND `id` NOT IN('.$catkey.')'));
        }
        return $this->_filterSubCategories[$catkey];
    }

    public function getTotalFilteredItems($selectedFilters) {
        $query  = $this->buildCountQuery($selectedFilters, true);
        $result = mysql_query($query);
        if($result && ($std = mysql_fetch_object($result)) && $std->cnt){
            return $std->cnt;
        } return 0;
    }

    /**
     * @param array $conditions
     * @param String $operator
     * @return string
     */
    protected static function mergeConditions(array $conditions = array(), $operator = self::OPERATOR_AND, $group = true, & $cnt = 0) {
        $condition = '';
        if(($cnt = count($conditions))) {
            $condition = implode(' '.$operator.' ', $conditions);
            if($group && $cnt > 1) $condition = '('.$condition.')';
        } return $condition;
    }
    /**
     * @return array
     */
    public function getFilters($strict = false, $types = array()) {
        // обявляем массив с обязательными колонками
        $arFilters = array (
            'childrensCount' => 0,
            'selectedFilters' => $this->UrlWL->getFilters()->getSelected(), // текущие выбранные фильтры
            'selectedCount' => $this->UrlWL->getFilters()->countSelectedFilterAttributes(), // текущее количество выбранных аттрибутов
            'items' => array(),
        );
        // filling the filters items array
        $filtersIdSet = array(); $idx = 0;
        $foundFiltersSet = getRowItemsValue(CATEGORY_FILTERS_TABLE, '`fid`', 'WHERE `type`='.UrlFilters::LIST_TYPE_DEFAULT.(empty($this->categoriesIdSet) ? '' : ' AND `cid` IN ('.implode(',', $this->categoriesIdSet).')').($strict ? " AND `cid`={$this->categoryID} " : ""), 'ORDER BY IF(`cid`='.$this->categoryID.', 1, 0) DESC, `order`', '', 'fid');
        foreach($foundFiltersSet as $filterID) {
            isset($filtersIdSet[$filterID]) OR $filtersIdSet[$filterID] = $idx++;
        }
        foreach ($filtersIdSet as $filterID => $idx) {
            // забираем ранее полученные данные по фильтрам
            $frow = $this->getRow($filterID);
            if ($frow && (!$this->UrlWL->getFilters()->getFilterType() || $this->UrlWL->getFilters()->getFilterType() == $frow['tid']) && (empty($types) || in_array($frow['tid'], $types))) {
                // обявляем нужные дополнительные колонки и переопределяем нужные переменные
                $frow['order'] = $idx + 1;
                $frow['type']  = "undefined";
                $frow['selectedFilters'] = $arFilters['selectedFilters'];
                $frow['selectedCount'] = $arFilters['selectedCount'];
                $frow['isSetFilter'] = false;
                $frow['children'] = array();
                $frow['FE'] = new FilterElement($frow['id'], $frow['tid']);
                // если фильтр выбран - то переопределяем нужные колонки
                if($frow['selectedCount'] > 0 && $this->UrlWL->getFilters()->issetFilter($frow['id'])){
                    $_UrlFilters = $this->UrlWL->getFilters()->copy()->removeFilter($frow['id']);
                    $frow['selectedFilters'] = $_UrlFilters->getSelected();
                    $frow['selectedCount'] = $_UrlFilters->countSelectedFilterAttributes();
                    $frow['isSetFilter'] = true;
                    // очищаем обект для освобождения памяти
                    unset($_UrlFilters); 
                }
                // очищаем лишние данные
                unset($frow['created'], $frow['modified']); 
                // копируем настроенный фильтр
                $arFilters['items'][$frow['id']] = $frow;
            }
        }
        // prepare by type
        $this->prepareFilters($arFilters, $strict);
        // final prepare
        foreach($arFilters['items'] as &$frow) {
            // урл для сброса параметров этого фильтра
            $frow['reset_url'] = '';
            if($frow['FE']->getTotal()) {
                // увеличиваем количество самих фильтров
                $arFilters['childrensCount']++;
                if($frow['type'] != 'price' && $frow['type'] != 'category') {
                    // сортировка по найденным товарам: вначале с товарами - потом без
                    $visible = $hidden = array();                
                    foreach(array_keys($frow['children']) as $alias) {
                        if($frow['children'][$alias]['cnt'] > 0) {
                            $visible[$alias] = &$frow['children'][$alias];
                        } else $hidden[$alias] = &$frow['children'][$alias];
                    }
                    $frow['children'] = $visible + $hidden;
                    // добавляем видимые елементы если их количества не достаточно
                    if($frow['FE']->getInvolved() < self::MAX_FILTERS_IN_BLOCK) {
                        foreach(array_keys($frow['children']) as $alias) {
                            if(!$frow['children'][$alias]['primary']) {
                                $frow['children'][$alias]['primary'] = true; 
                                $frow['FE']->incrementInvolved()->decrementHidden();
                            } 
                            if($frow['FE']->getInvolved() >= self::MAX_FILTERS_IN_BLOCK) break;
                        } 
                    }
                }
            }
            // добавляем количественные переменные
            $frow['involvedCnt'] = $frow['FE']->getInvolved(); 
            $frow['hiddenCnt'] = $frow['FE']->getHidden(); 
            $frow['totalCnt'] = $frow['FE']->getTotal();
            // очищаем обект для освобождения памяти
            unset($frow['FE']);                
        } return $arFilters;
    }
    /**
     * сбор значений фильтров по типам
     * @param array $arFilters
     */
    private function prepareFilters(&$arFilters, $strict, $logQuery = WLCMS_IS_DEV) {
        // prepare base Query
        $baseQuery = $this->prepareQuery();
        // walk by filters
        foreach($arFilters['items'] as &$frow) {
            // определяем нужно ли делать дополнительные подсчеты
            // generate filtered condition exlude current filter
            $filterQuery = $frow['selectedCount'] ? $this->prepareQuery($frow['selectedFilters']) : new FilterQuery;
            // определяем колонку для подсчета
            $joinColumn = $this->getGroupColumn($frow['tid']);
            // for category filter type : Тип соединения - один к одному
            if ($frow['tid'] == UrlFilters::TYPE_CATEGORY) {
                $frow['type'] = 'category';
                $tBaseCount = ($filterQuery->where ? 'IF(COUNT(t.`id`)>0, 1, 0)' : '(1)');
                $query  = 'SELECT /*SQL_NO_CACHE*/ ti.`category_id` `alias`, ti.`category_title` `title`, ti.`category_parent_id` `parent_id`, ' . PHP_EOL
                               . 'ti.`category_parent_title` `parent_title`, '.$tBaseCount.' `cnt` ' . PHP_EOL
                        . 'FROM `'.$this->_table.'` ti ' . PHP_EOL . ($filterQuery->where ? ''
                        . 'LEFT JOIN (' . PHP_EOL
                        . '  SELECT ti.`'.$joinColumn.'` `id` '
                        . '  FROM '.$this->_table.' ti ' . PHP_EOL . $filterQuery->getWhere('  ') . '  GROUP BY ti.`'.$joinColumn.'` ' . PHP_EOL . $filterQuery->getHaving('  ')
                        . ') t ON(t.`id` = ti.`'.$joinColumn.'`)' . PHP_EOL : '')
                        . 'WHERE '.$baseQuery->where . PHP_EOL
                        . "GROUP BY ti.`category_id` ORDER BY ti.`category_title` ASC";
                if($logQuery) $this->logQuery($query);
                $res = mysql_query($query);
                if ($res and mysql_num_rows($res) > 0) {
                    // fill
                    while ($r = mysql_fetch_assoc($res)) {
                        $frow['children'][$r['alias']] = $r;
                    }
                    $this->processFilterCategory($frow, $this->getModuleCategoryID());
                }
            }
            // for brand filter type : Тип соединения - один к одному
            elseif(!$this->brandID and $frow['tid'] == UrlFilters::TYPE_BRAND) {
                $frow['type'] = 'brand';
                $tBaseCount = ($filterQuery->where ? 'IF(COUNT(t.`id`)>0, 1, 0)' : '(1)');
                $query = 'SELECT /*SQL_NO_CACHE*/ ti.`brand_id` as `alias`, ti.`brand_title` as `title`, '.$tBaseCount.' `cnt` ' . PHP_EOL
                    . 'FROM `'.$this->_table.'` ti ' . PHP_EOL . ($filterQuery->where ? ''
                    . 'LEFT JOIN (' . PHP_EOL
                    . '  SELECT ti.`'.$joinColumn.'` `id` '
                    . '  FROM '.$this->_table.' ti ' . PHP_EOL . $filterQuery->getWhere('  ') . '  GROUP BY ti.`'.$joinColumn.'` ' . PHP_EOL . $filterQuery->getHaving('  ')
                    . ') t ON(t.`id` = ti.`'.$joinColumn.'`)' . PHP_EOL : '') 
                    . 'WHERE ti.`brand_id`>0 AND ' . $baseQuery->where . PHP_EOL 
                    . 'GROUP BY ti.`brand_id` ORDER BY ti.`brand_title` ASC ';
                if($logQuery) $this->logQuery($query);
                $res = mysql_query($query);
                if ($res and mysql_num_rows($res) > 0) {
                    while ($r = mysql_fetch_assoc($res)) {
                        $this->prepareFilter($frow['FE']->change($r['alias']), $r, $strict);
                        $frow['children'][$r['alias']] = $r;
                    }
                }
            }
            // for color filter type : Тип соединения - один ко многим
            elseif ($frow['tid'] == UrlFilters::TYPE_COLOR) {
                $frow['type'] = 'color';
                $tBaseCount = ($filterQuery->where ? 'IF(COUNT(t.`id`)>0, 1, 0)' : '(1)');
                $query = 'SELECT /*SQL_NO_CACHE*/ ti.`color_id` as `alias`, ti.`color_title` as `title`, ti.`color_hex` as `hex`, '.$tBaseCount.' `cnt` ' . PHP_EOL
                    . 'FROM `'.$this->_table.'` ti ' . PHP_EOL . ($filterQuery->where ? ''
                    . 'LEFT JOIN (' . PHP_EOL
                    . '  SELECT ti.`'.$joinColumn.'` `id` '
                    . '  FROM '.$this->_table.' ti ' . PHP_EOL . $filterQuery->getWhere('  ') . '  GROUP BY ti.`'.$joinColumn.'` ' . PHP_EOL . $filterQuery->getHaving('  ')
                    . ') t ON(t.`id` = ti.`'.$joinColumn.'`)' . PHP_EOL : '') 
                    . 'WHERE ti.`color_id`>0 AND ' . $baseQuery->where . PHP_EOL 
                    . 'GROUP BY ti.`color_id` ORDER BY ti.`color_title` ASC ';
                if($logQuery) $this->logQuery($query);
                $res = mysql_query($query);
                if($res && mysql_num_rows($res) > 0) {
                    while ($r = mysql_fetch_assoc($res)) {
                        $r["short_title"] = PHPHelper::shortenColorTitle($r["title"], 8);
                        $this->prepareFilter($frow['FE']->change($r['alias']), $r, $strict);
                        $frow['children'][$r['alias']] = $r;
                    }
                }
            }
            // for fixed or range price filter type : Тип соединения - один
            elseif ($frow['tid'] == UrlFilters::TYPE_PRICE) {
                // for range type
                $tBaseCount = ($filterQuery->where ? 'IF(COUNT(t.`id`)>0, 1, 0)' : '(1)');
                $query = 'SELECT /*SQL_NO_CACHE*/ r.`id` as `alias`, r.`title`, '.$tBaseCount.' `cnt` ' . PHP_EOL
                    . 'FROM `'.$this->_table.'` ti ' . PHP_EOL
                    . 'LEFT JOIN `'.RANGES_TABLE.'` r ON r.`fid`='.$frow['id'] . PHP_EOL . ($filterQuery->where ? ''
                    . 'LEFT JOIN (' . PHP_EOL
                    . '  SELECT ti.`'.$joinColumn.'` `id` '
                    . '  FROM '.$this->_table.' ti ' . PHP_EOL . $filterQuery->getWhere('  ') . '  GROUP BY ti.`'.$joinColumn.'` ' . PHP_EOL . $filterQuery->getHaving('  ')
                    . ') t ON(t.`id` = ti.`'.$joinColumn.'`)' . PHP_EOL : '') 
                    . 'WHERE ' . $baseQuery->where . ' AND (r.`vmin`>0 OR r.`vmax`>0) AND ( ' . PHP_EOL
                    . '     (r.`vmin`=0 AND r.`vmax` > ti.`product_price`) ' . PHP_EOL
                    . '      OR ' . PHP_EOL
                    . '     (r.`vmax`=0 AND r.`vmin` <= ti.`product_price`) ' . PHP_EOL
                    . '      OR ' . PHP_EOL
                    . '     (ti.`product_price` BETWEEN r.`vmin` AND r.`vmax`) ' . PHP_EOL
                    . ')' . PHP_EOL 
                    . 'GROUP BY r.`id` ORDER BY r.`order` ASC ';
                if($logQuery) $this->logQuery($query);
                $res = mysql_query($query);
                if ($res and mysql_num_rows($res) > 0) {
                    $frow['type'] = 'range';
                    while ($r = mysql_fetch_assoc($res)) {
                        $this->prepareFilter($frow['FE']->change($r['alias']), $r, $strict);
                        $frow['children'][$r['alias']] = $r;
                    }
                }
                // for fixed type
                else {
                    $frow['type'] = 'price';
                    $r = array(UrlFiltersRange::KEY_MIN => $this->totalMinPrice, UrlFiltersRange::KEY_MAX => $this->totalMaxPrice);
                    $r['cnt'] = $this->count;
                    $this->prepareFilterRange($frow['FE']->change(null, true), $r);
                    $frow['children'] = $r;
                }
            }
            // for attribute filter type : Тип соединения - один ко многим
            elseif ($frow['tid'] == UrlFilters::TYPE_TEXT or $frow['tid'] == UrlFilters::TYPE_NUMBER) {
                $frow['type'] = 'simple';
                $tBaseCount = ($filterQuery->where ? 'IF(COUNT(t.`id`)>0, 1, 0)' : '(1)');
                $query = 'SELECT /*SQL_NO_CACHE*/ ti.`value_id` as `alias`, ti.`value_title` as title, '.$tBaseCount.' `cnt` ' . PHP_EOL
                    . 'FROM `'.$this->_table.'` ti ' . PHP_EOL  . ($filterQuery->where ? ''
                    . 'LEFT JOIN (' . PHP_EOL
                    . '  SELECT ti.`'.$joinColumn.'` `id` ' . PHP_EOL
                    . '  FROM '.$this->_table.' ti ' . PHP_EOL . $filterQuery->getWhere('  ') . '  GROUP BY ti.`'.$joinColumn.'` ' . PHP_EOL . $filterQuery->getHaving('  ')
                    . ') t ON(t.`id` = ti.`'.$joinColumn.'`)' . PHP_EOL : '')
                    . 'WHERE ti.`value_id`>0 AND ti.`attribute_id` = '.$frow['aid'] . ' AND ' . $baseQuery->where . PHP_EOL
                    . 'GROUP BY ti.`value_id` ORDER BY ti.`value_title` ASC ';
                if($logQuery) $this->logQuery($query);
                $res = mysql_query($query);
                if($res && mysql_num_rows($res) > 0) {
                    while ($r = mysql_fetch_assoc($res)) {
                        $this->prepareFilter($frow['FE']->change($r['alias']), $r, $strict);
                        $frow['children'][$r['alias']] = $r;
                    }
                }
            }        
        }
        if($logQuery) $this->logQuery('');
    }
    
    private function processFilterCategory (& $frow, $moduleCategoryID) {
        // creatу link
        $rows = & $frow['children'];
        // delete root category
        if(isset($rows[$moduleCategoryID])) {
            unset($rows[$moduleCategoryID]);
        }
        // fill ubsent parent
        $keys = array_keys($rows);
        foreach ($keys as $key) {
            $parentID = $rows[$key]["parent_id"];
            $parentTitle = $rows[$key]["parent_title"];
            $rows[$key]["is_child"] = false;
            $rows[$key]["subcategories"] = array();
            $rows[$key]["selected_children"] = false;
            unset($rows[$key]["parent_title"]);
            if ($parentID == $moduleCategoryID OR array_key_exists($parentID, $rows)) {
                continue;
            }
            $rows[$parentID] = $rows[$key];
            $rows[$parentID]["cnt"] = 0;
            $rows[$parentID]["alias"] = $parentID;
            $rows[$parentID]["title"] = $parentTitle;
            $rows[$parentID]["parent_id"] = $moduleCategoryID;
        }
        // prepare each filter element
        foreach ($rows as & $r) {
            if (array_key_exists($r["parent_id"], $rows)) {
                $r["is_child"] = true;
            }
            $this->prepareFilterCategory($frow['FE']->change($r['alias']), $r);
        }
        // create tree
        $frow['has_selected_children'] = self::createTree($rows);
    }
    
    private static function createTree (&$rows) {
        $selected = false;
        foreach (array_keys($rows) as $key) {
            $parentID = $rows[$key]["parent_id"];
            unset($rows[$key]["parent_id"], $rows[$key]["is_child"]);
            if (array_key_exists($parentID, $rows)) {
                $rows[$parentID]["cnt"] += (int)$rows[$key]["cnt"];
                $rows[$parentID]["subcategories"][$key] = & $rows[$key];
                if ($rows[$key]["selected"]) {
                    $rows[$parentID]["selected_children"] = $rows[$key]["selected"];
                    $selected = true;
                }
                unset($rows[$key]);
            }
        }
        uasort($rows, function($a, $b){
            return strcmp($a['title'], $b['title']);
        });
        return $selected;
    }
    
    /**
     * @param FilterElement $FE
     * @param array $row
     */
    private function prepareFilter(FilterElement $FE, array &$row, $strict) {
        // проверяем выбран ли на данный момент этот фильтр
        $row['selected'] = $this->UrlWL->getFilters()->issetAttribute($FE->getFilterID(), $FE->getValueID());
        // урл для ссылок
        $row['url'] = '';
        if ($row['cnt'] > 0){
            // копируем фильтры для манипуляции ими
            $Filters = $this->UrlWL->copyFilters();
            if($row['selected']){
                // добавляем атрибут по любому даже если он там есть
                $Filters->removeAttribute($FE->getFilterID(), $FE->getValueID());
            } else {
                if ($strict) $Filters->removeFilter($FE->getFilterID());
                // удаляем атрибут по любому даже если он там есть
                $Filters->appendAttribute($FE->getFilterID(), $FE->getValueID());
            }
            $row['url'] = $this->getUrl($Filters);
            // очищаем обект для освобождения памяти
            unset($Filters);
        } elseif ($row['selected']) {
            $row['url'] = $this->getUrl($this->UrlWL->copyFilters()->removeAttribute($FE->getFilterID(), $FE->getValueID()));
        }     
        // определение типа ссылки 
        if($row['selected']) {
            $row['primary'] = true;   
            // повышаем текущий количество вовлеченных значений
            $FE->incrementInvolved();
        } else {
            $row['primary'] = false;     
            // повышаем текущий количество спрятанных значений 
            $FE->incrementHidden();
        }
        // повышаем текущее количество елементов фильтра
        $FE->incrementTotal();
    }
    /**
     * @param FilterElement $FE
     * @param array $row
     */
    private function prepareFilterCategory(FilterElement $FE, array &$row) {
        // проверяем выбран ли на данный момент этот фильтр
        $row['selected'] = $this->UrlWL->getFilters()->issetAttribute($FE->getFilterID(), $FE->getValueID());
        // урл для ссылок
        $row['url'] = '';
        // копируем фильтры для манипуляции ими
        $Filters = $this->UrlWL->copyFilters()->removeFilter($FE->getFilterID());
        if (!$row['selected']) {
            $Filters->appendAttribute($FE->getFilterID(), $FE->getValueID());
        } else if ($row['selected'] && $row['is_child']) {
            $Filters->appendAttribute($FE->getFilterID(), $row['parent_id']);
        }
        $row['url'] = $this->getUrl($Filters);
        // очищаем обект для освобождения памяти
        unset($Filters);
        // определение типа ссылки
        $row['primary'] = true;
        // повышаем текущий количество вовлеченных значений
        $FE->incrementInvolved();
        // повышаем текущее количество елементов фильтра
        $FE->incrementTotal();
    }

    /**
     * @param FilterElement $FE
     * @param array $row
     */
    private function prepareFilterRange(FilterElement $FE, array &$row) {
        $row['selected'] = array (
            UrlFiltersRange::KEY_MIN => $this->UrlWL->getFilters()->getAttribute($FE->getFilterID(), UrlFiltersRange::KEY_MIN, 0),
            UrlFiltersRange::KEY_MAX => $this->UrlWL->getFilters()->getAttribute($FE->getFilterID(), UrlFiltersRange::KEY_MAX, 0),
        );
        $Filters = $this->UrlWL->copyFilters()
            ->appendAttribute($FE->getFilterID(), $row['selected'][UrlFiltersRange::KEY_MIN], UrlFiltersRange::KEY_MIN)
            ->appendAttribute($FE->getFilterID(), $row['selected'][UrlFiltersRange::KEY_MAX], UrlFiltersRange::KEY_MAX)
        ;
        $row['cnt'] = $this->count;
        $row['url'] = $this->getUrl($Filters->setMaskRangesOn());
        $row['masks'] = json_encode(array(
            UrlFiltersRange::KEY_MIN => UrlFiltersRange::maskKey(UrlFiltersRange::KEY_MIN),
            UrlFiltersRange::KEY_MAX => UrlFiltersRange::maskKey(UrlFiltersRange::KEY_MAX),
            UrlFiltersRange::KEY_SEP_MAX => UrlFiltersRange::generateMaxPart(UrlFiltersRange::maskKey(UrlFiltersRange::KEY_MAX)),
        ));
        $Filters->removeFilter($FE->getFilterID());
        $row['selected']['url'] = $this->getUrl($Filters);
        // определение типа ссылки 
        $row['primary'] = true;   
        // повышаем текущий количество вовлеченных значений
        $FE->incrementInvolved();
        // повышаем текущее количество елементов фильтра
        $FE->incrementTotal();
        // очищаем обект для освобождения памяти
        unset($Filters);
    }
    /**
     * @param UrlFilters $Filters
     * @param bool $reCopyUrlWL
     * @return string
     */
    public function getUrl(UrlFilters $Filters=null, $reCopyUrlWL=true) {
        if($reCopyUrlWL || $this->_UrlWL === null){
            // чистим память чтобы не было засорения копиями
            if($this->_UrlWL !== null){
                unset($this->_UrlWL);
            } $this->_UrlWL = $this->UrlWL->copy();
        }
        // если переданы фильтры - то устанавливаем их
        if($Filters !== null){
            $this->_UrlWL->setFilters($Filters);
        } return $this->_UrlWL->resetPage()->buildUrl();
    }
    /**
     * @param string $number
     * @return string
     */
    public static function normalizeNumber($number) {
        return str_replace(',', '.', $number);
    }
    /**
     * @param string $searchtext
     * @param int $limit
     * @param int $minLength
     * @return array
     */
    public static function searchWords($searchtext, $limit=10, $minLength=1) {
        $words = $searchtext ? array_unique(explode(self::WORDS_SEPARATOR, $searchtext)) : array();
        foreach($words as $key => $val){
            if(strlen($val) > $minLength){
                $words[$key] = mysql_real_escape_string($val);
            } else {
                unset($words[$key]);
            }
        }
        if($words){
            $words = array_slice($words, 0, $limit);
        }
        return $words;
    }
    /**
     * @param string $searchtext
     * @return string
     */
    public static function generateSearchWhereState($searchtext = ""){
        $where = self::generateWordsCondition($searchtext);
        if (!empty($where)) $where = "AND($where)>0 ";
        return $where;
    }
    /**
     * @param array $searchtext
     * @param bool $group
     * @param int $weight from 1 to 10
     * @param string $suffix
     * @return string
     */
    public static function generateWordsCondition($searchtext, $group=true, $weight=1, $suffix='') {
        $words = self::searchWords($searchtext);
        $cnt = count($words);
        $nums = array();
        $code = $title = '';
        if ($weight < 1) $weight = 1;
        elseif ($weight > 10) $weight = 10;
        foreach ($words as $key => $word){
            if (is_numeric($word)){
                $word = self::normalizeNumber($word);
                if ($cnt == 1) $code = $word;
                else $nums[] = $word;
            } elseif (strpos($word, self::WORDS_SEPARATOR)!==FALSE) {
                $title = $word;
                unset($words[$key]);
                $cnt--;
            }
        }
        $arConditions = array();
        // поиск по ID товаров
        if ($code > 0) {
            $arConditions[] = 'IF(t.`id`='.$code.', 10, 0)'.PHP_EOL;
        }
        if ($nums) {
            foreach ($nums as $num) {
                $arConditions[] = 'IF(t.`id`='.$num.', 1, 0)'.PHP_EOL;
            }
        }
        // поиск по точному совпадению заголовка товара
        if ($title) {
            $arConditions[] = 'IF(LOWER(t.`title`)="'.$title.'", 10, 0)'.PHP_EOL;
            $arConditions[] = 'IF(LOWER(t.`pcode`)="'.$title.'", 10, 0)'.PHP_EOL;
        }
        // поиск по всем словам в остальных свойствах товара
        if ($words) {
            foreach ($words as $word) {
                // поиск по заголовкам товаров
                $arConditions[] = 'IF(LOWER(t.`title`) LIKE "%'.$word.'%", 1, 0)'.PHP_EOL;
                // поиск по заголовкам товаров
                $arConditions[] = 'IF(LOWER(t.`pcode`) LIKE "%'.$word.'%", 1, 0)'.PHP_EOL;
                // поиск по краткому описанию товаров
                $arConditions[] = 'IF(LOWER(t.`descr`) LIKE "%'.$word.'%", 1, 0)'.PHP_EOL;
            }
            // поиск по брендам товаров
            $arConditions[] = 'IF((SELECT COUNT(`id`) FROM `'.BRANDS_TABLE.'` WHERE `id`=t.`bid` AND `active`=1 AND (LOWER(`title`) LIKE "%'.implode('%" OR LOWER(`title`) LIKE "%', $words).'%"))>0, 2, 0)'.PHP_EOL;
            // поиск по значениям атрибутов товаров
            $arConditions[] = 'IF((SELECT COUNT(avtx.`id`) FROM `'.ATTRIBUTES_VALUES_TABLE.'` avtx JOIN `'.PRODUCT_ATTRIBUTE_TABLE.'` patx ON (patx.`value`=avtx.`id`) WHERE patx.`pid`=t.`id` AND (LOWER(avtx.`title`) LIKE "%'.implode('%" OR LOWER(avtx.`title`) LIKE "%', $words).'%"))>0, 1, 0)'.PHP_EOL;
        } return self::mergeConditions($arConditions, self::OPERATOR_PLUS, true).$suffix;
    }

    /**
     * @param string $query to log
     * @return boolean true if the $query log succesfully
     */
    protected function logQuery($query) {
        return saveStrToFile(($query ? '-- Time: ' . date('Y-m-d H:i:s') . PHP_EOL . trim($query) . ';' : '') . PHP_EOL, WLCMS_RUNTIME_DIR . DS . "filter_queries.sql");
    }
}

class CatalogFilters extends Filters {
    
    protected function setIndexTable() {
        $this->_table = CATALOG_INDEX_TABLE;
    }

    protected function getModuleCategoryID() {
        return UrlWL::CATALOG_CATID;
    }

    protected function getSelectColumn() {
        return $this->getGroupColumn();
    }

    protected function getGroupColumn($tid=null) {
        return ($this->separateByColor ? $this->getProductColumn() : 'model_id');
    }
}

class BrandFilters extends CatalogFilters {
   /**
     * 
     * @param UrlWL $UrlWL
     * @param int $categoryID
     * @param int $brandID задает параметр ID бренда для выборки товаров по ID бренда (используется в модуле Бренды)
     */
    public function __construct($UrlWL, $categoryID, $brandID = 0) {
        if(!$brandID) die('Нет бренда - нет фильтров!');  
        parent::__construct($UrlWL, $categoryID);
        $this->brandID = $brandID;
    }

}

class PrintFilters extends Filters {
    /**
     * 
     * @param UrlWL $UrlWL
     * @param int $categoryID     
     * @param array $categoriesIdSet
     */
    public function __construct($UrlWL, $categoryID, array $categoriesIdSet) {
        parent::__construct($UrlWL, $categoryID);
        $this->categoriesIdSet = $categoriesIdSet;
    }
    
    protected function setIndexTable() {
        $this->_table = PRINT_INDEX_TABLE;
    }

    protected function getModuleCategoryID() {
        return UrlWL::PRINT_CATID;
    }

    protected function getProductColumn() {
        return 'print_id';
    }

    protected function getSelectColumn() {
        return ($this->separateByColor ? 'product_id' : ($this->separateByType ? 'assortment_id' : $this->getProductColumn()));
    }
    
    protected function getGroupColumn($tid=null) {
        switch ($tid) {
            case UrlFilters::TYPE_COLOR:
                return 'color_id';
            case UrlFilters::TYPE_BRAND:
            case UrlFilters::TYPE_CATEGORY:
            case UrlFilters::TYPE_PRICE:
            case UrlFilters::TYPE_NUMBER:
            case UrlFilters::TYPE_TEXT:
                return 'substrate_id';
            default: 
                return $this->getSelectColumn();
        }
    }
}