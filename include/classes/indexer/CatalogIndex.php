<?php

/*
 * WebLife CMS
 * Developed by http://weblife.ua/
 */

require_once 'IndexBase.php';
require_once 'CatalogIndexItem.php';

/**
 * Description of CatalogIndex class
 * This class extends IndexBase class functionality for catalog module
 * Use $product_id for catalog row
 *
 * @package indexer
 * @version 2018-04-10
 * @author weblife
 */
class CatalogIndex extends IndexBase {
    protected $eurRate;
    protected $undefvar;
    protected $precision;
    protected $getPriceFunc;
    protected $updatePriceFunc;
    
    /**
     * @param string $table
     * @param float $eurRate
     * @param int $precision
     */
    public function __construct($table, $eurRate, $precision) {
        parent::__construct($table);
        $this->eurRate = $eurRate;
        $this->undefvar = '-||-';
        $this->precision = $precision;
        $this->getPriceFunc = $this->indexTable . '_get_price';
        $this->updatePriceFunc = $this->indexTable . '_update_price';
    }

    protected function columnsMap() {
        return CatalogIndexItem::columnsMap();
    }

    protected function createStackTable($table) {
        $query = "CREATE TABLE IF NOT EXISTS `{$table}` (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `applied` tinyint(1) unsigned NOT NULL DEFAULT '0',
                    `entity_id` int(11) unsigned NOT NULL DEFAULT '0',
                    `entity_type` enum('".implode('\',\'', CatalogIndexItem::entityTypes())."') NOT NULL DEFAULT '-',
                    `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_applied` (`applied`),
                    UNIQUE KEY `udx_keys` (`entity_id`,`entity_type`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
        return mysql_query($query) or die('Create stack indexes table: '.mysql_error());
    }

    protected function createIndexTable($table) {
        /**
         * @tutorial Если в фильтрах для сортировки будут использоватся order то 
         * нужно разкоментить KEY *_sequence а закоментить *_title
         */
        $query = "CREATE TABLE IF NOT EXISTS `{$table}` (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID категории',
                    `category_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Название категории',
                    `category_parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID родительской категории',
                    `category_parent_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Название родительской категории',
                /*    `category_sequence` int(11) NOT NULL DEFAULT '0' COMMENT 'Сортировка категории',*/
                    `model_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID модели',
                    `brand_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID бренда',
                    `series_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID серии',
                    `brand_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Название бренда',
                    `series_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Название серии',
                /*    `brand_sequence` int(11) NOT NULL DEFAULT '0' COMMENT 'Сортировка бренда',*/
                    `color_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID цвета',
                    `color_hex` varchar(255) NOT NULL DEFAULT '' COMMENT 'Код цвета',
                    `color_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Название цвета',
                /*    `color_sequence` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Сортировка цвета',*/
                    `attribute_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID атрибута',
                    `value_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID значения',
                    `value_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Название значения',
                /*    `value_sequence` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Сортировка значения',*/
                    `eur_price` float(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Цена в евро',
                    `eur_rate` float(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Курс евро',
                    `round_precision` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT 'Количество знаков в цене после запятой',
                    `product_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID товара',
                    `product_price` float(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Цена',
                    `product_sequence` float(11,2) NOT NULL DEFAULT '0.00' COMMENT 'Сортировка (модель.товар)',
                    `is_available` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Наличие',
                    `is_changed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Запись изменена',
                    `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Запись удалена',
                    PRIMARY KEY (`id`),
                    KEY `idx_category_id` (`category_id`),
                    KEY `idx_category_title` (`category_title`),
                    KEY `idx_category_parent_id` (`category_parent_id`),
                /*    KEY `idx_category_sequence` (`category_sequence`),*/
                    KEY `idx_model_id` (`model_id`),
                    KEY `idx_brand_id` (`brand_id`),
                    KEY `idx_series_id` (`series_id`),
                    KEY `idx_brand_title` (`brand_title`),
                    KEY `idx_series_title` (`series_title`),
                /*    KEY `idx_brand_sequence` (`brand_sequence`),*/
                    KEY `idx_color_id` (`color_id`),
                    KEY `idx_color_title` (`color_title`),
                /*    KEY `idx_color_sequence` (`color_sequence`),*/
                    KEY `idx_attribute_id` (`attribute_id`),
                    KEY `idx_value_id` (`value_id`),
                    KEY `idx_value_title` (`value_title`),
                /*    KEY `idx_value_sequence` (`value_sequence`),*/
                    KEY `idx_product_id` (`product_id`),
                    KEY `idx_product_price` (`product_price`),
                    KEY `idx_product_sequence` (`product_sequence`),
                    KEY `idx_available` (`is_available`),
                    KEY `idx_changed` (`is_changed`),
                    KEY `idx_deleted` (`is_deleted`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
        return mysql_query($query) or die('Create indexes table: '.mysql_error());
    }

    protected function createSelectQuery($conditions = '') {
        $isChanged = empty($conditions) ? 0 : 1;
        $query = "SELECT t.* FROM (
            SELECT NULL AS `id`,
                mt.`id` AS `category_id`,
                mt.`title` AS `category_title`,
                mt.`pid` AS `category_parent_id`,
                pt.`title` AS `category_parent_title`,
            /*    m.`order` AS `category_sequence`,*/
                m.`id` AS `model_id`, 
                b.`id` AS `brand_id`, 
                s.`id` AS `series_id`,
                b.`title` AS `brand_title`, 
                s.`title` AS `series_title`,
            /*    b.`order` AS `brand_sequence`,*/
                c.`id` AS `color_id`,
                c.`hex` AS `color_hex`,
                c.`title` AS `color_title`,
            /*    c.`order` AS `color_sequence`,*/
                av.`aid` AS `attribute_id`, 
                av.`id` AS `value_id`, 
                av.`title` AS `value_title`, 
            /*    av.`order` AS `value_sequence`,*/
                p.`price` AS `eur_price`, 
                CAST('{$this->eurRate}' AS DECIMAL(8,2)) AS `eur_rate`,
                CAST('{$this->precision}' AS DECIMAL(2,0)) AS `round_precision`,
                p.`id` AS `product_id`, 
                `{$this->getPriceFunc}`(p.`price`, '{$this->eurRate}', '{$this->precision}') AS `product_price`, 
                CONCAT(m.`order`, '.', p.`order`)*1 AS `product_sequence`, 
                1 AS `is_available`, 
                {$isChanged} AS `is_changed`, 
                0 AS `is_deleted`
            FROM `".MODELS_TABLE."` m 
            INNER JOIN `".MAIN_TABLE."` mt ON(mt.`id`=m.`category_id` AND mt.`active`=1)
            LEFT JOIN `".MAIN_TABLE."` pt ON(pt.`id`=mt.`pid`)
            INNER JOIN `".CATALOG_TABLE."` p ON(p.`model_id`=m.`id` AND p.`price`>0 AND p.`active`=1)
            INNER JOIN `".COLORS_TABLE."` c ON(c.`id`=p.`color_id`)
            LEFT JOIN `".BRANDS_TABLE."` b ON(b.`id`=m.`brand_id` AND b.`active`=1) 
            LEFT JOIN `".SERIES_TABLE."` s ON(s.`id`=m.`series_id`)
            LEFT JOIN `".MODEL_ATTRIBUTES_TABLE."` ma ON(ma.`mid`=m.`id`)
            LEFT JOIN `".ATTRIBUTES_VALUES_TABLE."` av ON(av.`id`=ma.`value` AND av.`aid`=ma.`aid`)
            WHERE m.`active`=1
            ORDER BY m.`id`, p.`order`
        ) t {$conditions}";
        return $query;
    }

    protected function addProcedures(){
        /**
         * Index Get Price Function ********************************************
         */
        $query  =  "CREATE FUNCTION `{$this->getPriceFunc}`(eurPrice FLOAT(8,2), eurRate VARCHAR(50), roundPrecision VARCHAR(50)) RETURNS FLOAT(11,2)
                        DETERMINISTIC
                    BEGIN
                      DECLARE fPrice FLOAT(11,2) DEFAULT CAST(eurPrice AS DECIMAL(11,2));
                      DECLARE fRate FLOAT(8,2) DEFAULT CAST(eurRate AS DECIMAL(8,2));
                      DECLARE iPrecision TINYINT(2) DEFAULT CAST(roundPrecision AS DECIMAL(2,0));
                      IF (fPrice != 0) THEN
                        IF (fRate != 0) THEN
                          SET fPrice = fPrice*fRate;
                        END IF;
                        IF (iPrecision >= 0) THEN
                             SET fPrice = ROUND(fPrice, iPrecision);
                        END IF;
                      END IF;
                      RETURN fPrice;
                    END";
        mysql_query($query) or die("Create get price function: ".mysql_error());
        /**
         * Index Update Price Function *****************************************
         */
        $query  =  "CREATE PROCEDURE `{$this->updatePriceFunc}` (IN eurRate VARCHAR(50), roundPrecision VARCHAR(50))
                      NO SQL
                    BEGIN
                      DECLARE sUndefined VARCHAR(4) DEFAULT '{$this->undefvar}';
                      IF(eurRate != sUndefined || roundPrecision != sUndefined) THEN 
                        UPDATE `{$this->indexTable}` 
                        SET `eur_rate`        = IF(eurRate=sUndefined, `eur_rate`, CAST(eurRate AS DECIMAL(8,2))), 
                            `round_precision` = IF(roundPrecision=sUndefined, `round_precision`, CAST(roundPrecision AS DECIMAL(2,0))), 
                            `product_price`   = `{$this->getPriceFunc}`(`eur_price`, IF(eurRate=sUndefined, `eur_rate`, eurRate), IF(roundPrecision=sUndefined, `round_precision`, roundPrecision))
                        ;
                      END IF;
                    END";
        mysql_query($query) or die("Create index update price procedure: ".mysql_error());
        /**
         * Stack Function ******************************************************
         */
        $query  =  "CREATE PROCEDURE `{$this->stackFunc}` (IN `entityID` INT(11), IN `entityType` varchar(255))
                        NO SQL
                    BEGIN
                      INSERT INTO `{$this->stackTable}` (`entity_id`, `entity_type`) 
                        VALUES (entityID, entityType) 
                        ON DUPLICATE KEY UPDATE `applied`=0;
                    END";
        mysql_query($query) or die("Create index procedure: ".mysql_error());
    }

    protected function addTriggers(){
        /**
         * model_attributes ****************************************************
         */
        $table = MODEL_ATTRIBUTES_TABLE;
        // after insert
        $trigg  = $table.'_ai';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER INSERT ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(NEW.`mid`, 'model'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(OLD.`mid`, 'model'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * ru_brands ***********************************************************
         */
        $table = BRANDS_TABLE;
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`title`!=OLD.`title` OR " . PHP_EOL
                 . "        NEW.`active`!=OLD.`active` OR " . PHP_EOL
                 . "        NEW.`order`!=OLD.`order`) " . PHP_EOL
                 . "    THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(NEW.`id`, 'brand'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(OLD.`id`, 'brand'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * ru_series ***********************************************************
         */
        $table = SERIES_TABLE;
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`title`!=OLD.`title`) " . PHP_EOL
                 . "    THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(NEW.`id`, 'series'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(OLD.`id`, 'series'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * ru_model ************************************************************
         */
        $table = MODELS_TABLE;
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`category_id`!=OLD.`category_id` OR " . PHP_EOL
                 . "        NEW.`brand_id`!=OLD.`brand_id` OR " . PHP_EOL
                 . "        NEW.`active`!=OLD.`active` OR " . PHP_EOL
                 . "        NEW.`order`!=OLD.`order`) " . PHP_EOL
                 . "    THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(NEW.`id`, 'model'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(OLD.`id`, 'model'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * ru_catalog **********************************************************
         */
        $table = CATALOG_TABLE;
        // after insert 
        $trigg  = $table.'_ai';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER INSERT ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(NEW.`id`, 'product'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`model_id`!=OLD.`model_id`) " . PHP_EOL
                 . "    THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(NEW.`model_id`, 'model'); " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(OLD.`model_id`, 'model'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "    IF (NEW.`order`!=OLD.`order`) " . PHP_EOL
                 . "    THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(NEW.`model_id`, 'model'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "    IF (NEW.`color_id`!=OLD.`color_id` OR " . PHP_EOL
                 . "        NEW.`price`!=OLD.`price` OR " . PHP_EOL
                 . "        NEW.`active`!=OLD.`active`) " . PHP_EOL
                 . "    THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(NEW.`id`, 'product'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(OLD.`id`, 'product'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * ru_settings *********************************************************
         */
        $table = SETTINGS_TABLE;
        // after insert 
        $trigg  = $table.'_ai';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER INSERT ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`name`='eurRate') THEN " . PHP_EOL
                 . "        CALL `{$this->updatePriceFunc}`(NEW.`value`, '{$this->undefvar}'); " . PHP_EOL
                 . "    ELSEIF (NEW.`name`='pricePrecision') THEN " . PHP_EOL
                 . "        CALL `{$this->updatePriceFunc}`('{$this->undefvar}', NEW.`value`); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`name`='eurRate' AND NEW.`value`!=OLD.`value`) THEN " . PHP_EOL
                 . "        CALL `{$this->updatePriceFunc}`(NEW.`value`, '{$this->undefvar}'); " . PHP_EOL
                 . "    ELSEIF (NEW.`name`='pricePrecision' AND NEW.`value`!=OLD.`value`) THEN " . PHP_EOL
                 . "        CALL `{$this->updatePriceFunc}`('{$this->undefvar}', NEW.`value`); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (OLD.`name`='eurRate') THEN " . PHP_EOL
                 . "        CALL `{$this->updatePriceFunc}`(0, '{$this->undefvar}'); " . PHP_EOL
                 . "    ELSEIF (OLD.`name`='pricePrecision') THEN " . PHP_EOL
                 . "        CALL `{$this->updatePriceFunc}`('{$this->undefvar}', 0); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
    }

    protected function dropProcedures(){
        /**
         * Index Get Price Function ********************************************
         */
        self::dropFunction($this->getPriceFunc);
        /**
         * Index Update Price Function *****************************************
         */
        self::dropProcedure($this->updatePriceFunc);
        /**
         * Stack Function ******************************************************
         */
        self::dropProcedure($this->stackFunc);
    }

    protected function dropTriggers(){
        /**
         * model_attributes ****************************************************
         */
        self::dropTrigger(MODEL_ATTRIBUTES_TABLE."_ai");
        self::dropTrigger(MODEL_ATTRIBUTES_TABLE."_ad");
        /**
         * ru_brands ***********************************************************
         */
        self::dropTrigger(BRANDS_TABLE."_au");
        self::dropTrigger(BRANDS_TABLE."_ad");
        /**
         * ru_brands ***********************************************************
         */
        self::dropTrigger(SERIES_TABLE."_au");
        self::dropTrigger(SERIES_TABLE."_ad");
        /**
         * ru_model ************************************************************
         */
        self::dropTrigger(MODELS_TABLE."_au");
        self::dropTrigger(MODELS_TABLE."_ad");
        /**
         * ru_catalog **********************************************************
         */
        self::dropTrigger(CATALOG_TABLE."_ai");
        self::dropTrigger(CATALOG_TABLE."_au");
        self::dropTrigger(CATALOG_TABLE."_ad");
        /**
         * ru_settings *********************************************************
         */
        self::dropTrigger(SETTINGS_TABLE."_ai");
        self::dropTrigger(SETTINGS_TABLE."_au");
        self::dropTrigger(SETTINGS_TABLE."_ad");
    }

}
