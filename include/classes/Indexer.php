<?php

/*
 * WebLife CMS
 * Developed by http://weblife.ua/
 */

defined('WEBlife') or die('Restricted access'); // no direct access

require_once 'indexer/IndexItem.php';
require_once 'indexer/IndexStack.php';
require_once 'indexer/CatalogIndex.php';
require_once 'indexer/PrintIndex.php';

/**
 * Description of IndexStack
 *
 * @author weblife
 * @version 2018-04-10
 */

final class Indexer {
    
    static private $_instances;
    static public $settings;

    public static function init() {
        // drop shared triggers
        IndexBase::dropSharedTriggers(self::getInstances());
        // drop shared stored procedures
        IndexBase::dropSharedProcedures(self::getInstances());
        // init by instances
        foreach(self::getInstances() as $instance) {
            $instance->init();
        }
        // add shared stored procedures
        IndexBase::addSharedProcedures(self::getInstances());
        // add shared triggers
        IndexBase::addSharedTriggers(self::getInstances());
    }

    /**
     * Общий метод для изменений по всем индексным таблицам
     * @param IndexItem $IndexItem По умолчанию NULL - полная переиндексация <br>
     * <b>Возможные вариации:</b><br>
     * - NULL или IndexItem::getInstance()->setForced(true) - полная переиндексация по всем индексным таблицам ()<br>
     * - CatalogIndexItem::getInstance()->setForced(true)->setColorID(10) - обновление индексной таблицы каталога напрямую где цвет с ID 10<br>
     * - IndexItem::getInstance() - обновление всех индексных таблиц данными взятыми из их стековых таблиц где applied=0<br>
     * - CatalogIndexItem::getInstance()->setColorID(10) - добавление в стековую таблицу каталога цвета с ID 10<br>
     * 
     * <b>Примечание:</b> Если использовать CatalogIndexItem::getInstance() вместо IndexItem::getInstance() - то будет задействован только каталожная индексная таблица
     * @return int
     */
    public static function update(IndexItem $IndexItem = null) {
        $affected = 0;
        $class = $IndexItem ? get_class($IndexItem) : 'IndexItem';
        foreach(self::getInstances() as $key => $instance) {
            if($class == 'IndexItem' || $class == $key.'IndexItem'){
                $affected += $instance->update($IndexItem);
            }
        }
        return $affected;
    }

    public static function drop() {
        // drop shared triggers
        IndexBase::dropSharedTriggers(self::getInstances());
        // drop shared stored procedures
        IndexBase::dropSharedProcedures(self::getInstances());
        // drop by instances
        foreach(self::getInstances() as $instance) {
            $instance->drop();
        }
    }
    
    /**
     * @return string
     */
    public static function count() {
        $res = [];
        foreach(self::getInstances() as $key => $instance) {
            $res[] = $key . ' - ' . $instance->count();
        }
        return implode(', ', $res);
    }

    /**
     * 
     * @return IndexBase
     */
    private static function getInstances() {
        if(self::$_instances === null) {
            $eurRate = isset(self::$settings->eurRate) ? self::$settings->eurRate : 0;
            $precision = isset(self::$settings->pricePrecision) ? self::$settings->pricePrecision : 0;
            self::$_instances = [
                'Catalog' => new CatalogIndex(CATALOG_INDEX_TABLE, $eurRate, $precision),
                'Print' => new PrintIndex(PRINT_INDEX_TABLE),
            ];
        }
        return self::$_instances;
    }
}