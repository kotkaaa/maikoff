
RENAME TABLE `ru_prints` TO `ru_prints_old` ;
RENAME TABLE `ru_printfiles` TO `ru_printfiles_old` ;
RENAME TABLE `print_assortment` TO `print_assortment_old` ;
RENAME TABLE `print_assortment_colors` TO `print_assortment_colors_old` ;

DROP TABLE IF EXISTS `ru_prints`;
CREATE TABLE IF NOT EXISTS `ru_prints` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `type_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Product Default Type ID', -- для более удобного подключения через JOIN и получения дефолтного типа и дефолтного цвета
  `file_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Product Default File ID', -- для более удобного подключения через JOIN и получения дефолтного файла в админке
  `placement` varchar(50) NOT NULL DEFAULT '' COMMENT 'Print place', -- для определения размещения логотипа - спереди или на спине
  `pcode` varchar(32) NOT NULL COMMENT 'Product code',
  `title` varchar(255) NOT NULL,
  `text` TEXT NOT NULL DEFAULT '',
  `meta_descr` text NOT NULL,
  `meta_key` text NOT NULL,
  `meta_robots` text NOT NULL,
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  `seo_text` text NOT NULL,
  `seo_path` varchar(255) NOT NULL DEFAULT '' COMMENT 'Slug for seopath',
  `order` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_type_id` (`type_id`),
  KEY `idx_file_id` (`file_id`),
  KEY `idx_pcode` (`pcode`),
  KEY `idx_order` (`order`),
  KEY `idx_active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `ru_printfiles`;
CREATE TABLE IF NOT EXISTS `ru_printfiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `print_id` int(11) unsigned NOT NULL COMMENT 'Print ID',
  `filename` varchar(255) NOT NULL COMMENT 'Print file name with extension',
  `title` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_print_id` (`print_id`),
  KEY `idx_order` (`order`),
  KEY `idx_active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `print_assortment`;
CREATE TABLE IF NOT EXISTS `print_assortment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `print_id` int(11) unsigned NOT NULL COMMENT 'Print ID',
  `type_id` int(11) unsigned NOT NULL COMMENT 'Product Type ID',
  `color_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Default Color ID',  -- для более удобного подключения через JOIN и получения дефолтного цвета для данного типа
  `seo_path` varchar(255) NOT NULL DEFAULT '' COMMENT 'Slug for seopath (type-print)',
  `price` float(11,2) NOT NULL DEFAULT '0.00' COMMENT 'Price from type in UAH', -- цена в гривнах
  `order` int(11) NOT NULL DEFAULT '0' COMMENT 'Order from type',    -- для более удобного использования в админке
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked or not',    -- для более удобного использования и не постоянного пересоздания
  `isdefault` tinyint(1) unsigned NOT NULL DEFAULT '0',   -- для более удобного управления внутри этой таблицы
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx_print_type` (`print_id`,`type_id`),
  KEY `idx_print_id` (`print_id`),
  KEY `idx_type_id` (`type_id`),
  KEY `idx_color_id` (`color_id`),
  KEY `idx_seo_path` (`seo_path`),
  KEY `idx_order` (`order`),
  KEY `idx_active` (`active`),
  KEY `idx_isdefault` (`isdefault`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `print_assortment_settings`;
CREATE TABLE IF NOT EXISTS `print_assortment_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `assortment_id` int(11) unsigned NOT NULL COMMENT 'Assortment ID',
  `file_id` int(11) unsigned NOT NULL COMMENT 'File ID',
  `offset` int(11) NOT NULL DEFAULT '0' COMMENT 'Logo top position on wrapper',
  `width` int(11) NOT NULL DEFAULT '0' COMMENT 'Logo width on wrapper',
  `height` int(11) NOT NULL DEFAULT '0' COMMENT 'Logo height on wrapper',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked or not',    -- для более удобного использования и не постоянного пересоздания
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx_assortment_file` (`assortment_id`,`file_id`),
  KEY `idx_assortment_id` (`assortment_id`),
  KEY `idx_file_id` (`file_id`),
  KEY `idx_active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `print_assortment_colors`;
CREATE TABLE IF NOT EXISTS `print_assortment_colors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `assortment_id` int(11) unsigned NOT NULL COMMENT 'Assortment ID',
  `color_id` int(11) unsigned NOT NULL COMMENT 'Color ID',
  `file_id` int(11) unsigned NOT NULL COMMENT 'File ID',    -- для более удобного использования во многих зарпосах
/* 
  * для гибкости системы можно добавить эти два поля чтобы можно было подключать эту таблицу напрямую к принтам
  `print_id` int(11) unsigned NOT NULL COMMENT 'Print ID',
  `type_id` int(11) unsigned NOT NULL COMMENT 'Product Type ID',
*/
  `order` int(11) NOT NULL DEFAULT '0' COMMENT 'Order from type color',    -- для более удобного использования в админке
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked or not',    -- для более удобного использования и не постоянного пересоздания
  `isdefault` tinyint(1) unsigned NOT NULL DEFAULT '0',   -- для более удобного управления внутри этой таблицы
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx_assortment_color` (`assortment_id`,`color_id`),
  KEY `idx_assortment_file` (`assortment_id`,`file_id`),
  KEY `idx_assortment_id` (`assortment_id`),
  KEY `idx_color_id` (`color_id`),
  KEY `idx_file_id` (`file_id`),
/*
  KEY `idx_print_id` (`print_id`),
  KEY `idx_type_id` (`type_id`),
*/
  KEY `idx_order` (`order`),
  KEY `idx_active` (`active`),
  KEY `idx_isdefault` (`isdefault`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `ru_printfiles`
      (`id`,`print_id`,`filename`,`title`,`order`,`active`,`created`,`modified`)
SELECT `id`,`print_id`,`filename`,`title`,`order`,`active`,`created`,`modified` 
FROM `ru_printfiles_old`;

INSERT INTO `ru_prints`
      (`id`,`category_id`,`placement`,`pcode`,`title`,`text`,`meta_descr`,`meta_key`,`meta_robots`,`seo_title`,`seo_text`,`seo_path`,`order`,`active`,`created`,`modified`)
SELECT `id`,`category_id`,`placement`,`pcode`,`title`,`text`,`meta_descr`,`meta_key`,`meta_robots`,`seo_title`,`seo_text`,`seo_path`,`order`,`active`,`created`,`modified` 
FROM `ru_prints_old`;

INSERT INTO `print_assortment`
      (`id`,`print_id`,`type_id`,`color_id`,`seo_path`,`price`,`order`,`active`,`isdefault`)
SELECT NULL,a.`print_id`,a.`type_id`,a.`color_id`,a.`seo_path`,a.`price`,t.`order`,1 `active`,a.`isdefault`
FROM `print_assortment_old` a JOIN `ru_product_types` t ON t.`id`=a.`type_id` WHERE a.`color_id` > 0;

INSERT INTO `print_assortment_settings`
    (`id`,`assortment_id`,`file_id`,`offset`,`width`,`height`,`active`)
SELECT NULL,a.`id`,t.`file_id`,t.`offset`,t.`width`,t.`height`,1 `active`
FROM `print_assortment_old` t JOIN `print_assortment` a ON a.`print_id`=t.`print_id` AND a.`type_id`=t.`type_id`;

INSERT INTO `print_assortment_colors`
(`id`,`assortment_id`,`file_id`,`color_id`,`order`,`active`,`isdefault`)
SELECT NULL,a.`id`,ao.`file_id`,t.`color_id`,ti.`order`,1 `active`,IF(t.`color_id`=a.`color_id`, 1, 0) `isdefault`
FROM `print_assortment_colors_old` t
JOIN `print_assortment_old` ao ON ao.`id`=t.`assortment_id`
JOIN `product_types_images` ti ON ti.`type_id`=t.`type_id` AND ti.`color_id`=t.`color_id`
JOIN `print_assortment` a ON a.`print_id`=t.`print_id` AND a.`type_id`=t.`type_id`;

UPDATE `ru_prints` p SET p.`type_id`= ( SELECT a.`type_id` FROM `print_assortment` a WHERE a.`print_id`=p.`id` AND a.`isdefault`=1 LIMIT 1);
UPDATE `ru_prints` p SET p.`file_id`= ( SELECT c.`file_id` FROM `print_assortment` a JOIN `print_assortment_colors` c ON a.`id`=c.`assortment_id` WHERE a.`print_id`=p.`id` AND a.`type_id`=p.`type_id` AND c.`isdefault`=1 LIMIT 1);

DROP TABLE IF EXISTS `ru_prints_old`;
DROP TABLE IF EXISTS `ru_printfiles_old`;
DROP TABLE IF EXISTS `print_assortment_old`;
DROP TABLE IF EXISTS `print_assortment_colors_old`;
