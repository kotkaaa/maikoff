<?php

/*
 * WebLife CMS
 * Developed by http://weblife.ua/
 */

require_once 'IndexBase.php';
require_once 'PrintIndexItem.php';

/**
 * Description of PrintIndex class
 * This class extends IndexBase class functionality for print module
 * Use $product_id for combined PrintID & SubstrateID & ColorID in index table
 *
 * @package indexer
 * @version 2018-04-10
 * @author weblife
 */
class PrintIndex extends IndexBase {

    protected function columnsMap() {
        return PrintIndexItem::columnsMap();
    }

    protected function createStackTable($table) {
        $query = "CREATE TABLE IF NOT EXISTS `{$table}` (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `applied` tinyint(1) unsigned NOT NULL DEFAULT '0',
                    `entity_id` int(11) unsigned NOT NULL DEFAULT '0',
                    `entity_type` enum('".implode('\',\'', PrintIndexItem::entityTypes())."') NOT NULL DEFAULT '-',
                    `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_applied` (`applied`),
                    UNIQUE KEY `udx_keys` (`entity_id`,`entity_type`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
        return mysql_query($query) or die('Create stack indexes table: '.mysql_error());
    }

    protected function createIndexTable($table) {
        $query = "CREATE TABLE IF NOT EXISTS `{$table}` (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `substrate_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Substrate ID',
                    `category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID категории',
                    `category_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Название категории',
                    `category_parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID родительской категории',
                    `category_parent_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Название родительской категории',
                /*    `category_sequence` int(11) NOT NULL DEFAULT '0' COMMENT 'Сортировка категории',*/
                    `shortcut_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID ярлыка на принт',
                    `print_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Print ID',
                    `assortment_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Print assortment ID',
                    `logo_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Print Files ID',
                    `logo_width` int(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Logo width on wrapper',
                    `logo_offset` int(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Logo top position on wrapper',
                    `color_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID цвета',
                    `color_hex` varchar(255) NOT NULL DEFAULT '' COMMENT 'Код цвета',
                    `color_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Название цвета',
                /*    `color_sequence` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Сортировка цвета',*/
                    `attribute_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID атрибута',
                    `value_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID значения',
                    `value_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Название значения',
                /*    `value_sequence` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Сортировка значения',*/
                    `product_id` bigint(17) unsigned NOT NULL DEFAULT '0' COMMENT 'Print & Type & Color ID',
                    `product_price` float(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Цена',
                    `product_sequence` int(11) NOT NULL DEFAULT '0.00' COMMENT 'Сортировка принта',
                    `default_substrate_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Подложка по умолчанию',
                    `is_available` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Наличие',
                    `is_changed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Запись изменена',
                    `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Запись удалена',
                    PRIMARY KEY (`id`),
                    KEY `idx_substrate_id` (`substrate_id`),
                    KEY `idx_category_id` (`category_id`),
                    KEY `idx_category_title` (`category_title`),
                    KEY `idx_category_parent_id` (`category_parent_id`),
                /*    KEY `idx_category_sequence` (`category_sequence`),*/
                    KEY `shortcut_id` (`shortcut_id`),
                    KEY `idx_print_id` (`print_id`),
                    KEY `idx_assortment_id` (`assortment_id`),
                    KEY `idx_logo_id` (`logo_id`),
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
                /*    KEY `idx_default_substrate_id` (`default_substrate_id`),*/
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
                st.`id` AS `substrate_id`,
                m.`id` AS `category_id`,
                m.`title` AS `category_title`,
                m.`pid` AS `category_parent_id`,
                mp.`title` AS `category_parent_title`,
            /*    m.`order` AS `category_sequence`,*/
                p.`shortcut_id`,
                p.`print_id`,
                a.`id` AS `assortment_id`,
                ps.`file_id` AS `logo_id`,
                ps.`width` AS `logo_width`,
                ps.`offset` AS `logo_offset`,
                c.`id` AS `color_id`,
                c.`hex` AS `color_hex`,
                c.`title` AS `color_title`,
            /*    c.`order` AS `color_sequence`,*/
                av.`aid` AS `attribute_id`,
                av.`id` AS `value_id`,
                av.`title` AS `value_title`,
            /*    av.`order` AS `value_sequence`,*/
                CONCAT(RPAD(p.`print_id`, 6, '0'), LPAD(a.`substrate_id`, 3, '0'), LPAD(pc.`color_id`, 3, '0'))*1 AS `product_id`,
                a.`price` AS `product_price`,
                p.`order` AS `product_sequence`,
                p.`substrate_id` AS `default_substrate_id`,
                1 AS `is_available`,
                {$isChanged} AS `is_changed`,
                0 AS `is_deleted`
            FROM ((
                    SELECT t1.`id` AS `print_id`, t1.`substrate_id`, t1.`order`, t1.`category_id`, 0 AS `shortcut_id`
                    FROM `".PRINTS_TABLE."` t1 
                    WHERE t1.`active`>0
                ) UNION (
                    SELECT t2.`id` AS `print_id`, t2.`substrate_id`, t2.`order`, st.`cid` AS `category_id`, st.`id` AS `shortcut_id`
                    FROM `".SHORTCUTS_TABLE."` st 
                    INNER JOIN `".PRINTS_TABLE."` t2 ON(t2.`id`=st.`pid`) 
                    WHERE t2.`active`>0 AND st.`active`>0
            )) p 
            INNER JOIN `".MAIN_TABLE."` m ON(m.`id`=p.`category_id` AND m.`active`=1)
            LEFT JOIN `".MAIN_TABLE."` mp ON(mp.`id`=m.`pid`)
            INNER JOIN `".PRINT_ASSORTMENT_TABLE."` a ON(a.`print_id`=p.`print_id` AND a.`active`=1)
            INNER JOIN `".PRINT_ASSORTMENT_COLORS_TABLE."` pc ON(pc.`assortment_id`=a.`id` AND pc.`active`=1)
            INNER JOIN `".PRINT_ASSORTMENT_SETTINGS_TABLE."` ps ON(ps.`assortment_id`=a.`id` AND ps.`file_id`=pc.`file_id` AND ps.`active`=1)
            INNER JOIN `".SUBSTRATES_TABLE."` st ON(st.`id`=a.`substrate_id` AND st.`active`=1)
            INNER JOIN `".COLORS_TABLE."` c ON(c.`id`=pc.`color_id`) " .
/* It is very slower request but excludes duplicates of print and attributes. While duplicates are absent, I decided not to bother. 
   For acceleration, you must first select a separate table
            LEFT JOIN (      
                SELECT pa.`pid`, p.`substrate_id` `sid`, pa.`aid`, pa.`value` 
                FROM `".PRINT_ATTRIBUTES_TABLE."` pa JOIN `".PRINTS_TABLE."` p ON p.`id`=pa.`pid`
              UNION
                SELECT pa.`print_id` `pid`, pa.`substrate_id` `sid`, sa.`aid`, sa.`value` 
                FROM `".SUBSTRATES_ATTRIBUTES_TABLE."` sa JOIN `".PRINT_ASSORTMENT_TABLE."` pa ON pa.`substrate_id`=sa.`sid`
            ) pa ON(pa.`pid`=p.`print_id` AND pa.`sid`=a.`substrate_id`)
*/
/*
*/
"
            LEFT JOIN (      
                SELECT '0' AS `pid`, `sid`, `aid`, `value` FROM `".SUBSTRATES_ATTRIBUTES_TABLE."`
              UNION 
                SELECT `pid`, '0' AS `sid`, `aid`, `value` FROM `".PRINT_ATTRIBUTES_TABLE."`
            ) pa ON(pa.`pid`=p.`print_id` OR pa.`sid`=a.`substrate_id`)
            LEFT JOIN `".ATTRIBUTES_VALUES_TABLE."` av ON(av.`id`=pa.`value` AND av.`aid`=pa.`aid`)
        ) t {$conditions}";
        return $query;
    }

    protected function addProcedures(){
        $query  =  "CREATE PROCEDURE `{$this->stackFunc}` (IN `entityID` INT(11), IN `entityType` varchar(255))
                        NO SQL
                    BEGIN
                      INSERT INTO `{$this->stackTable}` (`entity_id`, `entity_type`) 
                        VALUES (entityID, entityType) 
                        ON DUPLICATE KEY UPDATE `applied`=0;
                    END;";
        mysql_query($query) or die("Create index procedure: ".mysql_error());
    }

    protected function addTriggers(){
        /**
         * print_assortment ****************************************************
         */
        $table = PRINT_ASSORTMENT_TABLE;
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`print_id`!=OLD.`print_id`) THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(OLD.`print_id`, 'print'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "    IF (NEW.`print_id`!=OLD.`print_id` OR " . PHP_EOL
                 . "        NEW.`substrate_id`!=OLD.`substrate_id` OR " . PHP_EOL
                 . "        NEW.`active`!=OLD.`active` OR " . PHP_EOL
                 . "        NEW.`isdefault`!=OLD.`isdefault`) " . PHP_EOL
                 . "    THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(NEW.`print_id`, 'print'); " . PHP_EOL
                 . "    ELSEIF (NEW.`color_id`!=OLD.`color_id` OR " . PHP_EOL
                 . "        NEW.`seo_path`!=OLD.`seo_path` OR " . PHP_EOL
                 . "        NEW.`price`!=OLD.`price` OR " . PHP_EOL
                 . "        NEW.`order`!=OLD.`order`) " . PHP_EOL
                 . "    THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(NEW.`id`, 'assortment'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (OLD.`isdefault`) THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(OLD.`print_id`, 'print'); " . PHP_EOL
                 . "    ELSE " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(OLD.`id`, 'assortment'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * print_assortment_settings *******************************************
         */
        $table = PRINT_ASSORTMENT_SETTINGS_TABLE;
         // after insert 
        $trigg  = $table.'_ai';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER INSERT ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(NEW.`assortment_id`, 'assortment'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`assortment_id`!=OLD.`assortment_id`) THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(OLD.`assortment_id`, 'assortment'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(NEW.`assortment_id`, 'assortment'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(OLD.`assortment_id`, 'assortment'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * print_assortment_colors *********************************************
         */
        $table = PRINT_ASSORTMENT_COLORS_TABLE;
         // after insert 
        $trigg  = $table.'_ai';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER INSERT ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(NEW.`assortment_id`, 'assortment'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`assortment_id`!=OLD.`assortment_id`) THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(OLD.`assortment_id`, 'assortment'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(NEW.`assortment_id`, 'assortment'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(OLD.`assortment_id`, 'assortment'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * print_attributes ****************************************************
         */
        $table = PRINT_ATTRIBUTES_TABLE;
        // after insert
        $trigg  = $table.'_ai';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER INSERT ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(NEW.`pid`, 'print'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(OLD.`pid`, 'print'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * ru_prints ***********************************************************
         */
        $table = PRINTS_TABLE;
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`category_id`!=OLD.`category_id` OR " . PHP_EOL
                 . "        NEW.`substrate_id`!=OLD.`substrate_id` OR " . PHP_EOL
                 . "        NEW.`active`!=OLD.`active` OR " . PHP_EOL
                 . "        NEW.`order`!=OLD.`order`) " . PHP_EOL
                 . "    THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(NEW.`id`, 'print'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(OLD.`id`, 'print'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * ru_printfiles *******************************************************
         */
        $table = PRINTFILES_TABLE;
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`active`!=OLD.`active`) THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(NEW.`id`, 'logo'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(OLD.`id`, 'logo'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * shortcuts ***********************************************************
         */
        $table = SHORTCUTS_TABLE;
         // after insert 
        $trigg  = $table.'_ai';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER INSERT ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(NEW.`id`, 'shortcut'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`active`!=OLD.`active` OR " . PHP_EOL
                 . "        NEW.`order`!=OLD.`order`) " . PHP_EOL
                 . "    THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(NEW.`id`, 'shortcut'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(OLD.`id`, 'shortcut'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * ru_substrates ****************************************************
         */
        $table = SUBSTRATES_TABLE;
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`active`!=OLD.`active` OR NEW.`order`!=OLD.`order`) " . PHP_EOL
                 . "    THEN " . PHP_EOL
                 . "        CALL `{$this->stackFunc}`(NEW.`id`, 'substrate'); " . PHP_EOL
                 . "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(OLD.`id`, 'substrate'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * substrates_attributes ****************************************************
         */
        $table = SUBSTRATES_ATTRIBUTES_TABLE;
        // after insert
        $trigg  = $table.'_ai';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER INSERT ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(NEW.`sid`, 'substrate'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    CALL `{$this->stackFunc}`(OLD.`sid`, 'substrate'); " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
    }

    protected function dropProcedures(){
        self::dropProcedure($this->stackFunc);
    }

    protected function dropTriggers(){
        /**
         * print_assortment ****************************************************
         */
        self::dropTrigger(PRINT_ASSORTMENT_TABLE."_au");
        self::dropTrigger(PRINT_ASSORTMENT_TABLE."_ad");
        /**
         * print_assortment_colors *********************************************
         */
        self::dropTrigger(PRINT_ASSORTMENT_COLORS_TABLE."_ai");
        self::dropTrigger(PRINT_ASSORTMENT_COLORS_TABLE."_au");
        self::dropTrigger(PRINT_ASSORTMENT_COLORS_TABLE."_ad");
        /**
         * print_assortment_settings *********************************************
         */
        self::dropTrigger(PRINT_ASSORTMENT_SETTINGS_TABLE."_ai");
        self::dropTrigger(PRINT_ASSORTMENT_SETTINGS_TABLE."_au");
        self::dropTrigger(PRINT_ASSORTMENT_SETTINGS_TABLE."_ad");
        /**
         * print_attributes ****************************************************
         */
        self::dropTrigger(PRINT_ATTRIBUTES_TABLE."_ai");
        self::dropTrigger(PRINT_ATTRIBUTES_TABLE."_ad");
        /**
         * ru_prints ***********************************************************
         */
        self::dropTrigger(PRINTS_TABLE."_au");
        self::dropTrigger(PRINTS_TABLE."_ad");
        /**
         * ru_printfiles *******************************************************
         */
        self::dropTrigger(PRINTFILES_TABLE."_au");
        self::dropTrigger(PRINTFILES_TABLE."_ad");
        /**
         * shortcuts ***********************************************************
         */
        self::dropTrigger(SHORTCUTS_TABLE."_ai");
        self::dropTrigger(SHORTCUTS_TABLE."_au");
        self::dropTrigger(SHORTCUTS_TABLE."_ad");
        /**
         * ru_substrates ****************************************************
         */
        self::dropTrigger(SUBSTRATES_TABLE."_au");
        self::dropTrigger(SUBSTRATES_TABLE."_ad");
        /**
         * substrates_attributes ****************************************************
         */
        self::dropTrigger(SUBSTRATES_ATTRIBUTES_TABLE."_ai");
        self::dropTrigger(SUBSTRATES_ATTRIBUTES_TABLE."_ad");
    }

}
