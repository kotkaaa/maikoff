-- -----------------------------------------------------------------------------
-- 07.03.2018
-- настройки
-- -----------------------------------------------------------------------------
TRUNCATE TABLE `ru_settings`;
INSERT INTO `ru_settings` (`name`,`value`,`require`) VALUES
('adwordsEmail','sales@maikoff.com.ua',0),
('adwordsPhone','(044) 599-99-22, (063) 344-33-12, (067) 324-92-19',0),
('copyright','Copyright 2017 © Maikoff. All rights reserved.',0),
('notifyEmail','sales@maikoff.com.ua',1),
('ownerAddress','&lt;p&gt;Киев,&lt;br /&gt;ул. Константиновская 688 (Подол)&amp;nbsp;&lt;/p&gt;',0),
('schedule','',0),
('siteEmail','info@maikoff.com.ua',1),
('sitePhone','(044) 599-99-21, (063) 344-33-11, (067) 324-92-18',1);
-- -----------------------------------------------------------------------------
-- 07.03.2018
-- структура разделов
-- -----------------------------------------------------------------------------
ALTER TABLE `ru_main` ADD `image_menu` VARCHAR(255) DEFAULT NULL AFTER `image`;
ALTER TABLE `ru_main` ADD `image_icon` VARCHAR(255) DEFAULT NULL AFTER `image_menu`;
-- -----------------------------------------------------------------------------
-- 07.03.2018
-- атрибуты
-- -----------------------------------------------------------------------------
ALTER TABLE `ru_attribute_types` ADD `active` TINYINT(1) NOT NULL DEFAULT '1';
UPDATE `ru_attribute_types` SET `active`='0' WHERE `id`='2';
-- -----------------------------------------------------------------------------
-- 07.03.2018
-- фильтры
-- -----------------------------------------------------------------------------
ALTER TABLE `ru_filter_types` ADD `active` TINYINT(1) NOT NULL DEFAULT '1';
UPDATE `ru_filter_types` SET `active`='0' WHERE `id` IN(2,4,5);
UPDATE `ru_filter_types` SET `title`='Атрибут' WHERE `id`='3';
-- -----------------------------------------------------------------------------
-- 07.03.2018
-- бренды
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ru_brands_description`;
DROP TABLE IF EXISTS `ru_brands_gallery`;
CREATE TABLE IF NOT EXISTS `ru_brands_gallery` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `href` varchar(255) NOT NULL,
  `fileorder` int(11) NOT NULL DEFAULT '1',
  `isdefault` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_pid` (`pid`),
  KEY `idx_fileorder` (`fileorder`),
  KEY `idx_isdefault` (`isdefault`),
  KEY `idx_active` (`active`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
-- -----------------------------------------------------------------------------
-- 07.03.2018
-- серии
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ru_series`;
CREATE TABLE IF NOT EXISTS `ru_series` (
    `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `brand_id` INT(11) unsigned NOT NULL DEFAULT '0',
    `title` VARCHAR(255) NOT NULL DEFAULT '',
    `seo_path` VARCHAR(255) NOT NULL DEFAULT '',
    `order` INT(11) unsigned NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`),
    KEY `idx_bid` (`brand_id`),
    KEY `idx_title` (`title`),
    KEY `idx_path` (`seo_path`),
    KEY `idx_order` (`order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
-- -----------------------------------------------------------------------------
-- 12.03.2018
-- виды печати
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ru_print_types`;
CREATE TABLE IF NOT EXISTS `ru_print_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `descr` text,
  `fulldescr` mediumtext,
  `image` varchar(100) DEFAULT NULL,
  `meta_descr` text NOT NULL,
  `meta_key` text NOT NULL,
  `meta_robots` varchar(63) NOT NULL DEFAULT '',
  `seo_path` varchar(255) NOT NULL DEFAULT '',
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  `order` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_title` (`title`),
  KEY `idx_order` (`order`),
  KEY `idx_active` (`active`),
  KEY `idx_created` (`created`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
-- -----------------------------------------------------------------------------
-- 14.03.2018
-- цвета
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ru_colors`;
CREATE TABLE IF NOT EXISTS `ru_colors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `seo_path` varchar(255) NOT NULL DEFAULT '',
  `hex` varchar(255) NOT NULL DEFAULT '',
  `order` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_title` (`title`),
  KEY `idx_hex` (`hex`),
  KEY `idx_order` (`order`),
  KEY `idx_path` (`seo_path`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
-- -----------------------------------------------------------------------------
-- 14.03.2018
-- цвета
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ru_sizes`;
CREATE TABLE IF NOT EXISTS `ru_sizes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `order` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_title` (`title`),
  KEY `idx_order` (`order`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
-- -----------------------------------------------------------------------------
-- 19.03.2018
-- цвета
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ru_size_grids`;
CREATE TABLE `ru_size_grids` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
-- -----------------------------------------------------------------------------
-- 21.03.2018
-- типы товаров
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ru_product_types`;
CREATE TABLE IF NOT EXISTS `ru_product_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `title_short` varchar(255) NOT NULL DEFAULT '',
  `price` float(11,2) NOT NULL DEFAULT '0.00',
  `dimensions` text,
  `meta_descr` text NOT NULL,
  `meta_key` text NOT NULL,
  `meta_robots` varchar(255) NOT NULL DEFAULT '',
  `seo_path` varchar(255) NOT NULL DEFAULT '',
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_title` (`title`),
  KEY `idx_price` (`price`),
  KEY `idx_path` (`seo_path`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
-- связь типов товаров с размерами
DROP TABLE IF EXISTS `product_types_sizes`;
CREATE TABLE IF NOT EXISTS `product_types_sizes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `size_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_tid` (`type_id`),
  KEY `idx_sid` (`size_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
-- связь типов товаров с цветами
DROP TABLE IF EXISTS `product_types_images`;
CREATE TABLE IF NOT EXISTS `product_types_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `color_id` int(11) unsigned NOT NULL DEFAULT '0',
  `img_front` varchar(255) DEFAULT NULL,
  `img_rear` varchar(255) DEFAULT NULL,
  `placement` varchar(255) DEFAULT NULL,
  `order` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_tid` (`type_id`),
  KEY `idx_cid` (`color_id`),
  KEY `idx_place` (`placement`),
  KEY `idx_order` (`order`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
 -- триггеры обновления типа расположения печати
DROP TRIGGER IF EXISTS `product_types_images_bi`;
CREATE TRIGGER `product_types_images_bi` 
    BEFORE INSERT
    ON `product_types_images`
    FOR EACH ROW
BEGIN
    DECLARE placement VARCHAR(255);
    SELECT (CASE 
        WHEN (CHAR_LENGTH(NEW.`img_front`)>0 AND CHAR_LENGTH(NEW.`img_rear`)>0) THEN 'both' 
        WHEN CHAR_LENGTH(NEW.`img_front`)>0 THEN 'front' 
        WHEN CHAR_LENGTH(NEW.`img_rear`)>0 THEN 'rear' 
    END) INTO placement; 
    SET NEW.`placement`=placement;
END;
DROP TRIGGER IF EXISTS `product_types_images_bu`;
CREATE TRIGGER `product_types_images_bu` 
    BEFORE UPDATE
    ON `product_types_images`
    FOR EACH ROW
BEGIN
    DECLARE placement VARCHAR(255);
    SELECT (CASE 
        WHEN (CHAR_LENGTH(NEW.`img_front`)>0 AND CHAR_LENGTH(NEW.`img_rear`)>0) THEN 'both' 
        WHEN CHAR_LENGTH(NEW.`img_front`)>0 THEN 'front' 
        WHEN CHAR_LENGTH(NEW.`img_rear`)>0 THEN 'rear' 
    END) INTO placement; 
    SET NEW.`placement`=placement;
END;
-- -----------------------------------------------------------------------------

-- ----------------------LENA user3---------------------------------------------
-- 22.03.2018
-- каталог товаров
-- -----------------------------------------------------------------------------
-- удаление неиспользуемых таблиц
DROP TABLE IF EXISTS `ru_assortment`;

-- каталог (модели)
DROP TABLE IF EXISTS `ru_models`;
CREATE TABLE IF NOT EXISTS `ru_models` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL DEFAULT '0',
  `brand_id` int(11) NOT NULL DEFAULT '0',
  `series_id` int(11) NOT NULL DEFAULT '0',
  `size_grid_id` int(11) NOT NULL DEFAULT '0',
  `article_id` int(11) NOT NULL DEFAULT '0',
  `pcode` varchar(32) NOT NULL,
  `title` varchar(255) NOT NULL,
  `colors` text NOT NULL,
  `sizes` text NOT NULL,
  `meta_descr` text NOT NULL,
  `meta_key` text NOT NULL,
  `meta_robots` text NOT NULL,
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  `seo_text` text NOT NULL DEFAULT '',
  `is_fast_print` tinyint(1) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_type_id` (`type_id`),
  KEY `idx_brand_id` (`brand_id`),
  KEY `idx_series_id` (`series_id`),
  KEY `idx_size_grid_id` (`size_grid_id`),
  KEY `idx_article_id` (`article_id`),
  KEY `idx_pcode` (`pcode`),
  KEY `idx_is_fast_print` (`is_fast_print`),
  KEY `idx_order` (`order`),
  KEY `idx_active` (`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- товар
DROP TABLE IF EXISTS `ru_catalog`;
CREATE TABLE IF NOT EXISTS `ru_catalog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL DEFAULT '0',
  `color_id` int(11) NOT NULL DEFAULT '0',
  `pcode` varchar(32) NOT NULL,
  `seo_path` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL,
  `price` float(11,2) NOT NULL DEFAULT '0.00',
  `print_types` text NOT NULL DEFAULT '',
  `order` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_model_id` (`model_id`),
  KEY `idx_color_id` (`color_id`),
  KEY `idx_pcode` (`pcode`),
  KEY `idx_seo_path` (`seo_path`),
  KEY `idx_order` (`order`),
  KEY `idx_active` (`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- добавление в настройки параметра курса евро
INSERT INTO `ru_settings` (`name`, `value`, `require`) VALUES ('eurRate', '33', '0');

-- добавление связующей таблицы модель - атрибуты (вместо product_attribute - пока не удаляю)
DROP TABLE IF EXISTS `ru_model_attribute`;
CREATE TABLE IF NOT EXISTS `ru_model_attribute` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL DEFAULT '1' COMMENT 'Attribute ID',
  `mid` int(11) NOT NULL DEFAULT '1' COMMENT 'Model ID',
  `value` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `idx_aid` (`aid`),
   KEY `idx_mid` (`mid`),
   KEY `idx_value` (`value`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Model Attributes Values';

-- добавление связующей таблицы товар - размер
DROP TABLE IF EXISTS `product_sizes`;
CREATE TABLE IF NOT EXISTS `product_sizes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '1' COMMENT 'Product ID',
  `size` varchar(5) NOT NULL DEFAULT '' COMMENT 'Size',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
KEY `idx_pid` (`pid`),
KEY `idx_size` (`size`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Product Sizes';
-- -----------------------------------------------------------------------------
-- 02.04.2018
-- каталог товаров - правки
-- -----------------------------------------------------------------------------
-- замена путей uploaded 
UPDATE `ru_news` SET `fulldescr` = REPLACE(`fulldescr`, '/uploaded/files/', '/uploaded/media/');

-- переименование каталога
UPDATE `ru_main` SET `title` = 'Каталог одежды' WHERE `id` = 18;

-- достпуы
UPDATE `users_access` SET `modules`=CONCAT(`modules`, ',catalog') WHERE CONCAT(',', `modules`, ',') NOT LIKE '%,catalog,%' AND `uid`=0 AND `gid`=1;
UPDATE `users_access` SET `modules`=CONCAT(`modules`, ',models') WHERE CONCAT(',', `modules`, ',') NOT LIKE '%,models,%' AND `uid`=0 AND `gid`=1;

-- картинки
DELETE FROM `images_params` WHERE `module`='catalog';
INSERT INTO `images_params` (`id`, `module`, `aliases`, `max_width`, `max_height`, `crop_width`, `crop_height`, `crop_color`, `title`, `column`, `ptable`, `ftable`) VALUES
(NULL, 'catalog', 'a:4:{s:3:&quot;big&quot;;a:2:{s:5:&quot;width&quot;;s:3:&quot;540&quot;;s:6:&quot;height&quot;;s:3:&quot;620&quot;;}s:6:&quot;middle&quot;;a:2:{s:5:&quot;width&quot;;s:3:&quot;260&quot;;s:6:&quot;height&quot;;s:3:&quot;299&quot;;}s:5:&quot;small&quot;;a:2:{s:5:&quot;width&quot;;s:3:&quot;101&quot;;s:6:&quot;height&quot;;s:3:&quot;116&quot;;}s:5:&quot;thumb&quot;;a:2:{s:5:&quot;width&quot;;s:2:&quot;72&quot;;s:6:&quot;height&quot;;s:2:&quot;83&quot;;}}', '', '', '540', '620', '', 'Фотогалерея', 'filename', 'CATALOG_TABLE', 'CATALOGFILES_TABLE');

-- настройки модулей
UPDATE `modules_params` SET `order`=`order`+1 WHERE `order`>3;
DELETE FROM `modules_params` WHERE `module` IN ('models', 'catalog');
INSERT INTO `modules_params` (`module`, `title`, `short_title`, `seotable`, `seogroup`, `images`, `access`, `history`, `menu`, `order`) VALUES
('models', 'Каталог одежды', 'Модели', '', 0, 0, 1, 1, 1, 3),
('catalog', 'Каталог товаров', 'Товары', 'CATALOG_TABLE', 1, 1, 1, 1, 0, 4);

-- -----------------------------------------------------------------------------
-- 01.03.2018
-- структура разделов
-- -----------------------------------------------------------------------------
ALTER TABLE `ru_main` ADD `separator` TINYINT(1) NOT NULL DEFAULT '0';
-- -----------------------------------------------------------------------------
-- 03.03.2018
-- подписка на новости
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `subscribitions`;
CREATE TABLE IF NOT EXISTS `subscribitions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx_email` (`email`),
  KEY `idx_active` (`active`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
-- -----------------------------------------------------------------------------
-- 04.03.2018
-- методы печати
-- -----------------------------------------------------------------------------
ALTER TABLE `ru_print_types` ADD `min_qty` INT(11) NOT NULL DEFAULT '0';
-- -----------------------------------------------------------------------------
-- 13.04.2018
-- типы фильтров
-- -----------------------------------------------------------------------------
UPDATE `ru_filter_types` SET `type` = 'int' WHERE `id` =1;
UPDATE `ru_filter_types` SET `title` = 'Цвет', `type` = 'int', `colname` = 'color_id', `active` = '1' WHERE `id` = 5;
DELETE FROM `ru_filter_types` WHERE `id` = 6;
ALTER TABLE `ru_filter_types` AUTO_INCREMENT =6;
UPDATE `ru_filters` SET `tid` = '5' WHERE `tid` = 6;
UPDATE `ru_filters` SET `aid` = '0' WHERE `tid` = 5;
-- -----------------------------------------------------------------------------
-- 10.04.2018
-- каталог принтов
-- -----------------------------------------------------------------------------
-- таблица принтов
DROP TABLE IF EXISTS `ru_prints`;
CREATE TABLE IF NOT EXISTS `ru_prints` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `category_id` int(11) NOT NULL DEFAULT '0',
    `pcode` varchar(32) NOT NULL,
    `title` varchar(255) NOT NULL,
    `meta_descr` text NOT NULL,
    `meta_key` text NOT NULL,
    `meta_robots` text NOT NULL,
    `seo_title` varchar(255) NOT NULL DEFAULT '',
    `seo_text` text NOT NULL DEFAULT '',
    `seo_path` varchar(255) NOT NULL DEFAULT '',
    `order` int(11) NOT NULL DEFAULT '0',
    `active` tinyint(1) NOT NULL DEFAULT '1',
    `created` datetime NOT NULL,
    `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_category_id` (`category_id`),
    KEY `idx_pcode` (`pcode`),
    KEY `idx_order` (`order`),
    KEY `idx_active` (`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- таблица логотипов
DROP TABLE IF EXISTS `ru_printfiles`;
CREATE TABLE IF NOT EXISTS `ru_printfiles` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `print_id` int(11) NOT NULL DEFAULT '0',
    `filename` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `order` int(11) NOT NULL DEFAULT '0',
    `active` tinyint(1) NOT NULL DEFAULT '1',
    `created` datetime NOT NULL,
    `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_print_id` (`print_id`),
    KEY `idx_order` (`order`),
    KEY `idx_active` (`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- таблица настройки по типу для логотипа
DROP TABLE IF EXISTS `print_assortment`;
CREATE TABLE IF NOT EXISTS `print_assortment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `print_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Print ID',
  `type_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Product Type ID',
  `file_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Print Files ID',  
  `color_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Default assortment color',
  `price` float(11,2) NOT NULL,
  `offset` int(11) NOT NULL DEFAULT '0' COMMENT 'Logo top position on wrapper',
  `width` int(11) NOT NULL DEFAULT '0' COMMENT 'Logo width on wrapper',
  `height` int(11) NOT NULL DEFAULT '0' COMMENT 'Logo height on wrapper',
  `placement` varchar(255) NOT NULL DEFAULT '' COMMENT 'Wrapper side',
  `isdefault` int(11) NOT NULL DEFAULT '0',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
  KEY `idx_print_id` (`print_id`),
  KEY `idx_type_id` (`type_id`),
  KEY `idx_color_id` (`color_id`),
  KEY `idx_isdefault` (`isdefault`),
# в пределах одного принта должен быть только 1 дефолтный тип
#  UNIQUE `udx_default_type` (`print_id`, `type_id`, `isdefault`) COMMENT 'Only one default type can be in product', 
# в пределах одного принта каждому типу может быть назначен только один логотип 
  UNIQUE `udx_print_file` (`print_id`, `type_id`, `file_id`) COMMENT 'Only one logo can be in each type in product' 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- таблица товаров с принтом
DROP TABLE IF EXISTS `print_assortment_colors`;
CREATE TABLE IF NOT EXISTS `print_assortment_colors` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `assortment_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Print assortment ID (print_id+type_id+file_id)',
    `print_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Print ID',
    `type_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Product type ID',
    `color_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Color ID',
    `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_assortment_id` (`assortment_id`),
    KEY `idx_print_id` (`print_id`),
    KEY `idx_type_id` (`type_id`),   
    KEY `idx_color_id` (`color_id`),
# в пределах одного принта в каждом типе может быть назначен только один уникальный цвет (не может в одном принте в одном типе быть 2 белых цвета)
    UNIQUE `udx_print_type_color` (`print_id`, `type_id`, `color_id`) COMMENT 'Only one unique color can be in each type in print'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- достпуы
UPDATE `users_access` SET `modules`=CONCAT(`modules`, ',prints') WHERE CONCAT(',', `modules`, ',') NOT LIKE '%,prints,%' AND `uid`=0 AND `gid`=1;

-- картинки
DELETE FROM `images_params` WHERE `module`='prints';
INSERT INTO `images_params` (`id`, `module`, `aliases`, `max_width`, `max_height`, `crop_width`, `crop_height`, `crop_color`, `title`, `column`, `ptable`, `ftable`) VALUES
(NULL, 'prints', 'a:4:{s:3:&quot;big&quot;;a:2:{s:5:&quot;width&quot;;s:3:&quot;540&quot;;s:6:&quot;height&quot;;s:3:&quot;620&quot;;}s:6:&quot;middle&quot;;a:2:{s:5:&quot;width&quot;;s:3:&quot;260&quot;;s:6:&quot;height&quot;;s:3:&quot;299&quot;;}s:5:&quot;small&quot;;a:2:{s:5:&quot;width&quot;;s:3:&quot;101&quot;;s:6:&quot;height&quot;;s:3:&quot;116&quot;;}s:5:&quot;thumb&quot;;a:2:{s:5:&quot;width&quot;;s:2:&quot;72&quot;;s:6:&quot;height&quot;;s:2:&quot;83&quot;;}}', '', '', '540', '620', '', 'Фотогалерея', 'filename', 'PRINTS_TABLE', 'PRINTFILES_TABLE');

-- настройки модулей
UPDATE `modules_params` SET `order`=`order`+1 WHERE `order`>4;
DELETE FROM `modules_params` WHERE `module` IN ('prints');
INSERT INTO `modules_params` (`module`, `title`, `short_title`, `seotable`, `seogroup`, `images`, `access`, `history`, `menu`, `order`) VALUES
('prints', 'Каталог принтов', 'Принты', 'PRINTS_TABLE', 1, 1, 1, 1, 1, 5);

-- добавление в мейн
INSERT INTO `ru_main` 
(`id`,`pid`,`redirectid`,`redirecturl`,`title`,`text`,`descr`,`image`,`image_menu`,`image_icon`,`pagetype`,`menutype`,`module`,`meta_descr`,`meta_key`,`meta_robots`,`seo_path`,`seo_title`,`seo_text`,`filter_seo_title`,`filter_seo_text`,`filter_meta_descr`,`filter_meta_key`,`order`,`active`,`access`,`created`,`modified`,`separator`) 
VALUES 
(19, '0', '0', '', 'Каталог принтов', NULL, NULL, NULL, NULL, NULL, '0', '0', 'prints', '', '', '', 'prints', '', '', '', '', '', '', '0', '1', '1', NOW(), CURRENT_TIMESTAMP, '0');

-- переименование таблицы атрибутов моделей (но перед этим нужно удалить все данные по индексным таблицам)
DROP TABLE IF EXISTS `ru_filter_index`;
DROP TABLE IF EXISTS `ru_filter_index_stack`;
DROP PROCEDURE IF EXISTS `filter_index_update`;
DROP PROCEDURE IF EXISTS `filter_index_stack_update`;
DROP TRIGGER IF EXISTS `ru_attributes_ai`;
DROP TRIGGER IF EXISTS `ru_attributes_au`;
DROP TRIGGER IF EXISTS `ru_attributes_ad`;
DROP TRIGGER IF EXISTS `ru_attributes_values_ai`;
DROP TRIGGER IF EXISTS `ru_attributes_values_au`;
DROP TRIGGER IF EXISTS `ru_attributes_values_ad`;
DROP TRIGGER IF EXISTS `ru_model_attribute_ai`;
DROP TRIGGER IF EXISTS `ru_model_attribute_ad`;
DROP TRIGGER IF EXISTS `ru_brands_ai`;
DROP TRIGGER IF EXISTS `ru_brands_au`;
DROP TRIGGER IF EXISTS `ru_brands_ad`;
DROP TRIGGER IF EXISTS `ru_models_ai`;
DROP TRIGGER IF EXISTS `ru_models_au`;
DROP TRIGGER IF EXISTS `ru_models_ad`;
DROP TRIGGER IF EXISTS `ru_catalog_ai`;
DROP TRIGGER IF EXISTS `ru_catalog_au`;
DROP TRIGGER IF EXISTS `ru_catalog_ad`;
DROP TRIGGER IF EXISTS `ru_colors_ai`;
DROP TRIGGER IF EXISTS `ru_colors_au`;
DROP TRIGGER IF EXISTS `ru_colors_ad`;
RENAME TABLE ru_model_attribute TO model_attributes;
-- уцдаление ненужной таблицы
DROP TABLE IF EXISTS ru_product_attribute;
-- создание атрибутов принтов
DROP TABLE IF EXISTS `print_attributes`;
CREATE TABLE IF NOT EXISTS `print_attributes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL DEFAULT '1' COMMENT 'Attribute ID',
  `pid` int(11) NOT NULL DEFAULT '1' COMMENT 'Print ID',
  `value` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `idx_aid` (`aid`),
    KEY `idx_pid` (`pid`),   
   KEY `idx_value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Print Attributes Values';

-- добавление к ярлыкам даты создания
ALTER TABLE `shortcuts` ADD `created` DATETIME NULL DEFAULT NULL AFTER `order`;
-- -----------------------------------------------------------------------------
-- 26.04.2018
-- каталог принтов - модификациии
-- -----------------------------------------------------------------------------
-- добавление стороны нанесения в принт
ALTER TABLE `ru_prints` ADD `placement` VARCHAR(255) NOT NULL DEFAULT '' AFTER `category_id`;
ALTER TABLE `print_assortment` DROP COLUMN `placement`;
-- -----------------------------------------------------------------------------
-- 10.05.2018
-- выборки
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ru_selections`;
CREATE TABLE IF NOT EXISTS `ru_selections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `descr` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_type` (`type`),
    UNIQUE `udx_alias` (`alias`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `ru_selections` (`id`, `title`, `descr`, `type`, `alias`, `active`, `created`, `modified`) 
VALUES 
(NULL, 'Легко. Быстро. Надежно', 'Выберите товар под печать ', '2',  'home_1', '1', '2018-05-10 00:00:00', CURRENT_TIMESTAMP),
(NULL, 'Повысьте узнаваемость бренда', 'Создавайте подарки и рекламные продукты со всеми свомим идеями ', '2','home_2', '1', '2018-05-10 00:00:00', CURRENT_TIMESTAMP),
(NULL, 'Подготовьтесь к билжайшему празднику', '', '1', 'thanks', '1', '2018-05-10 00:00:00', CURRENT_TIMESTAMP);

DROP TABLE IF EXISTS product_selections;

DROP TABLE IF EXISTS `ru_selectionfiles`;
CREATE TABLE IF NOT EXISTS `ru_selectionfiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `selection_id` int(11) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT '',
  `url` varchar(255) DEFAULT '',
  `order` int(11) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_selection` (`selection_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `selection_products`;
CREATE TABLE IF NOT EXISTS `selection_products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `selection_id` int(11) unsigned NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_selection` (`selection_id`),
    KEY `idx_product` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
-- -----------------------------------------------------------------------------
-- 07.05.2018
-- заказы
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `shipping_id` int(11) NOT NULL,
  `shipping_price` float(11,2) DEFAULT '0.00',
  `total_qty` int(11) unsigned NOT NULL DEFAULT '0',
  `total_price` float(11,2) NOT NULL DEFAULT '0.00',  
  `comment` text NOT NULL DEFAULT '',  
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_shipping` (`shipping_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `order_products`;
CREATE TABLE IF NOT EXISTS `order_products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `type_id` int(11) unsigned NOT NULL,
  `color_id` int(11) unsigned NOT NULL,
  `size_id` int(11) unsigned NOT NULL,
  `brand_id` int(11) unsigned NOT NULL DEFAULT 0,
  `series_id` int(11) unsigned NOT NULL DEFAULT 0,
  `module` varchar(255) NOT NULL,
  `pcode` varchar(32) NOT NULL DEFAULT '',  
  `title` varchar(255) NOT NULL,  
  `type_title` varchar(255) NOT NULL,  
  `color_title` varchar(255) NOT NULL,  
  `size_title` varchar(255) NOT NULL, 
  `color_hex` varchar(255) NOT NULL, 
  `brand_title` varchar(255) NOT NULL DEFAULT '',  
  `series_title` varchar(255) NOT NULL DEFAULT '',  
  `product_image` LONGBLOB NOT NULL DEFAULT '',     
  `qty` int(11) unsigned NOT NULL,
  `price` float(11,2) NOT NULL,  
    PRIMARY KEY (`id`),
    KEY `idx_order` (`order_id`),
    KEY `idx_product` (`product_id`),   
    KEY `idx_type` (`type_id`),
    KEY `idx_brand` (`brand_id`),
    KEY `idx_series` (`series_id`),
    KEY `idx_color` (`color_id`),
    KEY `idx_size_id` (`size_id`),
    KEY `idx_module` (`module`),
    KEY `idx_pcode` (`pcode`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `order_products` (`id`, `order_id`, `product_id`, `type_id`, `color_id`, `size_id`, `brand_id`, `series_id`, `module`, `pcode`, `title`, `type_title`, `color_title`, `size_title`, `color_hex`, `brand_title`, `series_title`, `product_image`, `qty`, `price`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 'catalog', '123445', 'Мужская футболка Панда', 'Мужская футболка', 'белый', 'm', 'FFFFFF', 'Sols', '', 0x89504e470d0a1a0a0000000d494844520000015900000174080600000089c04a33000000017352474200aece1ce90000000467414d410000b18f0bfc6105000000097048597300000ec400000ec401952b0e1b0000001974455874536f6674776172650041646f626520496d616765526561647971c9653c0000c91049444154785eecfdddaf6edb5d9809be8780bf380602248a82a228d84ed2984aa9851a1d5fa4a1088d0d120e9ff789edba2883a912d054baaf72d5049541256ca7d42ddbf903caa960d3c2c72601f5952d2e3a11cd57c2392e89485ca040f037d849dcfb597b3f7b3febb7c77cdf77adbdd6397b1fcf077e1ebff1fb1c73ce31c77ad7da6beff3d497ee70d8d9d9d9d9b915f64376e791f8cffff93fdfd3eeea5ff1155f7178eaa9a72e041ca1facece970bfb21bbb3646b5b60ff2fffe5bf5c8c5ffce2170f5ff9955f793147e7804587affaaaafba18c1437725fa1f57fed37ffa4f872f7ce10b17d7c3b53ece6bdd793cd90fd99dfbf04994c312f90b7fe12f5c1c9a7e32150f54b60d311c3ce4711879e022e4b8b57a98626324179c1b03fd348c0ef5df16ac05e150fdf33ffff38b6b02d6c0b5bdfce52fbfd059fb0bb19e9d9706fb21fb658c070a8724878687230789dbc2c3c4d143143c70388c100f4efcfee800bac5cc05fcd65557ccad4dacb75ae32a1e88359eb5754eac7374ef03711cb6d8b9bea79f7efae2a0b57e05ccdfd929fb21fb658887c63c6c003b07059fdc601e22c6f630b11ee3ea90d18e980f3d70b12bc4e1f3e035c7dace7b4813cf21efe10cd6110f4f75a016ba7dc13cfba073aff891c1577ff5575ff4305e1dd415e68ab577befcd80fd92f1378cc7e3263e4c5e750f240701b70a0f4db61c679401043be90db83ca5a93e95fd5a900f1ac9791d8dad119116b698395cea8ee9acd43fcc2a31f18b1bff295afbc3864ad03e8883a58479d7c7cdc57be7821c6eebcf4d90fd997381e107dd9d19d835b401b9fd88843e700328751bbba39d658d11c20d63c0fd9996f0e76fb307ae05a031d01e2f15b13accba8ddfbc19c1c7fcc81dd4ff2d6c4e63af8710187eca4f1c2cfadb523f673c4c661dbb5eebc34d90fd997203c52c403c243a2be1e0a9d13eb2130f19040d48943fcf18307d664d607f2053b68239e38ed501d88c1466f0e2c72590376747c7e5a6f7d84786cda19e9d76bb43e2387e6d77ccdd75cfc4c569fa3c21c1889a7963efb8036fcac0f61bef3d2643f645f22f012fb498c47ea61018c5b8f599f2fbd87810781baa8b71e36e6f3d31b585b98d3477dd2c3083f32bfc5d6cef5d2d3031e889987d6ec430faf75fa409b35a8ff8a57bce2e2c705ded7d69f50b7f711661fec8876afd17bb3f3d2613f649f6038587a50a8f3f2fa9233d7b685f91e20c0d803c5038171c6599bdf54308e98eac683f1a01d1b6b7604ea71b8f12d7af3a53674f2b8279ffffce72f7ef6eca7c4f600fb68677df674c4c7a1071cb2af7ef5ab2fe6d867bd828f1a8c2b3f980fc6b106d6cae8bddbcadf79b2d80fd9270c1e172fb19f58fb090eb021bea47dbcda411f320fc115d653cc156cae0b5d9b98a30ef606728d41d0f994ca01cbcf88db0be6bce0a31e87f4a73ffde9fb3f63b62e78cdb34ed7261cf41eb8d0187540e779780fea93d66d3ea373847e1eba3b4f36fb21fb04c081c181c3a1d14f39f3d1758e1fb66ccdd70ed894f929d0b8192fd859ab073f34a7b580b987c8f43172bd1cb2acc34318da1398eb13eab28e3ff9933fb9ff73d41eace8d4443c409d833d5ef6b2975d1cd2e6427b6163ae8f7c759979d3dffcc275e3a3f77ee03eb9ec4fed3184178e4f437ff6677f76ff5b5f6c7e2233a6527c6911e361f5324f1bf39923c6d536e1c0f20083d9ef18f656b409b5566b40a7df7bdffbdec3effddeef1dfef00ffff0e280060e580e28ee9d07e6ccc5ef21660fc43cecc42913d7641eace200fff4350fece7a762447d7588ef3cdeec87ec63042f0f2f12070423735e2a84970cd1860ebef8d83a576a2377da5760e745b7878726420d049b621dd7c6418b74bde6216535b726a33057c05a5e37fa073ff8c18b7bf7833ff88387dffddddfbd1f6f0f47eb5a8b2f6608b9c4f80778fc4197f9f601f3b0a31b53211e3bbdfa29d4dc8e0831423e60a70ef791b5f1c59691f5796f771e7ff61f17bcc8f012f1e2f092f328f8b4e50b087d3cbe74f5ab378e179ab93662d01db59d821799380f086b3037df7a306b93cf7a11c0ae4f5c2be86324872f34fcda94f3cf7dee73173a3f23d5d61cbf40fde99ffee9e11bbff11b2f6a93c301660fc6a993cbc87ac9f153afd70df65941aed768edae0b5cabfef66ded6376a88ff521e83dc8771e2ff643f645c0c3c0178f1784d14f27e8beb4e8dad485b9acece6ad98be556dd6601c2fb06bf2c586d669be2f3c9fbafc64088c8db796071c3a760f780f590ecb6ffbb66f3bfce88ffee8857cd3377dd3858d1cfe70ebaffdb5bf76f1c99335da1bbb751c81180f5ed78f1fe10047ac01c620e8e21c21578c05738d6b8ddac53c69bccc39d7e2214bbcb2f378b07fe97b81e0c5e645e460e0532b735f067dc24bd4974529abb9e24bd8b93565a5d7063df4403feb458018eb378eeb21864f857ef2c46f2e621c5f749857e760ee1f483dfffcf3177f88f577ffeedf3dfcd5bffa572f0ed4affddaafbd3884bffeebbffef06bbff66b17f7951af646fac50c1811fd88bda8e78f088c2dc47a8de09cd1ba33079ab3827be8bd06e215aea558abfd88e1470908f7c07b78aaefce0bc37ec8de226c725ef24f7ef29317c2018bad2f0873849762a27f827dd2b8e96f2f698c6b9810c3cb0fae6fd626cf0362fac003a8df821b37fbaa337270f413fe3ffec7fff8f0fad7bffef0aa57bdeae230e17e1a4b0c87edbffdb7fff6624e0f460e1bd72fbdcff858137f5dd6bf68d06b703d057fed33a6f9c05c9bb9dad4a57115689fdaace135035f34d8775c3ff7ceb89d1787fd90bd61d8cc6cf2cf7ce63317872a9b9c3fe146e60b8f0fe64be04b544ebd28ab1cc1b7f26ff599f4053e95a3bf71f8a9e11f887978fa696bf6c48ef82d3db9ffe01ffc83c3a73ef5a9c31ffcc11f1cfef88ffff8228efbe727b7bff257fecac5c122f8b05b1fb11fb00e0e589e8bbf4120f31a999b57ac2bf39aa71fec837dd655c76ead19a37dd62df8b87ef621d2fbb0f3c2b3ff4cf6867063b3a1a107082f06735f98be38bc74b54f8c83fa5b63c5562dedb3dfa97ac0f511d78302acd31ae8f39aa5f9c450175d3b072707260727e8e3c0f8f7fffedf1f3efad18f5eccbffffbbfffe2a0249e986ff8866fb8ff69d9ef20384cadabcec87a5c13b91d61fabc8eeaac5d56f5a03a90e37af099337357358c29ab78e99ac0eb47a66fe7f6d80fd9478403c2df63e593172fd1dcf86ce6a9b3d1a7ada33446d4f54d8ed513ed33c69ad3cee861589ba379a5767546f3982bb5d307f8592ef0858b7b8be043b87f7e22365f9893c3e16b8cf7bb7dc4397e98fed6378671c633d6a62ee6cc5ab0ca5dc56d410cc2754af3a8653d627affb4efdc0efb217b0d3848f95405dc3e37693f9575e3ae36b1b6c6aacffc49e32733ef58add6993599d7ce4bc9b5f9e9cd97d918e3a475845ce6cd15eced07fd82856e1ea3b5dabf6b6b1df5d94fd0eb2bda5b632bb675563d055b63e788aff9c7eaeadbf28bf68e0807adf74bdfcecdb21fb257805bc5e1eacb8ff862839bd44dab8f386cc6a30bb66e6e377beb0af619ef9c111a33edd46404d7a3bfb83ee367ae9f349913dbb51ae31ad45b93171bec6f9cb826ed8ef669fccc85d674d4664fd6c0d8eb82c6c39c37be7962dc16b31eac72dabff7b7e04356b5567d662cf05cb0cfc3d6fc9d47673f644fc0ede15b4f0e5774371ffaea803106e9a1d0dc897662c0bae6c98cdbaa87bf871a715bb5007be7eae64e8857f023d65ec54363c4dc1533b66cf55ad5d2e65ab772a7bdf18c7dce30f38d3d867e6b9655ae35a7aff98cf33919af6dcea135c43dc3818be09f313b57e7c147aa9d4bf0698dc315e90bd60dbf3a849813d3b8c6a8cf11d091d63d16278dd9f2f95229ae71aed3f8d25ce8271ef37d415b0fccb1c6b9187b2c879a9573e87a66ceaa06f157a95f9a537d5ed3ac8dbf31abb9ccbd3263616503fa626744a885b8f7fdf1d05cdfced5d83fc90eb81d6c2e379ab66e54e7c048acfe554c6fb176470e2774e7fa10d006da64cb2ed69a3a58171b622dc7dac038a89d9750b896cec53eedd11a93556fb08e1863bcfe995f662c5407bf60f05c2733178ed5aa0e3316986bd7b6751fa539d683d66c3de8dc9cd6f0bac5787e2b61fe46c6cef9ec87ec3dfc9100b8e980cddecde7a65c6db61e32c64d9aaf7fd63ae693ad3514fcacc7387310ebc3b4af6aeb5be53a36cf517fedea57a57de1541dfced3ff3c53aaedf38e7136356351b3f7d2b5a67556fb2d56fda9d6ff927f867ac7340e74708fb617b75beac7f5cc066e260f5c702dafa29c2cdd471b5c18ef9b5adecc5b91bbe7341d75ffb56ad2dac03ed05ea8d81558cb94863cbb43b5fc5cf3a3366d50f1d66ecaabe58035a479d71f650ead3660c36a518672c18639d73b0065fd019ade15c8c5b51bbf9ab5c7c0aef04ef08bfaec83bc3a77c73778ef365fb49d61f09b079fc767fde8a6e3c7103f613ab90efa75e6b591731f7541fe6fd042aad397dc623ad6f5f70d4d6b853b9d3de97bc31acc3b131de97b94e7598f315c49ca27566ed99cf5c5bfb1adb71853ee3ac755bd883fbc9bd047bab03f3da272bfbac63fd89b5fd64eb1f92edacf9b23b64fde4ea2672c3384a6f4bedeac6231e1c527dc556cf6318e7ba66be76d0b7a2f1ea65d63c15537fef435f50f47ef12186b9faec33e7ea8d2f335eb6f25635409b6b3dc5565f997ee7abde65e5b78e3eeb58b334465635576cc5adfa00760f5bee1b31abb82f67be6c7e5ce08f04fa63016043ac36961b656bc33cca466a2ebddb7fceaf4b7bcc7ef028d7b5aac7587b5f38e4d875356f8baddcc956cf73f2cd5bb165df827e33e7540dfcab75360fdd79f553d7d71ab2b295d667ecbdacce7782fe83347c41dd7f94709997fc27592ecf9f21f5d315f6733eb17473a97b80c0aa26e3cc03e7455f31bf79ab7af436ce1858f501fd722a1e5631f63ba517d68a0ff11e21c455403b788d602ed4ee08c448f519d33958b739a758d559614d628fad69c5ecb1950fab1ac6e863de3dcb7ce6d9533be3eafe6cd9f9648be0d3ffe5cc4bfa90f5dfd8e4814fe60695dae7add1e788bfbab4c6f43b6237df180f8e59b71bb5f9a539b2b26dd17ec05c1da67f457bcd5c5f6c635a0f7dabaefec92a7e2b6ef63c8631b33ef6ad354eda53bdb653cc1c46a92e330656b615f6907372e0541c3515fed19ef6f872e325fb65867f66904fb0fc3376e283f6e14fb66c2bfbdc9cc66973ec66ac1fe61caccb38f5639caa33f3673c3466c65f95d6a71673852f1ad585f939b41e38aed6dc9a33a7fa399c1b57dac3f5cd75aed6bdb295b976e2cd39679de7c4e25326b5ade2a8cf1756841f23f09de4972b2fc94fb21caefc0b4cbcc07e82f24f40b95cc497dbcde126308691186f8fa3f1cea13a3ec7e6f453aa3674d7a19d511f63f5623ce8d3d6d8e6a2cf7ed65f61ee8c69bd950ece3b8231f51dc33c687c6bc2569dadbe2b7846de9fd2be33d77a73ac6f45edc7e2ca566d7460ae5d9fe3c49c15ade7d878edde2fe6f35d711dcefd0b0dabef2a5feabce40e59feb1ecd2cd31752f7d15336f4bed8d87daa62ecd31065137b6f98eb5adf4da566cf957b9ad59b66c5b6cf514ebb56e6d72aace1633a7bd66cd2dbbd48e0e334efb0aeb4f7d629f394eea3fc539315765b5bed907bb1f70c40f3afe26c2970b2fa92bedbf1fca43ef2638859bc4dce69d5b63326bd8636e30ed8e2bea53b7fe5cdf6a4eceb4cfbe1d1b3bf38a39e27cb5864a713efbc29c9f4bd7a53ed73ad9eab5b253eb54bdd26bdc8218fc1d67efe64fdf0a6b54a6fd182b3f36d7575aafebd7c6778bca3c805fcabc643ec9f2739fcf7ffef317ba0fb50f78e2b73a1e786e8ade0e7c6e067d5bf5666ee7e8f62b8d69ecac85beb2177d087d8c9b76d007adb9d219a1ba34c63c692cd7be5567cb2eb5576facbd1d4fd5d962abd716e7c614e2b535b7715b31bd8fa5f1803e6bac7460cea1c7e81c1a7faaafa3b0cffae337c0df9ac47cb9fcf8e02571c8f677f4804bf2a0f1213bc2b4f5e197da91ced5a5bd6015038d31678efa60653fa5d736d1d71767d6e828c6407b55bf0aed03b3e7756abed0b8d6b25af7a97bd43abd07e01ce917cf32730af6d9bbb1c772b7d88adfaad56ba8df03f6a5fedb074ffc8f0bfc934b0fd872ecc16d6d942d4ec5b7d7dc4c3263e4a637d8b1b5bab6d9b336466ba09fbbbec6add670ccafefdc5eb745d7b5a59773d67b2cc6bac4dcf4b55fa5e68cddca3b751f5679bd4647c41f1df8b7305faa3cd19f64593abfaac5018beec3eb43f55b64605e3a371fa6bd3ef5193f631cdb1f8c35de39d4e6170df49bc45e5d977d275d1ba86fad09bf9fb6ace9b8c531df55b1567b2370937d605ed7569f193739b6bee6aef4e6aa83bacf98b93993da5a039c6fc59c5ac38c95ee6d757f74c0f852e3893d6459367fd0c557422fc1073d0f36c0d78dd0870e73dedb327d5bb4becc7e8dd1c65cfbb41dd3cfc1ba2b1dcead774e5c6b16fbcefc737bdf04e7f47a21d7730aefa1eb716dd37e8c793de64eacdbfa65d681195fbdbe554ce31cf129fe07345f2a3c7c1a3d2170c0aee843dd82983273d467dc8aad7ecdddaa43dea9b516eacc5ad6d8ea01fa8cebfc3aacf266cd559f99a77fe72ee7dc9fabdc33625bd3dc39cab9cf87b8d69e715bf50bbefaa9e507a6cf7ef6b3cb1fff3da93c919f6459327fd885f8a078283e386dc4f9a9b636681c60d7366f893e59cda5f6c92ae7946d05fef684dad467dc8c398535caac3963f4cd1f47c0393d6f8ab9ce1792de93d5fdb9ea7d78d4fb774e4f7b9ca271d4dccab31f7efb1baf480f547dec9f57bef295cbef4a9f349ec843961f9473c032fa7bb1e0c8439b87ab632f571ba0e3abcdd8da003bf5eb6fddc9aa36ccbcc6ace28f611d72665da9bdb5675f698c34768bd9a735e154feceedd1e7b2c57c5eb07aeed3b6355fd5030f573f2011a76043beeeebbeeea1be4f1a4fdc97091e807f22e943823e88f960abe3536a87d680390773d565653bc5b1fee7d49931cc57b6ab702c7eae7782dffc551d6da7ea4cae7a0d8f2b57bdeedb807b79ec7efa0c5731d77d6ec7fac1f4f35ef321865fedfae4273f79cffae4f2c41db23c000e58047c40730360c786f8a953d1efa75d6cb38eb1dd00eaad8df85517bdd4dfb1719d4f7fe3c0be8ceada6b6b9e7aedcd95fa9015ed31d1c6a858672b6fceb768ad73b1e755f36e8ad5b5ddf65a4edd4ffcc762b67cae9bb131ce95d5f5198faf7ef6baf3be87e0fbca8729fe8127dff5279527eac7052c951bce0fc619f951015fed7c48421c0f11faaf706d5daa0f7bb549b620967ac8d6616d8c5ca5fe55d8ea531bccb54dfd2acc9eb255d7f9ccb94eefabb0b5ce178b795fae8ad7f2a8f7adeb583d93d53dab6de69ebb1ee3195baf73463f70f08e333efdf4d38f7ccd2f164fd427596f7c99bfc225da1891fae6c3c23f6baf6a95956db5094ed57914a8bdeab9a271e8ace53aebb1cecc9df6d5baf0adec3b8fce559ea5b15b39a76ab97f6e82ad5aee13de6f74fe0ce649e5893a64b9e1fecd103e3df269169bac0e611e900fd20727d814307fdacda3a7f50a713357fbd49d037ad7dcba8d3b87aeab7da6adf6f613fd32e3eadfb2b74f75e21b27ab755c95f62b5bf615e7c49ccb562daff5aabd4ec59f730fa98110bb8adfaa61effaad31a598675f685c47dfade29c7784f7bceffa93c41373c872b8fab0b8e9e0df0ee90304e7608eb27a98c543cf1e8ee45417f55973c620b337f3daac4fffc61da3d706e63122d652c058c7153376d68063f930fb30365f669d558ccc3588b6ad5ceef3298ef5bd2aa76a5db557afef3aebec3d46ef3391f91c847ee6d8db7971eeb85ab379cec5f7ce5c300f1befbf67c093c61371c8726379081e4280ad0f4e7c288cc75eac79e0817d1815edea2beaabbeaa5f5a77fa566cc560f7ba8fb1f29337d729c6339eaabdc2ba733cc575aee371e636d6bb55f358af73efff3950abbdd0af729de7c6d207f1fd7f123fcd3e1187ac37d807c94de766fba0f0b9818c4540bb0fab0fd75a1cb8e6605b7dc56c9de9f33037063f42bdda44bf3646e3a07a99f5cdc7de5a93c6b586a0cf9eb30ef3da5aab4c7bf5558d693bc5a9d8d59ae02a3d6e9aad359d62e6790d1da7687744dc87d30eabb561db92d6e88718e6f8c1519abbea4b1d7dadd118747e3ce87bfd24f144fc7681ff94214be570e53f8ee803f14f1f5ffef2975fcc7908c4f2e056ff02bb973b1f2079486f077665759016d7b3457d33d6b9358fd529b346b15ec7b2b2c1b4abb77efdb5cb96ff58adf6655cd57dd2e9359e62de23a487e5b975e4548efed9576a176caec939f0ae605be533ae6aad3e3479f80223767e5b887fdb60bed78f338ffd4a3930e7e107ccfb20e7b7117dc0d527f5f9e0ece78375dcc2b59c4b63c92dc77c72ea7a90536bba6e9f2d9b7646eb39d67f0e5beb79d2b9ca3d80eeb93ecf6375b67ca77a6fd5ee339cf439bbbe557ee38098193ff344bbb99c074fda8f0c1efb43961bca8d15369e0f47013eddfad5d007029d3b92a3dd8dcc2762c4afc2603c3148ebcc18c70976c4fc559cd7614d9973698deac4dbc3dcea65e6c98c9df5b658c53047da4b5d5f69dca330ebbe50acfa1ebba6ad756a673c76bf566cf53b27b77dc15a8cadebfb00ae4b59bd3f65d66aae76f3e9d15ac6f19edbff49e0b13e64fd198c9f30fb80b0cf87d24356dbfcb642bba350cf07874f01fb301a631f69bd699f6ba8df3a4a99f3425dfced0bc7e6f6d0c688f4bac13830c6b9bec680b9d078a80f9cf75e629b71d7a5bdafca4de71ebba6ad5ede0ff6cd6aef9fc38c3d96abafe37c1ed87c47d03bd75fcc7534a74c1bb1d603e6f819fdb120f09e234f0a8fed21cb0de567b17d08e2836003eaefc37763fa90ae829b02b672b537569a83ae4ca67d15b3aaffa8f49e38ce6b66fea8bdb7f26b47b7dfe3c26d5df775b96ebdabe419cbd8bda15eb42b5b7d9adbf8631863cdeaa0eee1de03f971e6b13d64bd81f3c1f8ad827e6f3c072b9f7c89e7ab5eb1c67c48d811747cca041b715b319dd7ef687ffba94be390550da80ecc5b53ccd72fe63bc2b17ecd9d759a834f29cc7d4e5bb4ce6d60ed533de6daafcbb13ad7edf128f7a7b9f4770dea5d537558c500ef1a755bdb0f3cc6aafbfcd1f9b19f39d6300e01eb4e3f6003ce003e803d299f661fcb43969bda9fc37a93197d983e841e960ae8873eacf9d2e3b38eb5cc2be6aefa29c689b1507b6d0871c53e731dc60bbaeb9f18575f73b15b4f7dab67997ee60a58d33c9fd78ae66c71cc770ee69faa33af0b56b615e7aef1dc7a40cdca75693efdbb863997d96fae019d3cdf316b74aeeef347ba17b4198fd45e7ff3accb01cb19a1fd71e6b13c64fdb4da1b38756eb4b6eae0039b68776c4e997efbc956af59af3e683da5f3eab595536b2f5b31daa98574be953363b7e24e71d53c7abe589cbbd617738d8fc27c968c48af471b323f0cb82726c402bee671408a318df50085dac57ac6b6f6e3cc637dc87233bdb1de50c63e2ce8cd9e0fd21c6b21a2ae1ffaa09b0b8c880f58117cb5b9a6c65853986b43ea9fb1e0dcb548756b4d29ce5db3cc78eb76645dd038c03765c5ccdb622bff49c0b57b9de75eb3903ff7f9e4543dd7d0b5cc1c6ddd6b8d73ec1e85ead06785dd7846756290e6017e51ef3b267d37f57156cc7a8f1b8fdd21cb4d43b8997d400836fdcecd7143ce1b4e8c71806e4c7570de876e9f3e74fc7de0a04fbd35c0eb30c718e6f66dbe10d35acddfca716d2b690ea0632fccb7aecf78a5e887e993addc155eb735cfe5aaf1b7c1bc0fe75e73e975a0cfeb5ad59b3965ae01ddf706f439369fb8c6764e9cfbdb1c7bb526825f9b340ed48d47e8656d477a7a1e3cce3c76872cff81446fa20fd187c04d9d3631a73663f0cd07b4a235cc51b48b36987ae7726c1dabf86318ef7a98776d2b88316ec65eb5ff56afaee7aa3557586fabdf16578d7f31b8a97b3e3915675fe398a33b3f95bff2b756af0bddc3d779315e9950173b35dab73abef6781c79ac0ed9de4c6f1a87eabcc14a1f8e31f58b7362eb73aeee686ce91cbd22ead60463da1756faaca574bedab4b5538b511f5497f65f316b003933efd45cac57b90ecd3da786f1e7c4be10b88e53f77f457356d7336d5eb7071152bb3eea22ccf53b1a03c4f03efa4107df6a4de620fe4681a2bf734740b71f906f2dedf6076a218ff3a7d9c7ea90ed3f00d107d29bcecdd4e78d37c61bde4da0f4416a536ffd553c78d823e8e6cfb863ccd8cea76f6b1d9d770dda8f5d0ba33673a135a1f58a39cd5de9ede37c25a5758ec1bdf7f9b6c64aa7a6bdea7fa159adad5c756d5e97f7589936b1feecd358c47cf690df86339fb1d2770e69fdd6f279b9379d8b35cd5127ceb5b4be719e05c4b896c791c7e690f52b56f1667a13bdc9c25c9b0fc11cd0a7cdf9391c8b3fe63bd6c375adb8c9f515628c3dd65fac792a567fd730d733e737017dcf5ddbe3c24daeb7b157b9bfab586cc76ae8db8ae9173bd6a5083a31486b34063a9fb5f4ad6a38376e9e1f8f0b8fcd21eb574ef0a621d09b37bfaae173ee0382ea80be8a35069f7db4eb8369237fc5961df475c350cf75b576e7c720d678a9de6b02fbc256fd590bb18f543f853510d7731dac31d9b29fbbc655ee6d7395b5293e03c435b78eb6c68b79e2dc8312cc939923da1ce733c5ee9ea6be35672de68a31aec77cd0d678fec211a3782e98f338f1581cb2dcd01e9ede3c6f18ffbc19377aebe73b809f1b6fad3ea4d66d2ee06b5ce72bcc5fc5a2dbb776a8cd6b056a6dfdc334b0d21b473fc47589ba1bb434aef5a13e987358d98a351967fdb91e634ed50462aeb39e636cd5bc6d56f76685eb43bab798af6a34be3ef749735b4fd9829c8e30e33bb79f7b5b9b50c7f7d49ae63327c7f7a4ef0618c7c89920d67bdc782c0e59ff8d026e646f746fbe769876a50fd3b975663dc1661e58cb5847681e4c5fe7c720ce58d73a99f58c39b586c9ca56c86fcc8c3f95bfa26b82397f9cb8cef5dd14e7de17d6b87a2eb5a977ac4ca66d15035b7669fdc6a27b7dda7bbd3316f02bbc173d80a531f63086f87e80795c78d10f596e88078d370db001736f38f406f2558c39379683d2af9accad03d6166ae1f71096eac478f8d60ed6da1ac5bc993fe75e1ff9cd615ec1a67f2bd6b9a3767da5718ec66a9373f24fb18ac3762c7f6bed9373d7f0a4b1ba766cf37de0fabd57b589ba718cf59b23ab3a8d576f1e36f3565298af6a08efb69f5289b336f05e2a30ef85f3c78517fd90e546ce0306bca9f87a83c1b81e82c671d042ff4bb6c65bc3181f86f59d5bcb5188b10623be8a185711e2b499d31a8d85e95360158b8d7523e8e7e4cf91d815e62ade2ff326ed6bcdc66ee9c51ab215b7657fdcb8ea3abd7eef813a7bbf76eb3a87552f6d8c0a344f9d913e8d83e6ba07c01847dfb3da1530d76bd10ef6a6467dae6dd662c4c73bdf1f213c0ebca8872c3704f146156e983774455f70e27ca0e8fc67673cbc27adeb680de7a0adac361ccc796bcd1ae663775cd19ab3bed46ebdabb0b54658d522aeb1ab3c993eeacd9ae7d62ae7c63dae5c67fdf3de5db78679f339ace68d076d33b6108f18d31ad5a5f53a56cc691d58cdd179e71d1f175ed443961bd14f9b7e45036e14c25c9f36f2fc0a8750039b737cccb77eefd6bad41473eb37c6ded238a88e4fe8bdf2990f732cad75383c7ff8c8bbdf7d78c7777dd7e1bbbeeb558757bdeaaef0df3b7ac5777ee7e13b7fecc70eeff9e8270e9fb8947399d6fbc88f7de7c57f176d0af55676e4652f7bd985ccb9f27ffec5e7ee557fc0e5eb7afef0eeef4cde77bcebf0ec3dcfbc4770fc9e3c7778d7dbbfe37eadef78bb95d6795ffad2870feffa8e076b6def15ab1a7779f6f0f6d6b9d3f7e1ab3e9fe7def5e01aeeca771c722997e8bd4177deb5a22b2bcc317fd6310f5bdf9dbe7f82cd38ecada3aff165c69b83504f21a63da4b9f8c49e9e2b8f032fda21cb8de0d3a69f645737125df1a6f9205636c0de07634d047c38faacd5de7c12b686b9d6379fb97e6dada35dd10fedb5029feb3a3cffd1c37bdef1f7ee1ca8ffd5e1077fe6670eeffbf8c70f77feff32d8def7bec34ffffdd71f5e7fe790fc6f7efcdd878f3cbf7dc8df39620fbffcbe59e4d1f9f83ffff043074eafff70f8fdc3ef7eec9e0a1ffbdd87d6e67a9107790f30feb977bdf5f053ef7d50ec63bffdfbf77baff2b0cddec758d5b8e0b9df3ffc76ebdce9bb6233ff12cf1e7ee1a75a0c3e7678efcfbeebfeb5781f90de1ba5d893bde37e157cec0704d45ba37d3aaf8d71e68131fa9983b1ccbb26fdc59abcd7f8f9ad22a00698633d3e4ca1fbae68076a98f762f3a21db27ecaec8d11e7f3a632ae6e5c6b3022dc7862157dc59a08b4ce6aa31a07cd83eaae6162ccf43517f0b3e64ffcea4f1cfededff9c1c3cfbcff6a07e2c7dff77f3dfcc0b7fe37871fffe83dc33d669fc781de8bb3d7f7dcbb0e6f7de8707a98c7f17acb73effad9c37befe997f8d8070e1fbe77caaef611d48e8e78bd8cc8cc356eeabd4fab7bd6d8c21e9db9be6b137cabda50bb072cef9e3d57fd3b5fd5651d7e087bb179510e596e8a5f698edd4c1f0c3675e3a71ddd43d1386fb47ee3a1b1daf017638ab1e635d7b589ba7d3bd726faacf9fc3ffdeec37ffd43ffecb03a5e9f79cb5b0effd32ffdd2e197eec9fff4d6b71e9e79e69ef33e1f3fbcff07de71f868ea96cbb3670eeffce0070f1f8c7ce8431fba3f6ec907dff9b63b99a7f1dabdc615ae6fae531ed89f3bbcebad3f75e7f3de698ef53bc5d63ab698bdb60e9b073c77f8f007b6aee263870f78ca86adebd17eeef59eaab375eddddfc4201e64b5a35b035d1ff7a4ba18cbc877b7fe28109a0fce895df5027df438fd1c6e9f17e590ed8f09c09bd39b073e546f9471608c0f791e88cc89f13f53313703b417fef940f44d88a38e225b0fd43a1d5bbb352e6afeea7f7ff83ffea3dfb86791670e6ff9a5ffdfe1b39ffdece1d7defdeec38f7dcff71cbee78ebce94d6f3abce33def39fcdaaf7deef0f9dffaadc32fbdb5c7de6f1f7efff9bb9a3deeaef9420daf3fbcee4e9d37bef18df7c5da53f0a97feff7fd1feee587716de772775d97c5fb643dc6e77ef12d87333ec43e32f4bf0af39a9b3f7d17d7f4dcaf1c7ac6bee16d6f3bbce19e0e1ffba95f387c98b8912b5bfdbc774863d4b5b3571915a80ee8c421f3fd02fcfe81b379ed3bd7e13b09d8019be29c18d7d7778ab9ef323ab1d611e2b159ef71f84d8317fc90f5a17193bc41bd91804f1174e3eaa7860fba68f7b70ccc672cd629733ea1f62ac6ebd9423f63632fade113ef397cd70fbdffae7e9fb71cfec56ffeabc3bbbfe735f7e67799fdbef4cddf7c78e3bb7ffdce61fb4b879eb50ff7ba372931babeaeebd21acfa0b1739d57c1b55cf0fcbb0e6ffde99bff59f26d33af9ff9f3bff2817c1a7fc3e1477ef2270f3fd253f6f0dec32f3fbbceed28bddfc79e95f6e6cf5a825d99ef28d8073f233188f556bdc03c7547a49f62c1daea8ae857eaeb7bff62f2a21cb2fc3c16bc41de18f4d2796f14f1c8bc89da151e989f50679cb40776e6e6835f111b57ac0de6cfd8d6c3678e76f3b07ff47ffe1f0f973fc3de39603ffbae03e7abeb177bb5163cf5da371ddefdeb773ed5fed27b0f3f7e27af71f2e00e9c66e6df9f8f1f3adc3172b1f7268f8ed7034f3df5fce1dd6ffde9b37e4cf042d375c29c8bf78f1f15fcca07f2c5e20d3f72f8ded7bce6f0bd974fd9c37b396583cff8f27dd9ee85b067dc6fcee1581e3ef77dfbe1dbc278686dfbb5b77aeb323fb536ec7e7aad40d7a66eddfa5e0c5ef043d68ffbbd49f326d4077d20451bf95b0f9983961f19580321de9e8c3e08eb10e3a81f5a57dd38b086ba630f7a6215e38d7dea7fff5f0eff647c887dcbfff68b87efb9339a0badd31a95c3e13577beb57fcd856eacf970b7e39abbf97779506f7dfd0f2c0fd0d75ca87e8a197bf1db0439619f19df5e9fe22abd4f312bcdda5bbdeedfbf677fe1d00fe4cffcf0f71e5e7bc7f7da77fcdf0e6fbb67bbe0bdbf7ce957dc2a403df43e17e7ddc7dda38eb396237ef6aa7bbee09b7ef326d889b137d21e15e23c13785f99a3930bd630cede057fa19736be9bdd5ae70bc10b7ac8fab358e94def08f8f47bb3f5f7a67b33672e318a0fc79ac69a8f80ba629ca37165da66cec4787b08ebfcd5f929f6db7feef03fdc396189a39ef1eada8fd11ed557ac6a6153ca45ef2b7d1edebe272799bf4df0869f3fbcff27fff6bdc9c3acaef3dabd173c6aa5673fd4df2978dbe1fffe13afbdd0bef4a5371ebefff2297bf8d0bd537675ffbdce53cf557c8ec6cf796bea13f6e7ecb39a9b33f3a5f5d1790711e6f6a84875eb62b38efec609f55fcc9fcdbe28876c6f2637c99b06ceb57910aa83b9a06d057dfc91417f44e168ae366af2a9d79fe30235f0e36bbc6b701d82cf7a509d58e7e63dc8ffe8e197ffd93df51edffec36f3a7cf33d1dec0b8c0a6cf584c655bfc4226785b5678f49fd5bb5ca7c8ee4df5deb73875f7c4b7f9be00d879f7fdf3bee7c4ebf53f7ae61c9393d27d7c9b93acf1e2e9fb16f3ebcf15e5faef94d974fd9bbbf337b711feec6acd678f73edd15efbb73c0a6ace2f401cfc13c63d51d672da57e30461db13eef15f8e1c79fc5b68e7b421b9857db8af602c65339b7c50b76c87a70cd0b75de1b2cf591cbe84df601604380f80a0f0e88f561a29b0bd8b5e107fe3b639ffffce71f3a6cc1daf6d55edac77c6b3b2fd4f9d2f3bf7ff8ed7bf3bb7cfbe187bee76f5c6817fe7bf960ffb926690fe2e6a87e8951a331addf9825f76291cd5e0bb896559f677fec5b2e7f6bfdcef71d7efc35db6b306ff6d57e8c73d77a552ef57ef643977e37f66d6f7ed33ded2e4f7def9b2fffc8e063ffebe157eefd86c839d740ccbc07dadc17ab3d2b7d66d561d6634e2d466a375edd39745f92cfbb07fca503e3a8370fde821d5a7bb5df5d9f3af2627d9addbedb370c37829be785f72679e3a03a780389adefd846016b13c7dfe082d9a7358d07740f5ffdcddd82f8ae5771236ab7b7b68bdacfffdef803afd71f5e7bef97098c87e6b8266d932dfb3958dbde5bb5567765c65e651d97629ffdb1c3dfef89f4cc3b0fef7fc7e5dfb03897abace1a669eff9a302ced8cb6b7bd3e1cd974ed98f1f3ee0297b8f3e1b595ddf6a7f3407cebd2fc6b537825d1de95e672eeac6f97e35bf7e986b9bf32dcc077210dfcdfa5e285e9043968bf3ab1617cc83e8cd84cee78d60eee105ce1dd5bda1e66b037af2950c9be2a16fbc104b4dff9d5b60ec06126bb93efd1dab1b37f9df7ffff2e7d8c3b7ffad8b6f89c17ce85aedad6eafda45bfbecb57f18099377b571e82d87b3dcec5fae454e7dbea1fbb7cc21edef9febb3f2680735b6cafe5b70fbfffecb38767af2a1f5efc75dc3b3dcebae6e7de75f8d94b67ecdfbf73a43ecc9b2e9fb2878f7fe0570ecfe739c0ece7fd53a0f7b37edf17680ccc5cd10fd6732f3b9f7a6b4f3fb9cdaf4ede5cd70a6b11abee5c56fe179a17e490e57043bc586ea8378251bbb6e281c7b70ee8e08d229e4fa9c8bc79f6f0532c3f9325c63ef604740f5004c89f7ff5d77cc578c4fcfa81d1c3dc38edbd0fffee77c75f3ef896d75e1c28ad09f6016baa4bede6b61ff2d0519f1c30aef9e5c2363fc71273475a67955b7a7f05fdd91ffbfb97bead7ee69def3ffcf8376fd7b137b416acd7f0b1c34fbdf9cd87375f557eeabd0fff1ad99d7eb3e78ae77ee57fbd94fbb637bff162ecda2ef437fde4e1e7fbab131fffc0e1579ebb1b33af8339f710fc2063cc8c5dd198d62ad895f99cc03dddbd692de6e8c27b8c9df3c077d39ed6379e5111e7d6a61e73f44a31d6be2f34b77ec87251f3e14b0fa67963e68d6bee566c7304dd07eb3a56f820cca596bffa85ae0871cedb0f662cac6c703ff772898bf9acbba235573db778a8f2bdd85effd67a958bf9c5ff863b76f38ef597d6bacfb36f3ffa638273ea4e1eeaf182f3dce1c3ffbc7f91e2ee8f0ac0b53db8aed78edf99fdf8e1a77fe1d90bbff7abd7a3ced87bd3b8c64fc8511aa7add4666cdf91821f1fe89bb9808e4c9ff6b2356f7df5828d58ce8063e7c06d70eb876c0f372ed49bce057b43d09515d8c943383081c39bb9f91d271ce688bf61d018757b7bf0fbc5a1b1c614af07889dfd8ff5026ccc1f7e100f3e15d0031d698f55ed696baf99bfc2f8c6699bb5f8bf872a8ddaab7a93cbbe670f6f7ff3f68f09e0a2f783a5dc05c33052f36eec0c7e61b8745dcf7df8d25fa33dbcedfb2ffda8e0c1f5dfd55ffbbd3f72f9f780dffbcb878f8cebf0da2ad64187ced111f6b8fa8ac622d682dac177d079c1a6dfb9ef149f627bd8619bebd7d6fe523b3d7c47cc1763b0eb7b491db25c4c3fc9fa2d3f17de9b2fbd71bd39bd81e6f0901ad3d13acced071cb2ac65faf4bb26c5756347df5ab335a9c7355aafb1ea8cd401e2e1c276a1852f3db034ce511daa0b36d6c148be358e615daf65a25f7dc99d5caf1f8cc3567ba9fdd9b7bff9a11f13f8219638d7f0507b0cf77ce7f1b6c387ee7ca7c2772b5792dff9f987ff12c446df5ef3731fee5fa3e54705df7b29c758e4e2debff67b0f3f7ce95fdfb9fbd76c0bf9ee49f7eadc2bd66c2ff71f60afefa175dcc1f8c601318ec8561da086828df7c4ef30c17520d818e96f3ed45ff12cf01e589f58e70a9063ec0bc1e937ef9a70811cb01c6cbd40e8c58b369973683c378a186582cd781e821bc64fc0f8781060bef13e84555db0a7a20dc8ddc2985e47f54bfcce73874fdc538d69bfcdbc402c71e69cc3ec71accf9dc87bda69ce5ac3b36f3f5cfe107bc5df2638b1de539cb3c6ad6b3ede77fcbbb16ff8f9c34f3ef45b050fb8bb8ed71ebeef472eff1b67eff56f26046b30fa4e40c7d57511df5c690dd0a78df9acb735679cfaac07d58118dfd7622ea33a71339fb9efe1f4095f305f286eed90e5223960bd595c2cc21ce90d136f88b188fe1e5ed6690c424ceb017362395019fffccffffc521c36680dc41cc1673fe70a90a34f9c53cbb889eb7fdddffef67b16b9137f2fc7bec459b3bdc1eb00edc64fb90ae6b45779c89a1e738dc77bcf1f13bce1fe8f095c83b2843ef77cabb8cdbc2b4299872a6ddc9bfb8cdf8d7dc3eb5f77e7a3ed7377feffb81cbeef472f7f6a7edfcf1ede953f00eb73eebddeba56731aa7cec8bc7b95796b75deb17b1f1deae7834db117b27afff44347455ca779f8b431b7766b2af85ceb6df3e0cdbc61f84a31bf85e1a2b0f553a2378798de80e6acb0b639c6a17343bdd9c0618960238f83d6074f1fece4fbe304fcd6f793af75ed8740fb60b39e52a6cd1a8caf79ddb75ee8f7f98d7f7ef8f0730ffa9ae788dd7c6cadc57a1bafee7d757e8c557d6b698707da9ac61ee3e1df2678dfc58f09ec2bea0fd5cddc9855dea4751ab3b5eeafb813b3aeb4cde5df8d3d1c3ef6de371fbee55bbee50c99ff6eeec70f1ff8f0ddef6f582b6b54b40173f76cf19d6a8ea3f1b5ab176df68719eb9c9aee45e68cbe87d051bdebc036df27e38c5137ceb3853efaf1a91383ce7b3d0fffdbe2560ed9f96911bc4195c2856b43ef1cd0bd51e24d9a76d0661d7e6480005f00385011e3e6661075ecc60a73f2c038378958b33675732e36c16bfed6e1f267d9df38fc5efea6cfcc773ef5525f59d956f4dae5dc5c383b76fe36c15b3f78f8ffdcfb31816b703cca8839a7ff59751f89f1d7681f918bdf99bda7439f91d78b0dd1b6f255af4f1d1a3b31aeb57d17403be20796590b5f73d4791f3c18157084b95671cdf8d4a1b1e8d4f740be6d6efc9065d12c1ee102b971ab83a702f81be39c9b217c75b21e984f5ceb807340a78effcd20d6e641cb41edcdb69fb9f4037dd686794de8ae650ab1737d80ed7ecedff89ec30f8d9f18fcb3fff7bfbc18cdb15ef36a177b317affa6ffc16c4deb03baf71dea2bb38f6bd982ffc0e1e51f13bcedf0c17ffaa665fda3b5f0ddcbd95adb16e7f4ba6a4df9d2872fffa8e091c9efcc4aeff36a5f9eb357cdd7ae3e39e5738f30fa5e11bb8a37b67ecf0ddf3d62e6fa8d5dd5ec79d19c429e355f884fb3377ec8b2e879439cf7a678f30a7ec5b931e8d4f1104447f0636b4f6cd600f310fe6bac8cfe68c05805b433166d8cd25cd7601dedf42bfaa963adaff88ad71dbef787c729fbfe9f3dbce7f9cb5f68661f6b552fd4379ef16ecf3b1bffc27219fcd65077640dadbfea054f258e5c451e6877f9e5f16382b77df03d17bfdab4aa619deaf71931400d6d0fc5dfa1f6e9d7a76c5def1677eb3d7778f73fb97cc4beed43777f4b81eff6182bd8b4ab7ff18bbf7b78e7a53fffe24706cfdfab7ff939609b7bcd384047cc6124be1f26603e6b689dfaddbfd6627cb0a7efae65fa5c873e64da5631eac518de4b847963acb3aa896ece6d72a387ac8b469c8317ed9c119b32c1cf43318eb13663c0fc559d099b891afed8c01a72f7007a7040cf4fb2d0f594e917f5550eb16e44f8e6b7ffa3c33fbca7dfe5370effe37ffb9ec3f3a35eafb573757b3dff918f5cfc0b4ed09c15cd2dadd71a776df726e55e6cd9eefddec37b2f9fb087f72cfe9ea9f9adbb5df3013d0056683fe52f77aeee9e7606f37763177f01e118c47ce94baf79e8b70c3efed3bf70f8c89d91fbd143abd7531d8c69bcf37938d5c708f8d4614b37c7ba5d0b58c7b8faace368ae318ce6cd180ff1fac47cd0d77c3e70dd26377ac8ce6fbd7b71935e2c71ab1b21faac0bdab8e9d0fc2d9d07e141cb589f75fb10110f41fb60f30bc97ce0425d6cf88dc1d67ec5fc2f7de9bb0fffc3cf8d4fb3bff18f0e7fe7bbde7d71d05ae341fcc3f7f8c2f6895f3dbcfbbb5e75f8d61ff881c3ff3cfe8bb5776bdc9b84d6b1fe64d56fc556fe71de7af8e0bbeffe35d3b2ea876dd5c3d8e6ac6c420debcc9ac7f22e41ce622d30ff1aed33effcc98b7fd6d0be53445dfb6be63fe67d78dfe1439cb27730766b9ddaad85b82fdd9bf543f72c68678eaebf358c81e62b62eccacf3b058d5fd15ed03ac772f5379fef1a66bd9be6c60e5916ca82b9c1ced139a4fc2a233de0bc7021af37c2431b9b9f2ccda1aeff4c9a3f5b31d778d721dac9c5ee9aada51df12b9cbda4baeb04e2c41cd7dc38f4b92ed7ff37febbffe7e167e76f73dd3968ffebbff7df1fdef3d1e72f72ecd39ad8bff4a5e70f1f7dc7df3b7cd77ff503879fb9f737389f7aeac1bd379ee141e65d5c13acea3fc89d990f437eeb950777e8326ffbe03f3dbce95e5f986be87c6b0dd8dbdb1cd8cad98abf7b3f2ff75ad620e75e8df2155ff189f1d768df70f891ef7bcd450f85fd61cfdab53d58d39b0edfffd60bd37ddef7cb1fb9f0752f360fc1e7689c39c6c2b4196f1d60ec7e357efab129d6b12ea8136f1c589bb9f75e1aa31d1bba73466ba833dac31ad039ebf343d36d7163872cff066b2fda0b71ee4523bd194ad10fd460ee08ade5274a6fdaac05dc4841e7e07bd9cb5e76ff0fc2fc39b2079d741df6a38f35e6065af5ae4d9d1c451bb5eeeaaf39bcfd573f307e6c7087df78ffe1677ef0ef1cbee66bbefbf05d3ff113770edc8f1e7ef5577ff5f0918f7ce4f09e77bce3f013dffdf4e1e9a7ffcee107dffff1c38357fb99c3dfcabfbdea5ab3e44bd0bfd7035df355309e7c6b6c327e4cd09cabf66daebaf3155bf5b7ee039cb89abb3d3ffcf397ffabbacffcf0e1ce197b89d5da3a670dc6cc7f99ebe2afd9de53c535339a0bada96e8c39d2d8621ca3ef93ef2173f7afefa2ef07b4871f9af0d56e2df0830910a31dbd395357885787ad7cc1ef19701bdcc821ebe21895de44e65e207a0f46e6c6a817ed48eb91afb436f3827d3e50741ea47f1d8f4fb308ebeac650663ef3d5216bee0a6b95cef5dfbdd6ef3efcc27ffcff1efe1fffa77bce4bfcc69df39603f7070f3ff4433f74213f73677ee76c1d3c7378cbbff87f1ddefe371e7c4182bbfa85fa10f61774d725adf53097fbcc5c7838f3ad870fde3961675c993dd7fdb7d6b40d3d5b6bb5775cd725fde27f1fa6eb9cbf1bfbcc8f7cdfe19beff98c6bef523bfa45df377dfff89101ff699ad3f7c53517e28c556fee9cc36a3e6dc5f7c377c458df7ddf21d7e7bdbf7f8fef8cd3b682bacd71ae6dae73d6329e713eff9be2460e59be0a20bd29e8bd38f579e3bde879f18578a0873accdcd613e6ae459f6b40fcfd591f3ee2c3ffdce73e777fb330f2c99758e6d49bd7e41cc8571a5bb431f6de317feaa96f3efcf8bffad4e15f7fe01f8edf9f3dcd336ff91787dffccd7f7978d7f7dcfdf6d4da65eb6ebbd6bb6b78b0f6ae0fd9823873cbdd3a0ff77ddb87fe978bdf26300f01f3add5f9318c7bb8ff3d25581bda43da0bddf9a552d8efadfd01f377639fb9f85101dcaf71297e1bd774f1dfff9a3f32f827ef397ce28effd2daee8cf35a1ed4b8dcd379fdcdaddefa7d56b503ef0922ad35ff80a9b5a9e91e93f687d632b6efa2028ead81cdbcfa79f7395b186f83473e645998378f0bf6a2d47b31e808bae807ed8cab181e5eeb017dfa50a1b9a5bd80380f59746e743702f1d8f0d1835ebd46996b0274eced893e371298b7da68af7de32f1efed5a73f7df8d7fffa03879ffb87770edc6f5f1cb9776cdffe969f3bfcdcfff69b87cf7ce633877ff98bff97c36b5ef3608d8e0fd67938fcadfe81f5330ffea384ae7b628df2a52fbdf6f0fa7bfa05af7fddc5fd815e3bb977e57587bf9dbecfbcf38387f7bcf1f2bd7324f741de83de0ff4bf79f8967b1a3c73a7b77f0597985ec35ddbe152ef5e3318ef689fd6b9b0bde675876f6d9d6f7dddc580effeda2eff870cee7c25f9d18b1f15dcf70f56bd26c4bcf127df79e7b8becc7377e452ef3b506755cbb85ea379da5679b511df77c0778f18ed053be2de9ef980dd1ecd476f6ff5956d32ed736e9fda59c76d1cb44fdd69b25ee599f0b358bed5eecd2996e7c6aacf0becc3c1a69d91438edf17c46f8c5f793810f974f9f297bffce26173587a18b6571fa238a716ebf72b1935a943cf4f7dea53876ffcc66f3c7cc3377cc3430739f9ade9dae85588c1b78a95aec55846c47ba3adcceb9c31f57504e3d5c51a8cad3f31c7f859afba2336e3e194de39786f9d17633b5aa7f1fa4b63a61fbb7566adc9aa8eb9ad738c19d39a8ec0bd306ed6340efbdc8fd05c21c7fdd73d57ccc38e4e7c218ff7c418e6402cef15ef98efa87f604d1cef1f7ee2edad8edf9ad441049b35fc30644feb8031136c0ad087f5f1e73537c9237d92e582b938f5098bef8da810df9b82ad74de180f437521c687606d6e1a766b6103e7c4a1fb6300604e7de61cdefcc8800317bbf1e67b5da04f5c33310a98df58fdae55b106b561e63556aadbd3fafad4b54b73c1fb38e39ba38edffcd651effda95feca1af75cb2ab739338f91fb88e89bcce70aad696e63acb555b3b1cd510763661c6877af23ede5ded057d1dee7672e23b9c6b66675eb88f788d1c3d5fc8aa01bcf7b8aeefbc8bbc5bcd7a64cbc4eea08b5b5837dade7a16cbde6166a20cd47b6e2afcb83955e03fe8baebd31ea8c8aa07b31501da6cff98ce3a67013b07333156d30d7e18d748eaef461598b1bcdc6e0f0259eebe42b6d6f3e76b14e6d2bdd9178a96ffa6b137c3367c69473e2b5b5af3ad7ecbc79e6acd0de7a65d65ad11a5326d8885726adb542fbf4b72682df186b3a42f553ac624ff5afc85c13ba7ef5c6abbbefeb03eb5973d643facee0638ff88e09767dbe37c4f07e81b53c189bafcfb9fd196b53ef7b89cdb97ec715d60674dffd9be4da872c17c2a2bc5804dd07e0851a07c6aa6b2ff3c698637da026422ffb4d5abbb9d605ebaa035f65fd8a06e4f9ed8c5f91c1f8d95f7ffba3cf3cc0c67ce6ace28fc580f7b9f54b63451b39ae63f683a91b67cec41a505d563993d9b330efbe9aacecab1a0a782dcaeada9c4f3bac6cd09c99df1cf5f6ad1fb866698cf679df5bb3a378e0590359d5a88db902f6765eaca98f7e730d80df3a3d6c674de6ca16beb7f35accd136d7c1dcdaaefba6b8f621cbc1e38528dc20173717adae0f29fa8d7104e3cd61e4c0e340f4db7cf0aba271e8de300f64e71372ac451cf5f9044b2c3f77f693aceba297736b52a39b04f02bce1d5daf736a6073add810eab526e01736af7dcd51c07c6b743ee3aa0b7aafcb1c6319593b6cc5ac680f98f1ea732d0a346795efdc67058ed6691c58bbd7ec08bdf7a2df5aadc9d87c59d9a0b9c4b82f26d6653de69827ccbd767d5e9776f45907f0f7be09b1826e8e718cac57b1b67d8c017bd43feb0931c615e3b0fb85039a4f1eb4b67eeb92cb5ae677ad8fcab50ed97eaa9b0f017d7513bc39e8337edac039393e2cf1a620402cfee6880710e0370f9db5138b6073e4b0250ff153ac07ee5c0b75ec0b9dbb0ee78ee453ab327344bbb85eec48afadcc38fdb53987da60fa8fad111a772ef69c759d97956d726c5d8ced05d59b6b5ce3b774714e9dd6525fc5578acfb47560c6cf3e08be2dbbf88ceaab7fe68331c653a3be824fd1e73b678e7594598bb9f7415ad77c5067b4e7caaf8023d46e6ccf9347e5f2559c8907ac0be2c2a473fc8ae057a0be8971c698e3e843b09f37589d3c62b67a686754e78045f70fc338643ff9c94f5efc6a14379e6be7d02d5d57d7e0d8fed3d71ce2b4176dfa3b6fee8a55efeb608fd9a76b51b0b12ed9ea7b95f5ccbea7d8ea4f9dd521b65acbb419af7d6b94d59a8f5d87cf1269af29b5abaf38d6ab395bf9ed0133ceb90728ebe7fd403ca410d6a1b48639c5f815d600f2665c6b8b3deb33aff55c8b73afe526b8f221eb21e3cdf1139fb8f07e1562eea7370f3d453f326f04b10ae0a74e6b7963d0598731fabc51f60262cc2146614eaed764cdcf7ef6b387dffccdbbbf836a2ef1425c2106218611ecdfdeae973982de5acc2bed09cd63dd331e1c0b39609c75942dbc4f602ceb35a77db936b0fec438695db05665de1b21c718f3674fe319e9553fba73e3986f3d0be3cd81e61be37c828fdad63766d650379e511fd7a06e1ecc9ac61a630d6cec1930a771c4d8d3f74d7d0b7c1eb08a7f7ddd77cafaae1f5c8f34a676e35d176873ee08e8f81be3b5823eedadc119c7facd7d141e743c137e3ee9a2588037a20bee621117aaeedc119a07ead684e61baf4db4b926e8832afa89f7263322e430e7df9f7df5ab5f7d31ffc33ffcc38b4fb6d0fa1db76c339edafaf4632b5d9722e68176e68d75de5847685c456a3316993ea4b6d66c7c99759cc32ab6fe59b3f1dc47b136345ff457da8bb9ccfce64c8ead0f56b95bf3ae47669c7bab7670ce1e9a58d7bccea1fb0edd2f9acd0147efbb73e2f84ed03a622ec22106f651ac854ebef1a08e4f5b99cf1fac2bab7c75ef2530dec4a7d92b1db2fd36d985bb58a417085e18376a75b3c51a136ce630f62b4b471f84b1bd51607d638c9339076c6c2cbe12b359386cffe00ffee0e22f28789df631df9e7304625c67c13ee3673de7800dc1563b4cdbca5f4ef52df6adaf73476a689ffdae02b90a581fb674e8fc58ff9907c46357ae42e367fe6a1d8db1ef44db1ce7fed33eeba0cfdef5c38c61ae00be1eb0ec613177e6b83eff6c63ae015c3b237e65c5f4a1dba3beaee118c637b7588703963ef6ba2e671fb236957e15f210f561b83085bc5e502fac37861af3a2cd43a8ef4343c863a4072368eb1c5cdf8cc3868f35f6fad820c420e87ff92fffe58b03f6dffc9b7f73ff0fc1f4235e6b6ddc23bf30d08b917e8cc4823e75841cd604d605e21463d1f16bf3b9e887e60873f1be371eddbc2952dd5c46849a8cde93c64a6b9a0fcccdd33e63c05c997ef357f6152bfb568d155d8b686bbeeb46b0fb7ca1f78bbd22dac41cc68a7d88f51994cefbdcb5bbefdcbb48eb8bf5b5fbed35ba7f9ee17a19d5ed656c7b1b3763db77c67714fde635a7b540bb23d7d4fbe641fb289c7dc8fa7358a53763e242815816ec28d5815ad3c61cb11e31adcd0df061adf261ae91b9b10a355a9fd19a1ec2d6fe0fffe13f1cfef44ffff4fe818c7d5e1b6073adf6a09ed8079fb9da1889c7ee1c8c6b8e31ce8d85d6d7df58506f0c1007d3be42bf39d01c6b28c5f9bcc6596be66d5d07184f4cebc8ca26d671b44673da6bcb2ead03ee15a5306f3d62c5bd21e8e6afea6833475be7d66f9f89f17d3e5e43f38df3706524873860448c03f288e9871f46a5d8ab71d894e619e39a9dcffec0dc3536d7f552db73e6ba3c78238f603317d38bda82d85e802378e06ce5d76e9c9b0ca116f3de60eb1b03ed09da8dc1bfca73d4e746e04707e89ff8c4272efeaaad18b73502eb046b7b3f117de0bac4f8529bb1d856b1b255d335ac206ecb07f8b6fa5a579f73459bd4be85b5dacf3c6ce6afeaf41e9bb385f5b638e65bd15ef69efd3b9fd7e29c51615ee97e5288b30698078ed07a826efe845ebcc38cde5763794f3860fd14de9e30e38177aaef373accdc89f173dd8ef8d4c15865c5caceb5f6bbdcab72d621db4f8cc00dec62bd98de1c6f9a5fa57c20c623cd3d07eb53935afc211435c49a5d2bf30a10e7ba9c2be603dff65087399fe4d93cfc6c96df34e013ad3fa36e1dfb120bd463dd6e3afc08767be2776db523cc8d37469ad3ba652b8f587b83fd5ac39862ae318cea525f63db6f9507cd15d7306d5ddbb15ae27ce602befacb561d40b79e79f563eb35cfd8c9ec459c36efa5b9e8da18d5673c748f01f3ae015d1ba3ef58eb6247e73cb00f3a36f2fc146b2f6ab48ffd5b535b4775f22ada8abeae11ac01cd311e5631e633c7ef5a39031a7f15ee563c0285f98d0270215da823b01820c70519ab0d59e5aed03f6b71237880d8fb8771d62fc7e6d66addfabdc1c0a758ee039b8a7f38e6777ee7772efef118fcb3267850037e84d8f6b4bef98ce6339ad339426de6c682736b81f14afd5eab7347b0aef1f581fe15c6b68682cf7b80781fa07d18b5436baeec50bd35bd7fa5b130eb4cbf58b76b00e31de71a95ae0bbda25d56b1c03d73ac80fb0921cf6b2777d629f6259ebd65bef1cdf3038771808f03960f255d1f351421d63ed6acbf7d6b076d53c0beceede31cd46b3b45d7e86f165d9593872c37d405834d85e6da388010c1e7263086f1dc8b6d6cc16e5d204699736ba8b7a673d787dd9ac09cebd6c7efcb7ab0f22303fe92023e685c6b4dbf62edfaca2a1f1bb285b1c635d69e8cc639c2ca06ce67ad1907d69763b55aa3b50bf6956fd685d69395adf5ac6f8c3eed5bac7cb5b59ed27530f7d9d7c7c85c9a833efdd0187c8afba5f663ac625a1b9873d0f89e23f4f113abb25a13e823c7eb07e3895bf5548438af0f3b75a131e8f6656c6df5c68b39fa3af28501b92a470f592ec443d3e6132ed08b20be0bf7e2197b83618e1362bd19ab0bb727ebeb1702edeaadcf1c9a8f30475ca79b861beab74dfc61171b8cdf30f8e33ffee3c3bffb77ffeee2d36c3f49fb00ecd9f55067ae11eced3af023c61babdf3ce7c29c3c6cda3b5a03ec8180b9d6d4678e75d419015d5fb18671d60173563645a6ed58bcf5a6e06794faa46b3dc5eca75e9cafd607dae82bf533baa6e68b7557b5d983eadae71ccc9f73f63d680775d6e41eb78f9f60fba30246b02662ae3fe2d36efc1ccd07e2bd57c628f85cdf8ac65b0f1d5675e7283e0fcfc3ab70f490e5867063ba1051f70299f32d35870378f10a0bc6877811f3428c55b70731c6a99befc3f3a1b78637a6b53a2a82cefadc380836c6affffaafbff8c7bbd1f971017f41814fb5edb755d3f51a571a5b78985c93f5146b81f57a7de85c37f3fa8bb5c0675bf45bb7f9e848efedf443afd95efacc710e2b1bd4d63ada3a7addce8b76467520ae3e98b945dfaa8ea057b409fa6a0d8d9d7e7da08fd1fbdc77a2f13e5f6d3e37467d420de93eb32efb910f15f810de05fe8c82f7c55cfb827dc477145abfb8be326b82fd009b02b346fdb5b7bf766c33d6eba7a7efe55578b0d2411703cc6d2653ef85eb73b13316a97dd5af620e74e450f41fd5066e706bf6216bf7012be6a2e3a3a6b1c066e20bc8d77ccdd71c5ef9ca57deb3defd392dcc7cd0269d13c783723320f663d426d8f4436366acbad75dbf79c0d8baeaabd8ea059bf62d5dec03abd8954c56b6b9ee82bdcf7fab2eb4c656bde64fffb44fbf356beffb22330fa68d39f5cc773f33aab757f7024cbf23e043dc9bf6d0c6218b9d391f3638607d5f1068ddea7d16a571f603edb3ae6b5137461dc16f2df31cc1386df6f0ba9d436b73bdfcf5faabf0f053be473fc5d2a00beee2407d75135d1c3e6b31efc2853960a39f3ee39c538707ab8e9d876f0fbeda6807f3b521d437bee0231e3f3794839439e35ffc8b7ff1f0755ff77587d7bdee7587bff497fed2e1abbffaab2f0e60f310ee19f1ae4f1bd89f911841ef3af43122d69ef9e88c5e0ba023d630b758577d620feab4873eebcdbac632ba466b80beeace5d33a3bdbca6e654a03ee05e6363ae8f3aaec7b918d79c996f1ed46e2c18c3bcbd26d81b2bbd6e6be8d72ed815b09ed7555f6df6b676e318b977ee557a622356ddbd8dce870df7be10e75aa0eb2207bf7a71eefaa43a38379e11c15e5da117e2dcf5f51a7d4fc1ebc3860f016c8e9c33f8a97b2ecbffc61726fe90c70575f1a0de46cc8d639c653b6791c682f50a7e6f12589779bf0070d11caaf89f7efae9fb37c61beacdf2c681757ab3c5ebc5cf2764859ed4f0df6ee0c7051cb47cbb84df3588eb559fd89b18d70a1dadd1756b330e6aab1d7a8dca8c695d041aa3debec69d0b79ad09b3d6ac8fce7ad591c6f28c90c29ce734311fffac05d5f523a23efb99a79f79f3b6e6ed27d8ea37ef543cf7a8f7c2d8e67784553df2dd239d138bce2738de03f63e1f3af06dd5aceebb8aa09333c5bece851cd18ead71f4f2bd056ba913b75a9bf9d30fabbec63972de9cc383d32578236c44416f10e09b17428c0beda8ee42419b34aecc5c6f9807a673460e5b04dacb75b73e07a23f6298bdc99d829f1bfad7fffa5f3f7cdbb77ddb85fccdbff9372f3ed9fa159db815d8595fb1dfdc2ca27f8ec499531fa24dd107e418738cfa5775ce652bb7b6d55a7a0d52bd983fd7bcaa5ba67fd6770ddc3331c6faade11c9f7e314ff4d76ede8cb54eeb6d615ddf076d60bfd69fbd60da9877dffa1ebdea55afba38607907a9ad0039eac01c71ff21e29cf8f6d16eecaac7a43dabb7066ccda179509f3ad7e13d46e70bce393c74c852909bd94d6661460e287f5f94586fb6370a1b7317ed829c83bab1d6f762a076e3a9632dfb9a637f210e1f621ee2275363a07d8de7fa801f19f0875e7c6ae5a0752dc5431f5cef5c97d7d27592479c5fbca43d88673d7d268cf698fd6a43a03108f9ea0860b37ef39b0bcd7104f4998fcdb139a7eae117e3d5a5f6e2b5c18c9fd7ad40d789748d3c0375a8de3534075d5fe3bd47e2be50ecd53aeac642e327dae9e5de6addd6292b1f35b05187c39577807d2bf891aec5bebc439e132b218711aaafc0afc02a8f51017cac85396b737dad85cff536cffb80d406d6e3439d39c778e8099144c33668737f4e09d8bd08c49b6f1ef89061d69bd48f38a70730f74281793f45fa5027e423fa8947e8e18f1b3878f9e2817cfad39fbe107e7b805fd9e2d7b75cc30abea2d9d7fbc13a11f35c83428cbe5e93d7edfab87ff83dc89b674ce782bda318630f41a7feaac68afad47b1d30eb09f18aa8cf78e68d8339176257fdb6683c359beb3382f69b36c6ead4a84d3ad7bf25601d9f2d6cad09e6b520daac654ef78b39eaf65007e6be37ee7330078861affabe5ba7358cc7a6defc69b3863a18a7ae1d9bfb0d31cf3de91cd45babb9c57973c0587eac7a8a870e592fcc226d6203a8dd0b85be68c6f44620d6d20fad3799718837a5a3b93c687b007ed680e073a3306af380e55fd8f22f1d78d07210bb7926f4d0471f704deae05ae6b8057e6aace2ac09f82bb501d7a7ee9a56ac7a6df597537ec04f5c7b3b6fae71ea65d5632b16bce6f60463a77d8badfcf6b4cf6a3de8cda92eac156aa316736bb63eb48ed76a3cd2fdc1bc7e708e38ef3ab67c8cbc1f1eb8605cf3d191d670646df39d70cd737d5d3b50139b523a6f7ee7d629ad39a96dcbcfbab7ce0679f034eec022b88124fac949614e516f9e4d19116cf8c08b21c73a2c04315e88adb49e8b9f37628e7cba469837c7b5827663f1b1363e09334767e4533a7f98c5afa738f25b04f85778cd8c1cc65cab6b777dccbd8fdae61cd0e7dcbed4a72639ceed0df440b4a9333aef387d4ae7624dd757df964e0eebd5e6d87cae05ddeb24c75e82df7bd01a8ede0fef656dd606eb62ab6cd9a436eab68f7663945ebb71aec31a32eb38d68e3ee7c05c1b3dfbed2b3dd0bda7087372bd06d0d6fae09cd16b413864f9f0d1fd6ded0a7ece1274fcd09a3c53466d535ca3f74a9dd1b9b9cec139b15eb7b1625e7315ededc575703dd25846eefb311e3ced3b580cf1c6d0ac078ccd5d20d8145c6445bba05ba3766dd6a31738361e3feb427a4358b773c478467c7d61156b91c79c9103979143171dec51dc4cf6726331ef4356b727e2dcbae8d0eb3196b9759a03c620400c3837de39345fbd31dacc556f0d689dfab9865e87e867649df654d7578e5db3cc39581b5c4ba5cc39cc7ab38739b533b647e3a1b172aa37d7dffb33b1d7bcdfce7d16d49c8705b826e6f6edb321473bf0d7c9fd8b38824e8e6748f3f58b76a5cfa6e85717ed0839f452d70fb366fde8f6351f660e781618c3589d1f1752638bfb872cc17e2524a137d6054917c5d838b006e35c7473015f6b40f3107b3b2fda3c08a98fccf502d7d45874fb6ae3b0b526421d3fedaef03e211ee0851aa5d7065da73e716da04f1b3ab98ea0cf7bd07ad5a1fae49c38ecc76a4c8c758d806dce67cdc63456b029b2b249eb5737b679cdaf6e9e6373664d049b7b505a4f5aaf6cd5ee1cdc0fd36e2c636b5707e6d628ec71841fa7f16717fc0b74fcd572f71990cb01eb214b9dd6b7bf309fbdb401b1fa18116cda5b4b8c07d6d5bc893ec0ef75d4669e6bda82b3817bb3c5fd6c0f20c49be709ed8229e6226ae3706141d40016858d18176a8cf510b1afba39e0c1454d2fb63a90c3dcbaaca37dea07747f0dc5fac4789832e2e74705fcca0af1d856d88b3523d6f35ad5bb5eafaf6b02afdd5c466c629dde4be3b11beb887d828fd8950f663fa96e8c6b675e2176e6d6663e546f8e60ebb559c75e8ae89b6867ec7351e4d85cddfb3eaf7f853df55babf1da64d6b386b4863ef5c6a273ef80c3cfbdd37d03cd230ebf3672b0fdfaaffffae1b77eebb72e7e65917782df99e56061fff3e18cb3a23faeb007feadfd86cd38f07e32d737d1d73da15ebb710874de9e5ea73abef6d507e430a707f7d15e8ae7dfe4e20950d49f2b10cc4163019bda183b7ec4051347530e222f04b08bb6626c7380bce9d34f5ffcf60474d6457f0fa0c6b0466cd57b83d0c11ec4696344e83bb196fdc01b6d6dfdd60674e2185d073862573f853da43d4be31cdb6395577f699c7ae733cf39a37d60e696daabcf1ace11e07ee2ab0dd09bb3c5caa76dd5139b76c72db6fcab5aa20dffa476f6a2a2adb9b5a1af6a3277dfe307f6e91ffdd11f5dfc4ef86b5ffbda8bf7803fa3600ef66c3de6d0f7a6fb1cd4a7ddf523f5b99ef6d1567caf5a13bc2e6b37575bd7cddc1c633bd7469c6cfd75db8b2cbe0a35695e1c0b47bc015d446fb0624ee7e61a2bd5017f69bcbed6f1c281b936f5f655c498426eaf0f21c703bd785fc0daf44326f5c3ec2b5d13a379d3e6dcebeb758279ce65ce679d536ce59fcbb9f1e7ae0531d67999f3c9ec63fcaa0eb1ab786d5d4775c7e632d7571adf718bf699fb0e1f76c47d2c5d0bccb979ecef3ff9933f397cd3377dd3e16bbff66b2f7c7c878740cf05a1d7ea7d993d401bbdd03b228579afa3b9c05cdbac31d7585d8c7584d65be508397cc8f377f0cbc509c5c229d0457193f82ad41bc6b70d1ec8d88c01735d0c3ab13c04e2a69f8bc6e75caa8beb33af370c5f3f65d28b75faad8b02e6d3171bb1d6716e2f602311cf3f0cb3c20d462e3988f9fafaed1771f8451b7e4047c0ba60dc8cc58e0075f1e9d7cee8732cd672341eb081fe624f65e59f36b1d7317f7dd49aacfcd66c3c36a4983bd7c8dcdc3e7f629a03cda3bef1fa8198799d33bff1b06577ced81ac67b9d8d634d3e6ff3b46f614df6adef257068b07ff80f89fa2333de0b757a9b03e6b93ef05db007318cd85cab795db7f15340bdb5c05ac6cd3ac07b0eae8998de1be680df359a6fac31d604e35c8b5c54c641400f9916bd08bc6347c09fc158cc3805ac07dc641e9435995707f3a4b5ba68ebea57b451d3b53277d42ef4f5e123c620e81edcfdaa3d219f43d41e403ef5aca5cf6bb6976b613406690c746d605de7a575275bf13073b433aac3aac6aa5f73561cf3eb9bd7a89db1f6de3f6044b4cdf82dec87f8fc44bb58937145d703e6ce712b7f056b4280bc295d8f71bd07f39a049b3ee2c55aec5b7e3cc09f51e8f75dc307c652c375b4b779ae0becd71ced0a5843017cae79d604e3005b73c11cedcc5b47f45bc3eb60ae407ddc93f969f6c2c321e2816861e9dc910571b8cc9b6c2cc2027af091d371ca64d692da059d8b4456f1cc8d77c4e6c1571bbac25af941bffe62fe7c38cd378f58e28c650e8d91d59c7837a4d7589c4f3b982fd5b73856cf7c4664c69c537f45eb4a6b5fa5ee5c1373f24fd530e69c6b32c69c633130fdce57792b1ab7aaab8d11c18e38afad4c3f98077ca0e20fba3c1f183d50a0b1622ded73d4a788fd1b8f68679cbae74969cc8a951ddb563cb816d736a99d5f6f2bf7ef169fdcfc9d50849be9a7396eaa3757014fed79d0e0a7a937c0c57be8820bf6a09b376be6817140ae7d009f35c103503bcc797345ddd8ad4fb1f8f842b382de408ceb05fa6163b437d8738539d45107aedd5a8cc6b86e05ecc5a83e69bc236827aff75bb8d6d69da3f9add9f8ae77d2dcd62bfa8bf379efbb8f60aec17d0dc63076bd9369271eb1266ce5eb87c658a3c2da198558e7f4f23d72efcd5accb93e8478a5d74c8ceb46b7169f6401bbe7007ef63f36eb98cf5821a668077cd600ecead87b3d8dc5ee073d6d8c65cec5b5ba06466cd36e3fedf6016d8cc05afce501be28f5d3ec833b7c0792dc888c2d6243ec1cbefc4cc6c614e4d7393c74c821c67a8cdc0c16880ede24f2bd5160cd0aa8ebb78ef51175c6423c6b5226d6c6675dd6cfbf1d3b6b096bf6e7bed0874c1de7887ad7681f738c15f5e62a8d6d0e58d7d862ecca27da5b171bf35e9370adb5d507f66addc67bcff5833e6cfacd695c59d9c905ebb5aef18cc4192bd8b7fab5ce1c9559cf9c63acea2aec3746628c53471aab7f0be38077b07bacf70c3bb8d701fb9cdb13617f9bab1803f6d65601ecf3de816b6164cdc64ff04f1f35cb5c5705663c73e27d06ae0fdd5a0876a53f4e7df86aeec00183f440e8029c53cc1816c0c533823e9bda10acc588dd1c6a5bbf7345d0c933ce1ecc19c1da620dfc8ce68a79f6e1faf964bf4537a7fded01d45787f6536fecc4fc99376dbddeda455bfdd4b077f5e6a3af62a4f558437367ec64f66b2eb4766bcd3c617eac67e367eed65ab09faa2bc4196b7e59d5a86de59f759cd76e4ffb2a30eda20d56a37988ef2574bfcf1ab30ed45e9a3ffd33df9178cf09441b584f59d56c2d51b78eac6aa17bdeb446ef8767007ee67c07cc410bcb43563c68fc31425f2646e61ca4c67003f87904c243f1b046f8e48b9f85998f900f7c65c4efc3042fce397841621dd7c3d88b36c61a7e0506e210738038d6c4bf5be0da567003fd24cbba813a623dd1477d7d8da7177305e6bc6bb586d735fb190be688f9da3a2abde7e0bd95c6314a75e91aa9611d6b28e27aad6d7e9ffb2a478ef5a056d7533ba3bdc13868fdc29a664dc7558e6bdbaa770c73cc77dd5d7bfd8dd3ce7ae75ca8830d88615f1bcb7eef9e07fdce81fcdaf5ade2a43d1dd5e7bd45ba4e70ae0071e8f8cc61ec3e6e3c7e44ccf7fe707e790602b1bd3fd85b1b3ff1f0e00e6f4012070042230f1e8b0b711ea634e04708fe0b56f83884fd03367319bd30740f58457a31e67831e01abd487b9007c63707cc21d6fad858ab37730baecb355a77d69ab80684587b349e9aab39429ea3b9d603f35a439fb952fbea5a5b1fc8b537304e1f68d73673c058c789f69907b3e6aa863142ace2dc718ab511c1c63df57917e3b95fe66893590b99ac6c624efb1b4f6df62cccf5e133ce1a48d70ada013b7ede637e168b6e5d62d0ade12102e6833580d1fadada1bf0cdf5807d19d5899bbd6a63549ae3689c7985f56943f75ab9bfda395338a7106210efbf7d89b516f387dfae0d28a470732d8000738b13839d85f4ab1f79e682b98ee00df5021bdbaf90da88b32f23f5d16155439fe053c03adeb815d4f15b81ae451daab787c2daeaeb5aade51c81ceb549731059c58af6c617fcc77c8eb3fe5c4339550fcec9b5af795b398598ad3ee8ab1adacc5dc56cd1dc49d701c7ea7a9d8d41d73e6b4df49b3fe3b15b8ff787efe210fe00a7f5799fc10f2fdaddcbb33ea3b6093edfc5e629ae49f13d176c8d65ecbb6d8eccf71eacd75ad0d8daade9f5af98f1e80f773e02093c04becaf110f8c487cd03c2c5e1e7532b230f867ff8da7f8792c3cb5c7258b037a30bf4425ca871d4f062f5a123e8d83c20594f7d53c0915874fa90af7d053d10379db9d5bd17e853841a8e083ec7c6cd39cc5c30c635cc1c309e7b228d5bd52dde4bc6c68abad70faddf75d55e8c696c7b35af31055b9f0ba8cf11d47b2de23543734af3660c3e055f6b21739dd23c738b369f45c5e7bbda0bfa8cc3d70f15bc9f80bdef31f1bcffc6e2f7393362b75fd7c25c1ba368d7a7f8aeab9bc31ab0fb410d3fd013f15a1891aec51ad0f7dbdc794dccfdc0462d7adb5fffac2bf87b9fae74c88a0ba1100f61f5630005bbbf6bcaa73ffe7e2f8bd54e1d60f1cdf7e26aa706e28d3717dd386cae8d1e33ae3d98037ee6d4f0abf731ecc53afc440bda195d836b037c8aa01363cff69eb1c0dc7a5e9736e3d1591bb45e7b980bedd178d16f5ee7cd056cb346634ee98ebd1e6dd6765eb6face1a30ed0ad7660d6db0eaa9dff8550c60b7ae31c639fa3ca531d0b9f7a5f5d42bf6d4cfbb80941907be33c0c881cb3e067abbb77c07c9a306a05b07f433b62690637c058c4788b367ef13b1f8ba7e4662b079adad5bccd1676ded404fbe0b477ac01203ea88f7175b7d17fac5ff5e130a7998312236b139f336e4023898b800ec7c9564640e5c482f1ed0ada718635d40b79737595a0facc1a8ee4d624dc7f08193c7cd875ea3a3e073bdaec3de5be0274f5db441edad85eef537469dd1f8ead078696c31b7f980fd581d9971d589656e7d73ab97dad067edc9aac6ec7f55668ef3d53ab46df9c8458ee5823d1ce761d11ae8c66153b4b1b73c50013bef82ef786b31faae9aef08fa4bfd429c02e698cffbc5fbc6dc7cf5d977f69b10e77b6a7ded137a22f4776de6a90bbae239581ee990151e00dffe237ce5f38130f290d0bd206c2c9e1f21f0a996b90f715e10e21c987393ac4f4dfdbd79603e318e7e35d2a6908bc0a90316e881b86ed7610debaae3731462ad53bb73f3c1bac6f9109b07cd258771f6553756b15ffbae30dfdaa06dfa1a03de77e37c8ee0884f3f9803fa660c4c5beb2a8dd1c67df25ee1a35ffdeaa09fd13cf632188fd863857eead817a68edf6bd74e9e737be8036be033d6f5223dac3c441ac3f3400aef8307adfb8e1cf3c0faa00d88614d600f6cce81b9f71088f19323763e9061f3bda49e35bd9e425da47dd0156d08b9de33c5fef33fb3e3889f7be43a10ef0d625cb9bbda1bc287e4cf71bc19d87c405e347316ccc530e78026079b87217672196b4747a8cf088eae81fad4437733a17b53d4c9c3af8db853b0066b023a504f29c631eaa3aff3d9d3b93113fbe1672dc5797dd4a8007e608eeefc5ccec99931f606ec5d3bf3aeb1736bd4bfa27673189bdffb3fa5b5993b2a608cb6c6af6c13fdaca37e73c53ea09df97cde809d98ee49b14ef702b1f467bf6b771fce7c62783788a33e23f9dabd9fe65953219611bfb18ceae09a1a0bae13bc367dd6d34f2c36fa8bb56a13f23cb8ed4d0fbfcbee1981d80f7bd7e0a8bee2c195de102e840b607451888b350e9d8393af1cd007e78583a3b9f5d5a60ee8d451987bd315e3ccf50ff24e61bc6be0a1a023f40263ac672c6ce9d0feed215e8b3486b1fd1beb86773eebde045d57d7543abfca1a886dcdf602e7736c4ec7c9ac47dc56ec55ec5bb1d01e7394ae8b67e8f39555debc16e8fd4047cc71ae5e7c2711e2db47bd7b0b5db41b67ed550de38c31d7830efbdcfbd05ad298be0fc65a0bb0d18b7308694c47e2156218fd6038d734b9fcc46e106e0e9f6859887f30d6435481fe050662c861f406307a9099037e7583dac9d1a7df1aaea1b588270efb29c8438c258fbf524c0de7e0ba45dd38d8b255a8e7fac0ba0ae0f3fea023bdbf8ccd07e780de5a2bf49f43d7cb687d6b4c5d6aebf394d62c5b76983e6aabf7be48e7e8cea72ed473ddada76e3fa598578c99f1e85bf744bb6bb1a7f47940e314631a27ecf5a79f7efae23d06fbb1e7c843d08bb5a85dbd6847803ac4500bd1b73a3710f3ecc1a80eaed3396373e8c12757be9bf6df1ac06f6dfbd01bf13b63bf533f97879fda0de30d6261ab1b85df4f90fd598879e60037c64fbde8c083116cccaddb3e402dfa10c3e8c39c71c7f0e1003510eab26670a43fb18ab11560a46fe71dcd0546e6e2dcebe91c189d370fac29337762fccc03f2ac8fdf5a2bbd31ce27b5b73638d7b6d22b2bb4cf98f655c7cf3d519c9bdbebd0ce5c1d8c752eedb78539ed098ed6701d4a6399932fb5aff6de84180e1546f63af9be37f651e8a3580fddf7027837f0193fa1367e63c47ad8fa0eaeea38678d8ee6bb1ece127f3b88eb9a90e33503799e2157e1d60f5960a13c24fe508cb1df967b187a33b9102e1ec1461c7ee2ea67f4e672c31174ed8a0f14aaeb279e1bb7bac92bacef0340a7a66b42ce81bcd239f5aceb7c0be2666ef35b437d0bfdad57b6f267ded67c05beadbc15ad651c637388d9ead91ecdef782eed31fb39dfb25fb517b86664f64656358d3fc55c67619fe3f7f767add7bdee7bd1b53837de519c531f9873f8b586e7436d1eb0e85d83f51ccd13df4f7a20e8b33e3a36ce0374d7e077e5d7e1a93b452e5ff90b0037c983d40392af285c94170f5c943f3a40fceac3e8c593830036c41be64db336f1f5518b78fefaa0df0a9c821cfebbf3fe47e4580b23b9acd53e08d7e13a5d1bd85f7d4563d1bdc6c67b9fbc87f606d7406e73ac376da57ee3cfc13c73bd7ed685e877dda2cd6b44c736af9951bd6b6a3d7bd48fae1d5a07f457b47b3fcd69de049f6b416f2dd17e8c99db11f1be30dad3befdb0808f7da14e0cf9cd41b71ebfb7bef56d30fb9cef32f9b19e9ffebc37400dd7d03e0871d6658ecee89e056aba8fcdf1fa187b0fb021dad49dd39f7cdf47fcd4e59d6d7ec54315cca146aff1babc28872c70233c307bd8f6417903b8686e0217cc9c1cc438fcde0ce6dec8821fbb8729ba9b85bf80e00339053fbbe19035d7b5d097aff4d4697f6aba46f1ba56f838567e6b18c38870dfc46b6344ba0ef360abbf18bb8ac3d65a82add7c9bcb1b3d6ca3f63b746305698bb6f8a7dd461de0f20176a6f2ea3d239318d83b90efd8ddfa2fef9ccb7a01f31abefc8f4798041ebb92e3e6cacf281780e580e5af6bef9d6f3baec81783f81bdc8b5201e68c49bc31900d8c935beb51861d6b68e421eef217d88e5fd64ddd624067c3f5c8f509b77b9b647e166aa5c032e808fe01c708ccc7bb18cde542e9a07cb83f06613eb4de326f2a010fc1d1163b8b93e200498e3b7ef29ccb356d7a84f1d318ed187ebfcaa58afd4e65aa47d9a873eebcc796bcdba2b669f39c2aac7ca262b7fe7c6629b7165d68499a3ded8827d5507cc6dcd551dfdc66c3173bb8f9415d34e0eb9ea8ebc138c8af663ccbec45b9b778739758d99be7ea0e25d46f8c0e27b4d0ce2bb387bf5fd642ee68179d6600df644f71a1462384380186c9e49edf7a8bc689f6427dc046fbe07a237df1b8678533864fd4a451c39e8c433120bc4a363f71057cce13f6fcc780efc050aa47dfc9349e02b203ec4af94880fcd79614ebc10eb86b596f9e8d640f75e59b3f7a73dc5587cea8c0834568c1175f38bf3fa1a837d9507fabc06206e5507ddf8ce791ec6e96bcc045b6bb95714fd15f3a4b1ce055bd7b405fed6a94ebe31ada38f67396310d781cfbdcebe0263d4a9c127d9defb09efd97ffc8ffff162bf1b476dfa904f1de61db193a7df358179ec55af95b9ef0d50479b6b07ec409ce700e261493d62384fd0f9644b8df6f2c716087ec6dbe0b13964859bc48de186a273a37a73c59baff406a1ebf7863afa00d928dc700e45a4f9c7e8cf637dd0ae15f82a482f6f2b75db579974bd8ccc8d73ed887501bd872c3dac818e9459af7ae7e07c45f319cd91d66a9d555c6d33966b81c6d5e63d7234668ec6988b1d5bc1a6b496341edd5ad29a3377ce57d8735e4b759f2da28e1f610f6a37a7425d60249618f78b7b939fc9ce3d53c8fdec673f7bf8d33ffdd38b3ca4eb65648ede756837161c89030e3c73405d01f28135720df6f71a7d2715219e6b735dd6269e77df9f17df169777ca630017cc57540e2b1ff8eac17b633964103719e20dc6063e08e883a12ee2433c07721b8f4e0d1f9c7ec49e8d2ffaa131b50b36c579d7b2ca01edc631aed6d3fc63fee9d3defc95beca6f1c744edcf4af721957b1c5982d66eeb15ad63916b3e258ffd2b8eaf4eb3e2eb5354ebdf342fdda4fad113fc25e37cf1c6be1eb3b656d50f77df1fdeb01abcd3e88748e9febe21df70c40b0e907e2fdae0ed1cf8f055efdea57dffa010b8fdd27d90937854f8e7c5244b8a9de60608ececde252f0390a7e6eb09f58a9c3dc3f4de5219c0375f92a4e4f6b60638ea06373d310433fbf45718dc681ebc436c1e6f588f15c93f7008ca5ae9b9411da031d21573ba33dbca689b9ea8d87fa27ad5fbd58d37a8e60ddd6375e9bf32d1a7b0cee0bcf125cabf784fbd9fb83bffddd67fa27d3deb975eca930f739b7177df4319aa7cdb5b007b19b871f7be71e6cfd57f1b6e03b397f5c26d6e190b3163663187b0dbc07c400233d7b1dcc11eb82357836f4e1bb5d7c7d779913635fdf6ddf477a31b7e60bc5637fc80a9b856f553cd0a037cb1b0c8e5c9a0f9c1bdd9f97fa20cffdad02208f9f49f190d185f5d0d383970789741d3e647bad1e3471d8aded3591d37ec01c9f39e8f4f47a19857cc4f5ccded6706db35721cefb6ffc29c859d5b4efd48defbca31827e8e669ef7ce64f88e1da7a1fac43aed2b97ed1cfbd565ff56e9e7e6d8ecddf62b55661ce9e2cd6ec7ad82bbe1f739d137ec3800f3d1e74e4bad7c845e8dbfd86aeddfab30f73ea18477d75857ec05a99b7ae7b121d3fb5f0bbbedbfc99eb299e98435658ae37959bcecf5699bbd93c00116eaa0f9b0dc4b707c0dc1cf3ce813cfef5301fb6b016043fc243e5410b36bf82b6dfd643676dc58d643cbabd5ac3ebc1c61a0a73e201bf35c191dcf6398639722ca7bd0aebf17ed8d7b1f59ccfb5a97b1ff0b74ee9dcbcc639f69ece1aa0cffbbc8a01ecadb3aaa93e7d8db107625cc1660c74fde8bc0f2b5acb0f057c925df528fe1578e03de03e905b5c2b7b0edd7d05addf6b6244b021bc4ffacde55aa8e5bd17aed318ecac874395777ebe072f064fdc217b0c2e851bce57ef791072c0f6c15c076a7bc8f2a0996bf741d39b3e1cb2f6735354a0631fc3ea916873c3b209fd6aef010e8c6cac6e4447d6589b3af5dad3dcd53a405ffdd62ac76a00beae01e82de633cefacd3366ea5be0f33e8a793e47e8fd82d53db54e6b9a3fc16e4df31bbfb253d335e9db023ffb82d1fce6328a3684fdc21ee2cf41981f83fa7ff4477f74118f0ed698b91ebeeddb18ed1ea8c07aad856077ada0de67e373f16f65f5393d0ebca40ed9c265f120106efa4d7c45a326872cdf2ef5a1bb917df0e808072dbd8d43770e73231063be23b497026e72eb12c7889fd16b668eaf75a0b652ff0afd627d680efa8c05ede6adc6d57d72ac1f7d8ec601cf446a2fe6dadbe7c9dc67aa18571d8cb7d60afc5d8f39c25c69fdd69c39e0bda0367bc23dd0c36be630d7463c07e2399f64e18ffff88f2fe2ac8fce5e43da0fbdd70bde5be318e9ef9ac1eba106f1ccd519ed07ac81c3d5dfb57f1c793c577503f820fcf9cc4de0e6a036f8c02bdd3c1dc1fc63346fd65be92b5c0b18d77975373d23720eb3b72fc164c6c9ca6e6f7cab5ab5a1b786fa1c8158e51ccc35be2f3f636b4f8ef9e0945fec6dbf73d7ee73300f989b5f1daa5f050e639e17071cdf4d31ba7f146cfafdce125dbff06eba6e460588738d5c8f71085f14f82da4affbbaaf3beb0fec5e4c5eb29f646f031e3a7fb2ea5f3ef0c1b379c41f17f8d0dd14dd20dd10d4c0a60eccdd60c4ba31f1b329ddd4c286b3c6ec439cdfb69187cd7533da07cc711d501dbf7346e78c2bfd14f49efdcc2fad397d503bbaf3d63d05f1de6368ae7a7bb36e516fdcaa0ed4a7ae30b7076be179316faf62be398cecc5d644807d434d21d63cea5fe5932c3df82d1b0f4e98f5eccb486d4663588beb6134ce3c46ae1ddd1fbb21d4e977874f0afb217b05d8141eb2e83c781e383ab7918d01ea6c0a3744c7b949a8e36360ec1caabbf9ace14675236ad736b1766ba8b70f71d4c68eeeb5b526f1ced5156d5bd487deb5406b21b5a33396d683399799eb9c51693f6dae0f9ff97314f70658af7a6b37577bc1d6b8a9775dcce90de8c073c3d73ec66a4767bff0c9b0715b10ef6f19f0a1829eee4be801ca3b404df791bdd0bbbfb09bdf78849fb35ae749e4ee13da399b6e0484b90f9f918d630c23f3b939eaef08c64e9b759155cc64f65cada1f9d645f4cd3e732ee6d53f6398730d62ac347ecb877dd685192f2bfbb15884977bf6c05e1bba52b4b587fa8c15ee89f765c69e536fd69db11ec2d61274f71471cd3b05b17c8ae5030707a63f13e54ff33d10fd319d1f342a85c399839a0f2e7c2a26874fd41cf8fc43e18c7e9a7d52d93fc95e013625bf8ccda6e0b6ad6e9d5fd9d914f8d9346c1260ce269b1bc679eb599f78466aa2fb42626b0f441bba31604ffda21fbaf9ed01d61574c47551cf78f459bf73f03aac03330f3a776dadc768bee316e66dc54d3f6363abb7bf39bdbf13edd3c77d58e56003ec5b35f5a97b3f019dda3e137d7ed26c1ee22744f6e839bf272bd6e4b065b4cfec0b8ccc6b73cefea2273a9cdbff49623f64af001b88bf10e18f0bdc3c8c0a872c7838b189d1d988c01c7b3793ba7647721811fa3937d6385f14e61eb06ee253b4be98473db12fd8b739d29ee633b6a6b5d0f5a9eb639cfaccad2eb58b7ec6d9abf18d53c03875e37bad30e3ac71cc2eda041fd27bdc1cecc6b00ee378fe7c2264ee1e9ccf897862acc59c3d74ceaf70ad70ddf6f15a5ca3eba64fc72f174ebf853b0fe1067103a9031b08016cc6a8b3f19c1bd3118cab0d9c936f1f7b41fb1e03bf422d5f8eda10c1265bf60971f57bddcd37469bf1cd536f1e36e518cd01e28ff5da62d699f315abdeb0655fe17391d6d8cac3e7be300e695f635aa3fbe8aad883431de1c0e693b1bfb7eae8078ef6fd72603f64af081b54911e9c8a9bd8c312817e3ad546ae71ce8b2f1b7efbf82d9ad2fef6b68e76f5c9ca06e6337a8d4ad72bed63ae36d6e4bcf9ad3973c058ef01a837a7d4aebe8a137b8171ae419fbadf9110c73a1abfc267018d51776dcc8d45acabdf39186f0dd7e45c1f76d7589f23fdbabe9ddb613f64af40377a372b82cf830498d7661c5fc9c15aadc90be10be341621fe8e1421d62edadddd13cf4ca8ada5731d662f4c544584fd7670fe34aebea676cac7363d1c17e706c7d600d681dfb4963e6da005b75ae15f193586b417b1572f4b52658d711f01b0fbd4f82ad35a5f5a06b9d35d4adeffdddb979f63b7b45baa9c1391bb51b5c984f91ea3037fb7c41f5cd3cf0a57104f4ce57ccbc9963afbed8d0758979ab38409fa2bd3d576cf9b15ba7cc78fbcd35ceb97165d69a39a5b6559d95cd7a3eefd94ff0cdfacd0774638c376e529b393b37cf7ec85e83d58605ec7e5235c60deed85ce7b54f1d7c01fcb4430ff479f0193fc14eac8734f3f952757dd3e7cb8fa03b87f66c9e76477d8d4157da43aacb5ca3a37d5675caca672e58bb31f5033d56d44e4ef3acb9952bc699cb1f50b91647af71c556fd19cf5c39b5a69d47633f64af882fa107161b74be4ce08bb03a748d99e853a831eb832f05f68a34b7b518f5cf9acc1b231ecee654a035bd27d03aeacd137de632578475f9f3c5d51a61d6017aad6227c4f47a5c6773bd0ff497e694adbed8dc0f601f7569febc1ec4b510e3bdd14f7dfdda5aaff952ffcecdf3e04eef9c051b12fa127483ea177d3d1c1a834d71b337aef3f962803e466c0a34a6711d81b869dfcaad14e27b00156b15f3db671507d8a794cebbae19075b7de67a26f89beb5c8ef964d6364ec18f34776503e61e96fa11f71982dd79f7cece0bcb7ee7af891bdccddc17c10dad5dddd815c6eab73ef37e8a13ecf69b6b686ec57861dedce618db18a92e336fbeecd298da8d750dc730dfd1fb620d75ecf6d37e4eed558c36ebb8ced66ddeec597fe3045b73843ea0cfbd50ba96d6b0a73e30d7e7038ddbb979f643f68ab031d98c6e7636b89b7582ddf88ee4b9d9ddd8ad618c3f8fdbdafc7c6b88af7ef4be402ba6cff88e93e6a84f5bedae89b1f3a98b71f63646987b98e8535fcd8dad9dfacac418734ed1c36e55b775b43b7a9f81f1d81eb207318af48b2ff9f4f4ba5bd7dac66843a8a17fe776d80fd92be2cbe3e8c60537b09bde0dbf027f37b7b9c0c8dcfcbe586595ef4b69dfc614fcc61ca331e6689bbe09bd95c92adedabd0e692deb39ae983ee7abbe72ac2eb669b7d6b19a722a7755a3eba900f13d50b55707fcabda1ec02bdfcecdb21fb2d78003c0cde986f650f080c5ef1f4230d7ef81699eb1cea11bbfb9509f766b28daa65d1fe8175f58681c3037be39137b986f4de7add13aab7973ace3bd2d33563fb6c632376e85b9c8cc05f31b03ceabb3ce79edc21c5f65dacd71b42e826de507faba271aa32eceed0d3366e7e6d80fd96bc08674c37390aa43ff74b79b7e6e68e6e4e1e75b366c8ab9c66e610d628ebd5cc420b0558f7c6364e66de56a6724beb5acb155c7b9d238996bb39e31f3daeb5747b630b6f97d5eb255cb3cb08e6b9eb17e7baebfa302d6c1c75a5c8ff5fae30a7cc4baf7c038ec80bdf557bd766e87fd90bd066c60377171d38a9bbf1863beb5662e60c3e78b03e6696b7d7dd8d44f71ece5c237fdb3367a05ffeaba3b37ae34dff931883346bd39a7f28b3d1995d23e7d16c6adface1ab2b293b3aa31211799b1ab75c02a6ef6eff5ecdc0efb1dbe2273b3721076937bc034c61c37bdbecec17f3d493bb5d0cd17fea18d1e64d66a3d7dda2b93da664ce7eacebba6ad38627c9157f10874bdea8d87de5bf3c01c05e6581a27aec51e5d07bacf45dbea0f8c5a539f3646a5d4df71e6436310d68400a39f6ecd65ecdce7d0b9b5acbd73f3ec87ec15e9a67463b269d1f976cd4d5d3ff832b8f19d1383cd1c46e00550d7ef4b02fa041ffda12f56d730d7d47e62ccb4833e048c61ac1df479adf39a1bef387baee6da660d7d8d1162eaeb61a458473b38726fbdf7da8c2fad33d7750c62119fb9f7a9f5acd1fafdf100a3358c01e6c46153a8851d39b5b69d47673f64af099bb587862f073a9bb7b8b911a94eeedcec2b9bfd649523ad2fb3a6fa2a766583d99338c5fa9d8b36304ebfbafec6ad681e180fed536a9f315bf1ed8fde43a9398d83958f5199606bceb1c3cf58fd8c8d5de56923cf3eede717e79ddb613f64af4837750fd36e5e3f39483f9908b66e7ed0c6272d6cdaedd3fc62dcca8fcd5ad415735690336b19bfca335e5ff3b13567e637d671c5ac639ef7b6fed653f4356e9533eb35bf758c17e7c66f31bf008b7565f6997e70ad80eefe295d17d0df43bcb5b7d6b5f3e8ec77f68ab821bb49c5ff42415f54e866966eecdac517069fdfdefa8218cfa860f7703fb53e64fa00fbc478b0e6ccaddf783f1d197b6a84f69a344e662c31ada18e6cf599757d267d86887321af31cc5b4bdd3e32eb80cf0bec37f3c45ee4208dd3d71eb38e7346f2bb67766e87fdce5e1136e4dcb8d0cd2bbe386e64e78e7d197c61f4959907730df8ac37e3982b655563326b95554dc14ebcfdcd6d8d55edd634ff18cd436f4df56953caa93e3c1f72baa659a3f3ad7a2bbbeb51b46df581c682ebdacaa9bd8733f3e6eddc3cfb217b45dcdcdd98dda073731b0f7333f7d3833581711ec08a31ad09b3aface2b4ad726a433f565f5f216e25407c753816533b54dfea5d664ceb3a4e1b395d43c1ee73310eace31c56f95bb4ce6a3cd5a7f3198b0076d6def53b761feedc3cfbddbd02ddb0d283af63bf5dee66676c3ee82bd46d1ea8cf7ee0210cd815e78eeac27cabaebeae019bfe158d95551d74ec7ecad786f43ab63016016257f1d38f786f9be32873dec3a95f005d83f1ed2733668579b215ab9d91eb709f351ebd32fbdb8b3935b4efdc0efb217b0dba6179e1e61f54c1dcd8d3e73875c517593fb407a3b1d0b17afb82bed218eb6e71cc075ddf565dc7da1a3fe34e712abeeb68bf82bdeb99d4e7c1b45543a68e7fabbeb94a3167dac1bc5375c151db7ec0be30ec87ec156153560a9bd7cdee46d6e6a1d94f6efd4424ad6b2de2d07929d09d1387cd1e8a3930ed8e2bea535fad67326391c69a2fc60071b36e6d33778b5943661fe6ad2fc7d603f8cdf5de6adb62d503db2a479f7e73e9657ceb192bc6b4760f517498b5b4efdc1efb1dbe06dde0be04dabad98d4177ee0169ac2f6ced7db1c43987745f9ead5acd6f6ced806dc5aabe98834d01fa7a1d30479831e6afec806dd27ac609be4a21b6f6e99f730f20ecaecdf5a26bd7a6dd1cc0469dc6ea2b5b35f88d10d1e75a2ac0f8c52f7ef1bebf7f83d018a88e7fe776d90fd96bd0978117c8430f29f8b5b9d16b03eb802374f3f75005ebc8aabf7dac5f7ff5d639c6561c767dada90eb3ef8c730ef5417d2b8c036bcd9cc68031da1991e6237d06ccc1d815d3474e6dd65db1aa6f7eb146857522e81ee80a36506f4e6d3bb7c77ec85e033725239bbab869d9c0faf899adf36e686ca0cd785f80be3833d71eda5acb1cb0c6c47858f90bb133de9ceae0bcf15bb9de17f0faea87eac6d626d8661fd85a0710c3fd9179afd0a70075146d3e33e6f6d1b625e6c296def515f2f1b96673b88ffa005dec2be8edb5733bec87ec3570730bba1b788a2fc1dcfcc01c1fa387a2f06da2df2af630821e48dafbb2e0ef1adb13c831cfbee6b78f3d88691c628df601edd61363f58331ada1df7e33474ecdc51ef5a3dbcf3f9d07627baf19675d625a1321075c33185731be7346685ffdeada8bcfc49ec630b26fba166cfa9b03dedf9ddbe3c1ddde390b5f0036aa2f027493f7c0680cf8d258a7874837bcba7d94fa6a13f3b51bd335992bb5adecea52bdf130e745dfec33e7600f7d3366c643d7558ee593d3bc1e4093196b8d598b794731aed42fd629abb815e6cefd29b3f69cefdc3cfb217b4538ac7c79dc9cccddd0f8dde0f83ddc049bb1fa19e73f73a8cf7ceb093d982b605d40ef8bd6b171add95aa5f6793df631c6b839cebe30e7c64eb0aff227fa5775f0752d626d416f2ca037ceb1f58c41bc47fa411fb13e57eb4075f3b4394e3bd4660f31ce5e4a316fe7f6d80fd92bd2cdda0dcb46f5db4636353a7e0f3a6399832f62a186b9e8adafaecf17a707a93621ce3ad3be45e3660ef5db5b3bd8a339c6176c0ab1addb5a62cdfaa57373b475447c3693f6725de6ea730d2b6aaf6e0dd1a7dd2f4c823efbb0169f6f9f33acd6480c3ffec0c7f5920ffeda20f69957dbceedb01fb2d7a01bd5cdeb9c8d5ed8e8c6490f4a3106f1a5a056eb4d1ff405c10eb5a1770ec69d62d6763dc8ec21d58d2dedadee68ec56bd15fa1bb7babeb98e155e23636bcc35f8dc66efad3568c7865e1f60a32fe3f43b6794ea5bb5d827bd9fd6715ee67ce76639bdf3762ed18deac645561bda979111c13e5f50e2d4819703ac0bd616e6fa8d81eaf6626cfd53cc586b32da7782cd3cafd5b8598f393efd8d538799379975a475a75fdd6700ed839f39624c6b34d638688c1cf3817e46fc8cf3feaa77bda2aff9d604757c083566ccce0bc37ec85e916e5ee1e5e806ae0f3a47276e1e9c7ebaf5db5a6b82f9ce19150f34ebd68fcd4fbe5b07cbc45ed2586aac5ef8ae817e0879d8cd67447addccfdb656bffdade71cf03bd67e0a635dfbbcf7602f7b98e31cc8334ee9da15c05e1fd407f6702ec6417531afeb50ec87b806041fd4efdc3db7733bec87ec3570338b9b56bb2f71373ea307273a1bdf397830996fdcc47847304f7a1880bd8fb1eab545fb319a5b1bd0b3d7481c7ae3b4b9e6da8c51047f634a6dea2b1ba06fd5daaa0ff5a14b75af6785f9ed0f73bec23c64459fb3b5ace7615b9be3ceedb11fb257844dd94f06c05cfc761f9b2f821b5f1b828d39a32fa4629e23366bd8b7b5d5a1fe63f6e9636e0ce8c7a6386f6ceba03b9fbaf1d05a625d311f7be3cacaaeadb5b4b5569fa1b1f3139d7e703de0f33a86bd2ad2b541fd5d17f8dc5739ae49df6a54277eee21a8be733b3c75e7215c7e7a3b9bf06b567ff6677f763176033377b3f6767a887ab8fa02a1cfc3b1e0f3855027d61e1ee4a57eeb9a67ad73982f1df9d673ed1dbdc615e6b58690632f6a786fbc363fd98371f6542f2b3bb639077b4dbc1672ec65be36204e5ca7b18eb553931cf3a5f74d9f39c0c8c1efbc344e9cdbcbb9b5992b828effe9a79fbe67d9b969ce7ffb76eee3a6856edcb9f1b5490f475f685fb4c6ae6ad4d61776e6302af8fa229f0339ad8d580fac3be7304763c06b6d4caf83d8abae959cf680daac5db0cd1c39d6bf75d511ae0118e75cba0e6b80f7401df4331e5b2be0573a07f3d55ba7f69ddb673f64af881b197123fb726e6d5a0f175f447460ae0d3a62270fd13efb8a763146ec03c61dcb076bb4ce16ad695ea9cdeb418c57409feb356e825daa436b9d62ab76738d71ec33619cfe6943b0354f98fbac678ef315e429c67acf4a6b21a2de1a3bb7c37ec85e0137249f48bb313b6257efc6258717c9d15aad535d9c335a1bd0fb9b08fa8863aeee0b8cb4c6acd5b9d87b825d69eeacd3979ed83917eb18e31c9a638cb98e65dae67a8ee5eb9bf6a29f5a08f5a700a375aa3336067c5ef3398273c0a6d8df358075667d7bd65f9f3ffedab91df643f61ab821e7666613b3e9ddd47333a3fb5238f7a0648ef82385e696daaca35e98fbd25a7bc69c83b932eb745e7b5f7e208ef5b4167ec53aae59ea6f2e6887fa670ebae82bcef5d53ff5295c27cc5e1d57f7a2985b1abfaa5921971e15ec8caec9b8d9abbe9ddb613f64af009b11dc9073b34a3fad36964d2fc4609b7132e7b3d6297cc1ecd95cc5f931e63543af75d69319effa598f6b2bcc5b77c5cc81ad9cc6aacfd8ad7b0fc6ae7c424c6b12ab788d33dfb963fb68635db05a0336e6edad4ecffe21ac76a435c07ed3be73f3ec87ec35e8e675ee6645f72041646e683ec13237d74fb0bc608af55b5b9b10672fe31aef0b0bf683394eac0133865ed88ee54ebff5bc2e7dae9bb93a822ee6ce7ade43a43561ce8bf18ced29730eda90ae7925d0e7e708b5b17e6a79add8da4759a18f1c853df4555ff5551776bf4382de4b7b98a3adf13b37cb7ec85e0337aabab879e7c186f4a563d3abeb339711ace17ca2dd5a4a214681d63f17f35b7bb5b6d9dbb9315ea34cbd73986b5dd5ef3db58679caa431e0ba1a8fdfbaea8a3d671e82dd1c6b6d613da0d66a1da536f3a0f1d644a8278d07e6e4b077cc6dfd9d9b653f641f11377037f6dcb06eea552cf4d015e2cd31bfb416e89fb1e64f66bd63183bc7b9867e3a9f6cd965fa987b8fd0dbd3f9cc01e3a0ba98db03c69a820ff023e64063e7f3360fcc15e7e6b6ff39747de6752df56bafcc7eea8c5cc755d6b27335f643f61a744376f332227d49d9e0c0689c9bbe9b1bdd97161fdffa992bfa8b3d8d2577e6813d85b9bda13a5883d1bee8da0b75bd1ef5ae0bb1be3e6024c79f4f5b1bdd3acd057463dbcb3ed6006a772ed8ea73b4363e46c0a7dd7ee01cbff9604c6dd60275fb1b670f6d5d9f3933c6b93e7b2bd8fcc2673d7df63077ff71c1edb11fb2d7c417d10d5f3aefe6d7eec677f377b377f3832f0b7646696cfba13b27a639505fc7d69858a7b53ab727f3793dae5f8c157d8ceaf8bdae992bd88d99766dad39a95fa15ee35b17bdd7a5cfe76a3dedad23da1aa3803590de37e3a1ba6053e63d595dd3bc8efa776e9e076ff3ce59b879d998ddac3037b67eedbc381eb0a05d98b726820d310756b6ea80bfa35813ac03d8670d2146bf31addb3a32af7d95d77e331f6a5bf901fb2aae6b85eac09c58476dac9be724e6d947a9cd78f4c617e67db6a539c4302ada3b176dab9eed61cccc2fbde69d9be5ee53df398b6ed27e1a2dc4b061ddd8bc346e70475fe4553e1083c88cd1df1875627d61e64bed7cbe50ce674fe365ce8de75ef49ae7b561d707facc6bffad1ad5c138a5f18831e05c8c07af89d178d7515a9bdce907fdf818ad6d8e3dc138305e3fba22ead641b4313ae757b860aecf1ed3aecd5a3b37cfe5b766e7246e4a5e20367637adba2f979b9711f1d7b4c8f3a5eecb8dee66efa6375f9d7c697c7ba1fbe2996bac38770dccb5cd3efa191b8bde7c46b00ea35f90f0a16b67ae68d32ed54be3aa336e09100bde6be7d0b97a7361ce85586a2240ccbcff60fd15ae49ac3573ec33d7612ffadadb6788ae48f5d535eddc0cfb217b0dba21ddc4d8dcd48cc630ead3ce61eb0ba5cd7898faf4433f49cf3520d01e42ac71335ea6cd5871ae40fdd54beb12e341a14c1f73afed2a5807aa03b5c5bed2fe826d65f73983758c354e9b7ad1bec21ac6745c3dd3599bb5757de2fd9ef7d45e337ee766d80fd96be0066673bac19dfb6955ba71e7266e3e2f8052f403ba35a6bd7564f663befaad05691d201ec1e68be9b5336ac36f9c580b694fbfc0cc2f3448b1b6d7d3da624e7dd85c9b9f9aa17d66ad69576f5c7dc7c06f7faf41dd9ac8ea391033d701dc833e57208678bfd80a3ab1f85b0b986b734dd21a3b37cb7ec83e026e4c36ad9bd91745bac97d597c091ca12f80f91da78d3845f44175687e7599f1d0b5b9aed9175bfd2bfa321be701e1fde2d0016bb407b406cc5e8dedfd95c653ab02cd57378751e933636c1eb5887144a47133c635c88c35063bba76986b5067fccaaffcca0bdd58316795b773f3ec87ec356043b2711d157dab971bbbe2dcd15a485f387d88351b6b6dd0be457d2bfd54aeeb723ded7dac46d7c5e82185f4a0d136e7a5f78618f39d4f5aa3e314b027b48f186b1ff5d5cf989d8bb62d1a2bb31e60abb4170233079d7b8e1823cd6fcececdb2ff9711aec8673ef399fb9fc080db87ee26f5a5e39399ff58c7a94d8c8f97805c0f2172a6ce386bcdbac66b37575da6cd3c75c1366bd4df6b87de97e620c2bd61cef5ce5e7ea2e55398398d11fa58ff1ca8659da9af58d99b07cc11efc1f415e65e1ba3eb6f3eb40658077be3b433cefb409c764664d6c5d75ae85ffdd55ffd50dccea373fe2eddb9804d88b031d9ccc21ce6c605c619abbf9b5dbdb9d675d4df3c9973696eeb6a3f0539beb860ee6a6e0f7da04f9c5780c3875c0f07d70a8d0362ae8375ad35eb82d720c66843f77e188b60536a67ad5e1b421e36bec8a00339625d467570046b1927eade1f735c2f98a36dd6d8b959f643f61a744376e302f3b9c1e7469e233e5f4c99f5ac017384eaa5766ab4b7fa29a8619d53f9c7ea6ee55a7bf6e93d99fd8b3e69cc4a9fe3ac7dec597414fd8cca9c23cd478c931e848ee65e05e3cd6d7ee7ae03dcb33b37cf7e67afc87c090ab679c04237353a316ef66ef2ce1dcdad0fd07d29153167c6d34301e28c85e635b72fbf31e62ae0e1640f72b041eb59bf7ef23a07d729d66e4f20afb6a977b4af7ac7c65a53d0b517e6bd3fa08e1de113ab3f4602ebf439387a7dfad1c9b587028ec4d84bbf358cf14715c6194b1c02c6eedc2cfb217b45dcbc7363b269f50163851706710e6ef0c6694344bb79d058b1862f50fb19875ef4f9e219c7d85c75702cc478283877540762c0f8ae11699eb6c2dcb596c61a539b748dea8aa84f9fb5bae6b2d50faca31fbdd760aed7662c420deb8875c07804acd37ae8f5af6aeedc0efb5dbe266e6c70d3322280af3180de8d3d7d628dd6037b4c5636a89dfa5b71d0fe40ec5c9f3221966b559f7daca5dd1a7dd167ed551f98b518d5a5f35953dfd45d3f585329ce5d3b79bd0eb0767b38d656b0837f1848cc2ad7b1fec23aeaafb81f19995bafd7be73f3ecbf5d7045f8ed822f7ef18b17ba9bb7b7d00deb66ee864777cee89f32a3f37258a731d05ce3f56d61bd19d7c300ec09335e9f63fb626b2ee0637dbd1640c7a78db1f786fbc0e80101fe4111180b8ec55c466b3a920b5d932310d71c69cfe635d79ad30ead25d4ec7591effac03abd0f306b3b5fd9ed01d636ce9a8cf6689f57bdea5517f69d9b653f64afc8673ffbd9c317bef0850b9d4decc666b37a2b19fb02d507f81ae34b01d8bbf9ab83bd106bce796b5447f423ae438c17e3c16bad6d829fb8f9a2625fd5d5e69c1131dfb9ccf8399fd48f8ecc5863c03846e37c86cd6f1dfd2b8c95d67514e318b9fe552e34ce119feb50f71ee237d69a7d3eea8caf78c52b2eedc59d9be1f2dbb07336ddd46e6cc44ddfcdadad7636755f8655ce6a348e51d43b2ae0baa0b9b5437b4c1ab702bfb51db5436b36d67859c515fde635be98d7f81e2ed27c73da77e6eb539a8faed43f6d2bf4176ca79879ed65be36f552dfceedb01fb2d7c04de9e666aceed8cdabbde05f1d8053b02bcc3da0051dbbb590fa3d5cb08b7510fb6877a486421c9f721a3fa5df0ab717cc39b84672fc5924780d606d7521b7f1ead69cb49ef5b5093ec4fb05ad0dc668abce3510df35346ede6bd05ec89febd3c68858c3f5591b18cdd7663ee8afadfacecdb2ffb8e08a7cee739fbbf89b5cfc5cb69bd98ddfdb89cdcd8f0ee8087663b559a3b6c2dc7eceb555776e8ca37e9971f54df411d77582be99df18afff181cd2d4f09ef51e95aec11ec4b51f609b7674edd49febd23ff58975195ba3f7c61805cca1b773c0d65a5bdfb65b53ddb979d69f71ea501d5ccb2b5ff9cafbffdec1cecd71f94be8ce59b0693b8a9b5b7b377963b5331edbfce63477a26f1e16607da4b55acf3932fb3ba7b6398d33afd80f9a730e33be79d6b5f6ec535d8169efb5f460f26077de71423ebef6706e1d458c7504fdf521cdab0eabb9394a7b543fc5556277ce673f64af889bbc1bda17ab9bfdd8a1870ff197cca11b7c7eca01fc8db197a06bf3db76a407c91ce9632f7289350f616e4fe6e8eddb51bf3988f7059bebb01fd8cfb8ae47dacfba8a36477b13afe8176bd9c7983e0b31d71cb03e4c3bb8868aa893471c3d9db7a635a4f7a535cde3d3a7f9e41aaf5f69cd828ff82dffcea3717947ef9c848dd80d89cec6762333d75e8841009fb1338ebad656b776716e2c68f36783b38eeba4a736c4fab3a67346e35caff1c680d7a3adf53df8adcde83abc2ffa4aeba3776e9e76fb17e38d71eebdb0677d5dc7ac07f81b635eafdd7a735ebc7e69ac7646d63aaf55f46b9f6b90f6696d625a7fe7e6797807ed9cc4cda92ed89cb369d19df70f8584b939c6cd98621c31bc4cf3e5b206b46699f58d99f1736ede8c9976e6b58336754566fc3166ee291acb3d026df4755de27c8e422e36460fa73e076b8bf35907dac3b8558d32fdadabbdf566dfadf819b77373ec87ec156163f6e5727332f207628cd8c1975afa69ce5cf34f419e02e4f56065b4f6ca2673cd625db04f65ab067aaf051d9c1b07eab5710d88754ae38079e35c1b683b56439d9c1eb8ea8cd6448857449d3e8dd506cd619c6b9a79603defc52a0fb45bdfb56353f021f3b71dd4edcb88adebd8b979f643f68ab029c11782d14dec86d7672ca0778edff8531bbdb9c6319aa78fd19aa53993e6033ad2971569eeecd1b971e6a98bf70ab0fbd25383b1ba31c6c196df3560672ece1957d7d4786dfa57e01775e39dd70eaccd18c46bdec2eb33bf605760c6b42e3e6ad91f9fbad88bd1fbb373b33cfc46ee1cc50dc9e88664acee58fb8c076c403d31ce18c16e3ccc5a7d7119a969dde65a1b69fc049bb501dd836e32e3acad5eb4d5aedefb20d3e741d0fac6146c8dedda679ea331de0fc762bdf9cc10eb94d967ca8a2ddfece1f5699b3ee31174516f3cccf9cecdf0f0aede39899b91cdaa60e3c5ab4d01ff04d838e88b0ffa1175602476be54c458cb3ecd5137d77ed6aead98d7dac699678c7d41bda3718c8ae8b71e63bfc565b416ba73f3d49d2365cbdeebaa8f797fcb60e6097182beaa23fa2bd07bb8029f7e73265c87d722f6d06e0c36ead1b7f5ecc1b8d567e7d1b8fc84764ee246ece67483fa723abab181b19b1dd41b03c4a1e35764e6fab2329f2fae31d6a8b81646f25c336087da8c47f8d93273e87a667f30cedcd6f140456f1dc558512776f629b38630278f75cef54bd7770cd7604de39b377fa302d0e9d1fefad5c9b14e9f813407660e60a387b1f898b3269f9376e179ecdc3c0f76e0ce59b029d99ce028fa7c81005dbb2f8cb66e7038355fd15a087de6ba405fa90d5d661c73fcf65ad1c38018e518d685e6d4be8571c6326a07e72bf41d8b016acd75744e3ee2f3761de6e92fc6c2aab6b6ead6b1567d80ae8fd14374b2ca9bacf2761e8dfd90bd063dd8d8948e4a377963e6dc6f4dd1eb9fa3b526f65287d612e37cc1cd01e315625787a93510f599631cd7a51dd18ed4defb88806b33c67ec578f2bbd6ad1aeda3de9ac7f4cea1366a3957770de87e5247c0beac4fd1bec25ce25ab7235407628d57ef5afa6916d4b1cf5a3b8fce7ec85e03362d9bb11bd54d0dbc706c58f0c05188779c1b7a356ffca4870702d501dd3841f705b78773b09739c6a81bebd8c3861c447d428e31fa5b935a65d6b08f398a3e7531963aea137d2ba6bdebd1c7d8becc899bb1b5cdba730ecd176dbd4fda18bb27c0b54d9bf9cd59ad61e7d1d90fd92be2462d6cd2b9b97df9ddcc6c607cb5817a73cbd6cbe7cb03e8ad2fada73eeb6147ba8ec9cc3547b1ffac0de682b13047d8aae1bd9519a7ee681fc6f69ce8575fb1ca37b6fdb479ff6b0362fbbc1c1b7faa0fa0cf38f0f9cde7afccbcaddacddfb9191ebced3b67e18675933ab239dda06e784663671ea863478e61fe56ac767bd686f46506d7685d30b6eb72341eb6e28d997698f702610ec6396227de79d7ae0dbaa6dad1ebb3373056664d3056ead7377b80b58c5727876b70147cb306312b5bf79635c5ef9aec878871cdd1bfb2b5eececd70f969ee5c0936249bdf1748f89b5f80ad2f05529dbce9376fce895164eb65acbd79d6535704fbbc9632f398833ab9d03a087aebaa375f1fb4964c3f10a30ee8d66abf399ae3a85d5a13f0378739420feeb5f33ecbae1f9bf682cd754e5feb82357a5d80ce3fbbc99e6b4f683eb4c6566ce3776e86fd90bd226c5271d35667936aeb869d9bd7b82d7b45bbe27cbe288d55077e2e3c691d62ad355f6eb016630f70304e9f733f5d41d732696dd730e35bf718c4906b2ce3b1bc736a4e660ff09e6873fdb3beb9a25ffbf4d9a7f58c5380feea605d613e6dc44f1bac6c3b8fc67ec85e030e0337b6828d0dea1cdd83a687e18c15f356f81237c69781511df03b37967c7b2a8d63b407a8d7661e6037dfb17eb17fd7d11874d6d5b5a0fb45419b315e0358d3b9233d5c9f23302ac5bc424cedce6bb34ffbb6fe56dd42cccca98ef827fec8fc62e97da13771487bb84664d238a10735776e9687effece51dcd46e46e66cceb9a1198d65ece6658e883526be5c2bdf2abfb1eaf6755dd235d4d73a1530be737547616e6f7463672fed8cab7a8c8a31406d63b4d5e7d83a5b581f581fbab5405ffb63a37ee7c681cfdd119931d8ba566b8179fac1b529d6248603b83f32d0eea80dcc0346a4b69d9b653f64af811b72b5319de373931bcb4b02cdd9aa610e028e30e38b39336f8e5b509b1776be98605d6d5e1f786d30d7478ef560d6e91c71ae0f3c746055dfdce913ed8cb336586345e3a0b51ca7cd1ce7d2fb3a7364e59b31806d752dc0bc32690f216ed567e7d1d80fd92bc246e485f747016e620f19e76cd679a81ae7e1c47c85f9d0d19cf672549c3baa03ba7580d1b528d426ced16f41f171f8326a43a4076eed2b9bba3d7a3fb68498d9c37ce7a0dfd13c6b003e05cc0563a071c60a39dc87ae4956b195f6987ae7c4faaca16b41f083710a3ed7a5eebd728e6ebe71c4b4ffcecdb01fb2d7c48de9e61437b036fc82ee267683cf392371ce85392fb53dc1911aeabe4ca01db10f7e5f266b4ef09ba7106bedd603fb156d8c8d35971138b89b6fbf0a7ea5e86ffd49bf48347fea4af15ee9b39f620c3e46682ccc78616e9eeb67eea800b18da94fdac7750371f31e34b7737ad4b77333ec87ec156113b259b77073331a478e87d92ad78d8eef58edd66b7d690d6bf6003a56bb986fadd55c11fc52fb29cceb782aff2af5652b07bbe27c35ae9887a390336d65fa98d7b6d513bb3ee21b67feccb536f619b3b2cffc9d47673f64af413f29809b945161b336c617d24dadcfb19b7beae4324e695fe67e3baf00f55d131803e6cd1c463fd520d6e85aadb75a7f7b36561b60efa758e310f2db1f69bc3dd1019f32d166accc1ae29cd16706d4c186787daed3e7636efdf6717d5d8f79cd578c03ebd466bc50a3b9d55d2330b78ef9c6ce1e3b37c3dd1db07365d88c6c4a36a79b58dd8deac6e670e8cb669c60d30e8c53b7a676e760ae7115d007aec3b13e47f2f48bebed75743417b0791f5a9b79e3a9e51c7aeff039471a6f4c6391aee118e451bbf1d6b526e837d6bcc6b92ed1479cb1d0351a63fde638177298770f4163c0ba8cd605e7e6a2cf1f1f30ce7a3b37c7e53769e76c7c89d8a4be1ce8d0cdde4f5f6e6437b5629eace6ca16d6f6c56eac7d56686ffcea659ef9ceed579aa348f563d703f81b7f0ce24ed513e38ead059f32696cf5ae8171d684693b16676ff759d7b49523e63696e7da1874e78ed6dfb939f643f69ab0297b187988f229413b2f073a3637bb9bd84d0d6eea69170ff11e688e400ffb03711eee0a74b4e6f45107dd1846ea22f6c7e63542edaec1d17ab31ffed6a85dd02bb515e6e42ba5b1d337e91a5a6b8ee25a1aef5cf44147ecde376dcca70d71ff4875606e6c61ee3daedff8d9477de76679b0cb77cea69b15e96101d8f8fbe4fa8517885c0f1ec715cd2386b9636dea08f55a1bbf2f2e30d7d6b93af89223d428da9b0ff403fb836b3216dd7cf356ebb23f781dd687f628e69b2baec3fe5b4c1f736d8cae95decc5d93a3f1ed87f47ecc6b43ccb3be6807eb18679e309f6023d69eadd735817561556be7d17878b7ee1c854d78958de8c6f6e5006d2bfa32543777d57bd62366da5a53d06b37cf1753b02b829f97d597139ac7883467eaad699e078073692e4cff315c8b4c1db17ed7d4b5a11fab013da8b075de11aa03f1bdf6557de93d2fe6511bb1de0a63e0dcfa3bd7633f64af415f906eeac206c7c6e8669f9b97b9f9136bd6e7dc97421f75f8f1002336ff6003992f9af9da9d83eba98e1f7a0df4c2878edd9cd6720474e7c45b937c608eb06e215ebff915eda7b076997398f58d315f614dfaecef35f55e80766dc640e364de8fc6ccf8553eacfab90e6df891f6036ce8ce776e86cb6fe0ce59b0397d59dc94883670a37783bbe1457b5f0ad106dae9eb8b50ac698e2fcfac611ef15b3e708e10d758b05faf05889b36687dd6a6de75b78f7566adce5bb3ba386fdd15e61a5fbdb9de7bd7400c3a76ef37d44f7c7d5d87fe15d85d03d88b513ba3bd6b53a831ebb706e27c15bb73735cdec53b67e3a7394167c34edcc4bc0c6e663779f3e71c8caf7deae4f922af6a08be89b9b307186f8feafee19eb982cf186555579beb7e54a8b75ac75568bcf5a8035e0bcc3838d5cf5c69fcac8d309ffb6bc618a74db431aefc807dd631d68377e7e6d80fd96be02605460f0bc62f7ce10b975e106c881bd90dec06576a231f1a0fd58139f18cbc1c0a58cbded3b6e5d36e8c73d644ac021d8db5bf1823c68239d85a6be68879a758c5d5a63ee3e6daa0ebc2e6bd517abfc56b3077d674247795475dece8d32ffae63327178c355f5f6380b9313bb7c37ec85e133739e314c0d717808dede109c4e1736cbc3f5395ead62bf5f7252396b92f97366b3062abdd39628eb180cd1ef51b8f38b76fc1669faffccaafbc1f37c1b6b21f63f62aae135ceb847ec6b1c65e8bbaf701dbac695c9ff3ea9e5a8b519b73d780c0cc6bac31dea73e23a45f18bba71c8b758ddfb9391e7e6377cec28deda67463fb873773a336ceb97a47c4dc6957449db5e833d77e93e643e7cd156b4e9bd70fd620065d31473fb48eac6c603df52dce8981adb8b956fb3a2acd9b7646f1b0137dd8bb6f56186b6d30873935187de6da1d116bcf754963a4393b37cb7ec85e1337a39bd84f2c3d7c9c1383bf2fdf6af3138fe09b529cbb0698b5e98bb8aec6e267deb582f3daadcb3f086ddfb91e6bdb4711e2153e5d51dfb9ba546f9dda45dbec758ad98f7caf99b96b62adb39ebd18bdb7c418c75c5b31c69f67eb9f71ad8fb016759f0539f58b36a96eff82df6b90ea3b37c37ec85e11372b9bb32f8b87879b143b3a628ca3e86f5c637c01fa22b4ae523bac6cadab6ebe870ad06b1e04c4740d8598d686ced5e98150a730475c836b9a71a05faae39bfe53ac6a31aabbb6555d6ca29fd1eb146cd468dd897662ada18ecf755843ddfba9ddbeb30618a32efaa5be9d9b613f64af4137b39b5cf4cdcd3a63dddc8cf385106df591dfb91c7b3956f1f434a76b425c4f7ba9973997ae055de1fac19ad5cfad5f5b757b5c05f31dbb06eb315fd5c5e67df2b93616ddb9e31460341699746f28e4f4e7bedaa5ba7d56319defdc1efb217b0dbaf1ddc4bc68d87de118f163036cab1706116b017a451adf7c75a439ceeb778dfa15e3b41ba39df57b3d80dd18d0d77a52bd742dde3304b4cf39686b3c6cf591d652041f6bf03a15ecabba5dbbb51aab8d38c0d7fe9d43ebe933ce7bcbbceb9a6b44b786e013fd8ab4f7cecdb21fb257844deacfeafaf260e7db6c7437bca39bb7363775fde0bc3f0feca83e698df63067fa115f4ed0df7581b1d8b93ec4df0a40cc9f23a85b0becc9e8bd64b4d6ec0bed33c1d7faabb8d699b1339eb570ad4a7f7462ae79c43a5737861c747de66847d4c5187d8dadd42ed5413ff58a36fb48fbeedc2cfb1dbd221c0a6e4e4637ad2f9260efcb058cbe3cce27c66fc5d86f82cd759d8bf1edd5eb405ff5d30e5b3dbb9e99df7ec61943edab32eb4fb6d6d86ba8806bb3b676c1de5851dfcaaddd11bc27c03df03990db788458ecdeabc6b417e8d32f33c79899bff3e83c78b23b67e14664a3a3fb32946e5463d5c178ec3d54acddfcd6b68e2f99f1cec1f8bea4602e367df636c79afa9ad3ffdc34a33a986fbc34065c2bacd603c4546a07e732e7a720be425dfbb70722ab6beddab816e7ead652772ead8fcf1ed89da373dfb52b736eeda90373a47360de9e3bb7c7e5b762e7241e36abcdead803a4e28b83f089987f0ed13ac518d0ef8be288d807b4cdf8ea8c8a87011807fa90d6549c83795d87182feada11fb38effd51c72eeade67698c6b7284eab3de8c67c43eafbfebe9f562e7590a31d8ba466c604d712db5817da8c1e8dc7bc55c1decd5d8fa8c676cac3132e73b37c37ec85e11373e1cdba87d31a03aa0cf3931e481751ca13a182bcd699d1907ae0fe19a7c19ad21b3d68a59df38ed8c0ae867f41ed0bff763456b7858c0ac0fae1bd95a77317ec5560d73f4b5c68c675e81c66a731f08314ae3447bd1d63d35e37cdeea326bed3c3ac777f5ce25d88cddb4d0b11b17983747b4f1872a6e6afce8ce61ce89b10ea3f5116dbe58e631f7f0d2af18e3d8dad58dd726e4cd58c4f5b4be7fc055bf79d4f60f0dc13a4aa116428ef5567160ac3a10a75d5daa8bb5e9d51a0858a77331af7ed60dc4cd7e9d4f3fbab5d05b13f0718ff51b03cec1e7681c18eb7ce76679eacecd7df0a4768ec28f0afeeccffeecd2cfc9d8d8cc7de9bfeaabbeea62b3f20fc5f485729c9b796eecadc7615cf36bb33fa037c69ad5cd01f3b6208ef8e68a76c47a3063ac01ae83be1eaeaeb76b87d62cc690dbbacd2ded515843af8f18eb4db0cf6b249e1ab3b7ba3e63116cccb97674f7d364e621f627cf1f53f8c5da78304fea9bf7541fbf35f2f297bf7cf39eef5c8ffd6e5e017f1edb8d8ccc4d49cc0aec6e7ceb28505d883747bd36b03fe3b1b574ed608de6acea93679dd5fa3a4ebf609f31f6d10eacc539a37933ae7d6a17fc88d76cad32e7e2fdd0efb8d567da8da7b77ddb7f8ef8eab79e79a27f32639a674e738d99bed6d9b9392ebf913b47f1db31f0e0613e3767376f373b3a2379f330016cda95bea8d5a1233ec02fd88dc1de9ee8e66153f4a95bc33954d7a7ad3e305f11e298d39f91399fc8c49aae73623d731131be369871ea7daed8667ef3bccf609ce0a396d7048c5c03782dd643b75e7b60a70e428ef9d81198f1d69b3675d7e4a894d6dcb939f643f64cdca0c046eccbe4c6f42542fa7274f332f6a5d16e7d473196b135a075f4a32bd2bae66a033ea1ebf7e56e2c58d39e2bac0773ad625f6be14798fb9dc2a4d70ee640d783cd7c6bf6be80d7e83a8018e3f06987e60171d810d700e8d6d0cebc50c33a602c423df3812f38eda5bf73e9b58035ecd55ca9be73bb6cbf313b0fe1cfcee68b323739309f9bbd6c6d727290e61bab5d6a873997ce8d614d88beae9118a5ccfe50dbeca3ccbcc649fd629e32691d63b519df5c47621aafaebfe893555c6bf5a0344e1fac7ca26e0cf85ca82bc6ad6c501d5aafeb58d9776e9efd903d1336bb9f80606e74e6e8f3a5c0de0dccdc5cec2b1da8836863b4b65897519dbeea501f63eb3a6227afb9c6166295ce89d526daa9a90ec6d606fee14de9a74a46e6ea8d35a63d1431a7e233326e55d7da30fb0036efa952ac517bebb886551cfeda676de6e4331a8790e71a6bafc8ecb773f3ec87ec99cc6f237d01d8a4e8ddec5bd4a76e0dc66e72ea31b70fa2cd5c21df97a5bed69c791c6afc6932e8eb3a9482af355a73c6eaa3a6eb067b80f7d4d85e9f7330be07b1072e6813f389c187b4963dbc6f5d87022bdddad26b93e6d99739ba02bd17d669ad5e23e0b33fba3fc3d6ee28cceda1301773c071e7e6d90fd933e966456753ba316b1374373123f022a96377c3a32be2dc1aeace8b3e31c69759aca36e9cb93dc4b0f54030b77ef39dafc0dfc3c2c344c8e33e787f1da5b160cfadbeda8da9dfdceace019b32a9ad393e43ef136363d1db13da77f6d24e5def55f52da883b80ef120761d9515c77aec5c8ffd903d936e6036bd2f97f812749376e39b3b5f02e6d43216f19315623de7a573e2a61f6a73cd7df17a2dc6ce1cd7772ec4ce755b439f52fa0770cd9fb1d87a1f5da3184b4ceb74d4e7a8803113d7d25ae6807a6d65d6256e95e3f5d7373fd57a3f1b07eaf8942d3f74deb89d9b633f64cf800de81f7a410f294737a92f7b5f145f08443fe0e3e5e981816efd622d6482cdb54cbfbdc17ce61ee460bf19eb35f06305edd23efaccb70723427daf91d1780f3875faf4fa67cfd6b4576356f1c4cdeb12fd1dc1b516f3889b7ef214fb29f5ab63ef75235e17a20d8c6b5e6de01cffb449fb8071c27cda766e868777d3ce120ea56e70981b79b549b111c7d84d3e31d7f855edc694e634a62f7373d0598b875f7dc7eacc1ad606f3b4a9cfda455fe318addd1afaa1b1d0bac622ada160e77a045b0f2063c43ad326f844bbf1fa56237ec5f5e8d75ef0e99fcc58f19af46fc56dd9776e86fd903d03bf8d65d342373b76a42f889f7a6bdf7a41a8e92790d698e04388751d602c3e74fc7c41e841a25dbd9857982b9dbb3e58e98cf4f67e697344ba8e59d31c47614e5eafcd7ce07e208d43a0fd675da87fb516b0177efb8035b54f694d632bd0b1df5d18e35c6ab3a6cc58612de6d9a3d235eedc3cfbbf5d70069ffffce72ffe2d020e0f37760f325f2afee9c27eabbfdab4d8c8d3472d72013bf96e7c47509ff6b2b2cd3c05da772bd718f38cb346d146bd5e8b35a07db887f85ab3f70f9f36c6e6da8b1ae5d8fd3707bbba23aceacfd15cd7edbdc3c6680de7f8bd8ede07e6883abe1eb2b2aa6f1d6399fb85dd7b05ae051a6b2db026bce215afb8f8b737766e96074f7d67130e4f36a62fb49bb22f4d5f0670f3d6066e704675720b36693da93e697d99b9ce8d9df1057be38e31fde6707dd669ad8ae02fd3d71ad0d103c41a8ed25acd53cc6d7efdb541af6b628c30f73937de7aec2d4644bf3e75a90eee3d447dc5ac3debecdc0efb217b06be4c7e1b3c37a787ad1b5d8873d313a37f8af98cbe88a0bf6cbd185d97ba624dd7e058e61c6ab3d6298cf19aac613ec27a7a9d80dd1c200f9bf9cea760f77ab4c1d465c6314764e6184b0f3e25f6def96cf1f77acc05e2ad01f5cddea0ad71a0dd78fceaa06e4cd709d5c138707d3366e766d80fd93398dfc6f142cd8d393729ba2f1ebee6685be9bc1cd27ac66033b64cbb736bd05f31ce5eae135aa3ba2f6d6db09a23c472dfa8dd6b32de3885038c1c0f32a5eb865e8f6053c0b522f683ea60bcb486ba7372e9e97733e075b95670af18db9ed69a10433ea3f1ab58eb764d30d7681dfab74efdc21c88ab7de7e678b0fb77366123f2299617c14deea674133b478c13e6e6809b99b9f56a136d8ce6572fda81d19e8598daad5ff0696f7eaf7bb255cf9f8fda57dd5a80aee87314fb52c31e731dc6146a6837cf11d015e78eeae2dc5ad32fbdbf5d4f75c1a680f7a8b4af785da0bfebf1def57e89fdb04fdfceedb01fb2276023b2693964fd4f61a3f71000e2dcc0e0cbe6dc0d6d9c2f947e631b278d81fa445f5f2c6dd27c3f39826b9d6b12e215e7c6306a479f753c68b95fd6d08fcdded8992b8dc50fcca53af113d702d6aad8834f7b487b22e682b5b6049a2bfa1ae3388518d7d06b762cc4f8299958f381913978ef81dac41aaf107facd7cea3b31fb2276023b231d9b07d09045dbb38ef06368e396373b4339f79a53daa436ba1334e8ca1b67f1a8d4d59e514e37afd409e7dc118e7f463ae60279e3fc93617f0791f80d1c3a4b5aca70ed630df7931d67c75ed628d8ae073cdb56fd11af6694f75fdde1f640b635b035b7311ff0cc118d76d3cf80506b4eddc2cfb217b02366137a51bdb8de9e6d52ec49bd3d8d239baf3e64e1a33eb893efb8ab6d5bab66a817e63c86b9d730e47301f1b3a35b48176ebc11c8138fb57f421d6515fd581fa8817ed33de7ad5b956af17a61f41d70fd58d6fec0a6bcc7a335ebf75f53b87de1ba9be7373ec87ec09d888dda49dbb5181b1070ff8d292a78fb1341fdce873c3b72eb89609b6e9536feff6d1dff5ea5f618fc638c787f45eb42fdf1178dff44ff023ed61cdd6b51fcc9afa3a4e21c7f5f8a320ed93dad4a9eb17e10a6b24c679fb9b671c1823c6d5eea80fb0b5467dd0356fc5a9ebdfb979f6bf8c7082cf7dee73874f7ffad3975e2c37a42f122f9af3de4ee7883f1feb4b59a6ad75b463539ff957a175c05ed8aac36a1d5cffaa3fb153faa203d7efc1d61abda7de37639c032336e7eab5b76e63c518c57ce36aefe1a3dff510536a335f5ad3b9f742bb3e46fad2876ff9f18336694dedc430c7de759adb1ed6c58efeca57be72ffcb08b7c0e53760e721dcc0ddccdd9c6e5a74706e3ca3ba2f00e8531774e7e64af553d8f35c8ef571eeda7adda01d88f51e798088bec60aba620f681fe97d4407f3a0f52bd03c65d5031b625d46af4b9a67bca8b73774bebac6e6b1b6990bc454ef881ddd3938f75e01718a766beedc2cfb217b02373a9bb41bd2c3c24fa6da80b93a98df4d0edddcd69039076a6c814f716e0d46d633d7b445fb10672d453bb427b65e23737dc6b986e681f7d9bac7d66b1cf9d3ee483deb3b9f3df0f3fc1aa70fb0b707236b321e8c81c629ce8d735d085f84b419a718633fe3459b7a477256b5409b54dfb91d1eece29d256cceb951f916ce6f79ddf8bec4fed706c00d6c0dc497a6ba3160ce0ae2611563fd958fda485f66fbc1cc71de18f391daad894d3b36d65a9f3da78e80070e422e871ff9c4aa3b777d8efd42a7df7b6b2e78ffc018459bb80e757c8cae019f76605426e6388273fba3b70fe3d4b916734be7aed158e6e07af181b5f5776d3b37cb7ec89ec04d28cc3d58bb497d21616e60711363ef8656773c556b4b3f8635ce8d87ae03588b6bb48e31d87d518d5137a6b1c5030656eb6b3dfc1e9ce8f370c086d00bfb1451b706320feb625dc0873e63c07a625ce3ad03eade1be7c4cefacc5deba435bd766d8cda9c23cc7b9f766e87cb6fd1ce25ba49dd847353e2eba6f7259839c63132af2ead53a8630de7e888baf51498ba75e65c59619c6b43176cd6d0ce38e3a9610ca36b026dc27c62aeb5c46b9e588fb1b9883e6b51c33a8c7e022cabbee668676c5ce3abbb0ec04e9d49d70c1d95d53aa9476e47201e9d910f08ada31dd9b91df6df2e3802b7e6539ffad4c5bfc2e5cbe0c66413b3d1d191b9698d3d86f1d04fc7702a578c6b5fb076edea7304e7e058f0730f8c33b78704f764ce61eb2099fd917938e8d7aeafb980de673499f9605caf6be636a731f672ae18dfeb007f1c02c61163ef820f1b3e64e6819fbadd37628c3510308f9e5b314f3ffdf443f5761e9df5c7979dfbf812206c50709c1b178c750e6e68c497d31ad66d7c59d9ad5f5f757badd0ce488eb133be3d8ceb085e8bf8378c100f871983cdd13a60acf1fa8cf7939bf3e60273ef29a07b5f5b4bc136f5c642fdc688367a18d7d8c2dcfb01c681f913e3e735887673f55b5b5ff3c8b1ae31d6f113eececdb31fb247603356d8a06e48e6d08de9866554981bbb1a3d548039f1ad89cd78686ee95fa114e6ad656d6df85b4fbdfee25ab5f71acd5147381811f3b0d9c7975dbcb780ddba823e639a0ffac82dd88d57d7eedaccc1661d47d78af4bb975e833668be3aa83716e8dd79f5e6d70ef8b4ad7c507bef1fd0d7ebdf3fc1de1efb217b0237695f5c36aa8707fe6e64740f97fac8417ca1c8f5d303b1fed2b9f9cd85d641672cfe4708a7bd75e608ab78d1a7cdfa5b3dfccd0a745f6873987bcfb0f9858ab936eb00b1b2eaa9cd1c6b69b36e750f1246e6f6689de6213e2346c4fbec7511c308cd07ecc6817620d6fba1dd7ce319d9172bf0110fe4a03b076b00bac27a8478afa5ebdcb959f643f6086e5c37b19bd0cddccd89f405c16ebefadcc4d6c1de4dcea894c68b36409f2fa97d15edab79d7409ea20d980336fde0358a8793b98cc4704811c73a6175dd5b4c1f755639dac1fa73ced8f58071c636c71846c55ac2358b315e2771dada03b00935417f63670ef3d66b9c7b16a62ecd53766e9efd903d821bd38dcf069d2f161887bd9bbe1b1abb73eb6143efdc5ce75be8b3a702730ef5c9d47d1915702dae6bced59bab4d9d91b9d709c67afdc68a7e45a65d9f23ebb0b7367b3b87b91ee64875841c6a22f3d33558c339d8a76b705d82dd796ba12bcc39b8ad01c662a3a67a63d0c9458cb1a6cc1ef5eddc2c0f76d9ce43b059fd361e7133fad2296ee629ddf86e7abf45b4a6b1808f1cecbe28ea8c623c767b586f25d23a53ef88bdbdb907fd992f63af03187b5dc4e8935e2bf7d2fb46acb501bb9f0a5bc3dad2b935985b0bdd9ed8664decf3db71eb58037cee8ea5715e9b7da70ec473efe6fd04ecde576cdab91f084c1bf5b199633fc4b53ae26f9eb1da776e87fdce9ec00deae6043770e7e81e1ce6f852cf97401b58a31b5d9f87ae742d820d21ced8ff7f7bf7b663d951a551982b300810efff8c88a3019babd648f587ff0aafac7477d7ae062986343567cc53c45a1911b933ab5cde9af59fe4579fd4f7a936ffae4dee8975f4ac9e37097e766c5efdd46feefa82ce7fce11d95b93cd2f9f2e261ef9f91239a74ea271189fefa4f8f644794f17b63cfeeacd01ef29ea1dd6255f3cf8d39bb3efa09ee7da2f5f8efb663f439bd0e6b319d30e4eece6b489d90e419c7d121b7e7b4439c441d037f89ed8bcd85e5b63bc0735692d0e60c8b3ded8d8e6ba38ca4d2fc6e9f2fd84b03dd93ecd45beeda5b7dc68bc3e6b4aeb13cd29a6a777cf97d6e71ca7f71b9f1e69ef4d8d58e8b175ded57b82adade6ec13cdbdef453cce771067ce39e7e5cb72ff6384cff0cf7ffef3ed9f39b401fb43920eeaf7df7fff36eed5f91375ecebcc6ef3a7eb61c377581c8a7038f3fbf1b538d49c5f2a393bcf524f3e39587f6c6e645b5f74b99c873cce3aecc1d66773cf3aef83ecf3c8f59ef8566fafd3a73ed2e7bb2aaea6e73ce36b873879a27ede41f927d599533f633af27b6e73c9cdcfb735677de3be769ead31896fbef9e6fe13872fe4c75ffdcbbf6883daa46987c606dd8b4f5ec82fbe07a40d6e93c7f60b354bb1640f94b98c43afcd616f8cbd736f2f75f9f0d4e74942ddd65777e6a5cd8d8db183bdefe6bddc93edefddcb17d32371e1c9499feb0c7dca0f796c63f193fc4febd91e90730ad60e63fd1af735d839718e2f5f9e7bc97e86365f9b901d6dd8ec1d4763313aaadfcb492d598a6f0fc83b756c1edb7ca1df52fcbd1eeb3feb426fb1b477549fe2e6dc77978f6c8fc6ec65fd7b11a413f36ccf1d278b3abd36c73a57cb5b5fe3a8ce7ca1f78ec31c1b3b6bd8f4da6ac9ae25b6e7fe1a2336c6d6677df5d2eff21aeedbfd8036a00dd9c1eadf316843fb9da2bf6799b83c8ccbcbb73d6273e3dcf80ec3c9e9db9e21be87502fb173bc5883f8aea74f910ea478f63e47e871f63adf41b818eae1f7a549f99b6b0e3d935dc7225e4d7a7bed4554fdc9cebdb2bd36afaf7d9aff64f34ff2e9dbba62e73a35d9f7984d9a8b1fd6b6eb481a7b7eb597d7712fd90f6853b611cf1fe3fd2a40dc663e373a6cea93add103f9cf98787a7bae1feced13e7b8bc538aaf9db8a4929e9b4eb60f7b75b9de635a1f7cee5701de6fb013793b9738ad3ee8d62b461a27c1deb1e7dd7ef9f69d80bd3ebdd416cbd738d1c3bc04e534de9e6a3ccff611df3e9b1bf4e5b5fc704a2f8fd8b46d489bf7dcb8eb5f7b0f06ffc2af97831cab4fdfce0379e4695e76b1e49c6f65eb7b0e39d5a84bf34735e1b0877cb153eb9fcec7bf980ff2b607fbd4c5769dc9ce1769b1601b6faf6cef63fdfbccdb17629e85847e62e6483ff5908ff30340f1cd39f3777ef35c5ec7a76ffff2236c789b72c7ef1d82e813ced3e512eaeab3070bdb47eefa826febf0941fa7df01730965ebc757bee7e84fa03bd0fd989cd64f8dde6ae9bd04f2e9d9fc62679f62d6479bafdaf3477fa2568c463dac4bdfa0cb4f1a8b6f5e315fdb74fee49ce744dfd0739fa37112f949ef877f73d60e6b283f5d5fd4273c03ace7f25aee25fb136893b6216dfcecc4a615e3db58fed3971d8df922bbfc7080c48dcf3e3b5e7ff6cafad9e409799bfff4fcadebf49f6b1743f1441ce541afd8fe511d09b1ad176f1e313dd5196f3c8af347b1b35f6c5e72d6ad3f8178e4dfb9e3ec2b262fbd354f39b173e68fed6d7c792df792fd0c0e543a7c3ad84dba9c1bd7c60e35e7013a3779633ef36eced6837dfa834f5f713df93f873a9fba62fb24adb5e795bb873a1a27eacecbaab15c3dc29c7a16938fedad2ee42627f9e4d29bc7d6f3a96f6bb00e3eb26ced89d8be9b60a7497de9937d4f9b938d5d1bdbf8f23aee25fb1368a3ee416e63ee8fb928be9b7d2f8ec8562fe73c5cc5b606670e7dda89de04cd257fed90b7bea5b84b456e3d4eccb97daaf1be7a3fd5f5b734ac95afb17746fb7199945feefed75bfe943ff49497843ec593f3525ad491ea965dc7d66ebf64d7922c8d3d53ecdace18bf9cd8fccd0df3b53e71ef3ff4a02fafe55eb29fc1663d37ac18df6eda36f3e66d8cae76c7c4a15dd9bc583f5fac3f1d1b8f1defa144f33fad413ff6e6a5b7ef39a7dcd8c35e9e1e7b49ca3717b29fe6b106a853abce45ce9faff524d95ba3b71c316b4b8ac95bb69666eb936cedda513ccc0debdfdef01c4976b9a126d2a4bced75791df792fd0c366b9bb1438afc1f514db536bbcdec30a4cf3e0e001c843d284f8782ef9c23d2643197bee972d24f07538c88e9cb174f79cbaea758f3f16d6ee38d85f83e63e873ae3d9ebe76ea57c04eefd7c87873a37173edbc4fb6bac69e6bb1f6fc1b5bdfce7fe6659bab3cff89f6fa57eb7b792df792fd0c6dc036eb6edc7c0e48e370b8f339d062215e9f74f86495b6e1ab319f5c0725f2410e315f6b48f851affc21beb6587dfc681df9136b13cf479b4b0dfd8479b66f6bebdf89e85df06f5e716b3467f02791bf7e8db726b2ebefeb136a372f1ad3be4edbc73cc19ff6ded2093ff4cd97f4b5ddb9d3729ad35af77dcadde72bcebf6cbf7e5a88c6ea9e6a2e5f9ef74fc3e55f1bd1c185cdbb3eec81b0f9e9cdcfd741397f9f980f8d8babd76305c6d5ebb1f190936cbfb4f86a7e79b1873bcdef79cc6dcd905b7c6be5a9d76fede2c9f657bbf3ac4d6f3fbe2eaffcdb9fcd9fd4cfc5598e7aeb50c35ebf98ba95289eadce583cedf9203ff69bcd479cdf5cb6eea7f6b8fcefb997ec676803fefce73fffe44039a036bf586339d5113e3ab2cf1abed88375e250e8cfde35f27f847ca8b396647b7bf6b3ff392e3ff29f3dd9f9f5dcbcf750fb44b51fbdb3738e1debbd79c9be536c7ceb4e9e6a836f63675ee3e4a9afb9c1e6373ef7c3f6e4bf7c1dee3f75f801fff8c73f7ef6b7bffded5fffbc61ec664e9f978a984d2d879fee62c8bf17849ae51c473eb94f35913ff9dc0514f2a2dcecd665cdf9fad176d79b0ef356d3a72bbde415970b73e8bd94eb539ade9b5f9c9db0b1e3b4b57699eb9334c7f68271921d6a43ae3e72373fea6dbed898fc72d27cdb6f6bad339ffcc88fe2f2d7d66beb8af51352f2ab5ffdeabfbd9757712fd90fe87785df7efbedcfbefbeebbb7b1c3b6ec61b5a113af36df1ed490abeefc32e40b7e639cf1f7d8f8f630efc2e7f03726676d0271127b8114f7ac72b7c67b39f38df7bde9c1d6637d108bfc679fb36ec7b16b5e3be86a5c76e65aca5bffe6876f4c276a9e7a566f3d919d4fdff4aeb37852afa4bdd87fbdd74f69fd5bb297d7f2e31be3f2096dce93366a9bd706de0dbd07a8b1dcf3b06caef80ad8e5ac76a8d64f761c4f3d769ec67aa577fe65ebc3e13d7f3fb8cf9514d3dfd87b4b42fd397763799bbfbc1733673dcebe8bf80a767cead09f7d0a7f6c9d3fd02c967f63d95bdfd83ef46e435db25f03be50a7579c3f295c5ecbfd24fb017dd7ffe31ffff8f649b6cd99ec465fdb868edddcd90e3c7ffa699397937fbf2c0ec47b5faaa7d8fab243efb0eec6fb0cefa1971e9e27e959f619f9c23ae872c4d0b8f72a0f7cc1df982f367fe78aec7aa7f7d70551cee6f32f72ac79df933a9cf5c6f4e69a4fef74fdd39b578eb9ad9d6ff7e1b927a3e7951be71fb05673ff8f085f874f77fbe547b421dbac6dca6c9bd606b6f13b04b19b3d7b6bf40afaa41c9a2c4fbe78f23bb84f736dae432cff1c2f67bfecf56577a0235fe23d443df98d63ff4e27edfdaac977caa267756ae9a0cf1efcd037ff591bc5bd9ba7fac67ac8598a7b6e7df2b5d62e47eb0fb5e6d0cf1c6ba3b1fdba3958ffe5f5dc4bf603da887b50718ecb7370a27863b5f2cbcbb6c9d70fbea79a133eb9219f6f2f049c633cf9e2ec4f273d23dbb839770de97d3f27ea16b5faee78ed8f9093b686ea937c670fbd832dff29d685f6f46c7a9f7364f7ac846f695ccf24bbb968be95f710dfb5d39fabbb7c39ee25fb136853ee81083e7ad98dedf0d1bbc1d9c14eab4f96625b634dc9d90f6aceda279f3562e3febe6ee473d0697eb677a23e6d9deac2187d9283defba3af7ef9f98cf9d2bbbe7cc65b5fadf9f28b057bf3d557e31327fb44bf64e7dd7ad467ffc38484df7b0ae333476f14f39301f26d2db9bc9e4f4fd5e5119f54dacc36b44d4b1c3879b1b9d9bbb11db2f26373a2b8fa3d904bb57a96ab5e1dc44eca357f38b0725727c5c31ae97d06e4b3beadcdced738d9cb05e65b7f7a7f65231e9bbf73c6e6aec4f656c7778e439e75649ff3c559f714abee695cae77137ded3d43a449a88b9dd3ef60b7b79e61dd97d7f3c357e8f2489bb90d996e93fa8490dd01b091e3dcf8f91d907343e723786fd36fbfa7ba68fc34c7b271f61ebcd09b5f0fbed3dfdad61f7b0965fb2615ea8a278df52ec7bad4435d6c4e545bdc38b6ef09bf9e728deb9318c7c6c3339eb2f06d9d675c093ae49b536cc77282ff647b666fddbedbcb6bb97fbbe0033a8c7ff8c31fdefe83845ffce2176f3e176d1bd5a1d9d7684c7720cf4fae28063dca69deeaa197be4bbe6ad2d559d3e728ee02da79c33c27fa577b7e02f5c949cfecedb3cf5d8e5a796c6b4fa258bdb757ec3cdb3b36b798dc33cf1c71d6185b4b3d9eea3d4b6cbcd8fe042267595ff98dcffd60dedec1ce95def9e4569f6d6d505b4dd27f84d0df93bdbc9e7bc97e4007e5f7bffffddbbf7feab2b489d32e97e51cc7fad4a6dbf0be04eb37ee70ec78e7e6035f6cce4794a7af3ae33da8a127bdecb36c7cd7a8e75e10e1395d32d9ea5c1c89bceaf5a18b17dbb9df43ef459db9eb6b7c622e398bfa72b6e7b92e3de0d956cae9d9b2c5430ce7986d8e33f6eb5ffffaedaf6fe977791d9feef4cb8f6843b6c91d669b7237ed525e88a749a84fcbcd5e3fa966fd78eacd179b7bb2f93b476bc9f64d24e979d73ed9798a6f7e82f2e4ee3b0c795da4def1d686717a7b9d3aca397b6c0d8acb353efbd0727a37de4f988784fc273fd8befed87139f5b1a6ad0fe3339e5ebbf8e68af35d5ecfbd643fc0866cc3139bd5c594ef44ccc1f1094d3f0276da3cc6e6db79f8914dcadb3a719a14db839ddd27f33469dd2e40a8df756edd19ef570bf28a67ef4f04a1468f24ac3fc4d1b8f8f64bbcebe2bb767df3479a1de6d213c6e9bd64438fed1367fdd6e82347ad1cdabacb4b3c9771797ab0772de97cfb6eeab9efe7f27aeeaf0b3ea05f13f43bd9fe0d837d557b606d568722e416cbde9cc61b57b73e9a2f1aef41da989e5b7be604bf3e670f64cb89ec2776cee859e4ea9f78ee9372d4cb4fbb08a2f17b7d177df480fcb3ae71a2f73e6f649bd3ef851b9f6bd93afdcb312eb6b5d91b7b227f72ce05b544cfec733dc1cfeecf177ef9cb5fbe8d2fafe5875be1f2489bb48deed080df66de78319bbb8d9d4d2ff2f89f0ec692ffcca163d792bdf971d62e4fb9f91ccc10ef93519c7de4f3ebb917e6f68bedb1eb539bf43c9bf714df39c421f7f4e70bf127d6ff9ebde4175b3bf6393696efdc1fd9043bdebeeba7f53be7c7f975b8bc8e7bc9fe04f640b43993c6366db64d6b6327eca7cdbdfef2f40d97181de5c3418aed1d9bb76b406362be9093ae67ba3f18f16b802e31b1fc7a447d8cf7c7e074637f1321eab36baea7dec6cd89c66a17f39933ad47fd1befba5a4371620de2deb51e913ff255df4f359e893fcef9c23c248a659b5bac71220fd695d637768ed8e7843962fbfa66b46bbdbc964f4fe8e547b441db908903e1e2e900ec667d4f47b524de8bd5cf01d95cf3071dbb36fe5d536417a343fee6c58eeb0376f1ecd6b8eb4c1a77017790cdb57951ad5ef9cb73694535c5ad2d3b4df8d5f26d5ff53bb7fca0e54571bd837f9113e623f9b73e5fcfe5d9762d72e2c9de35984ffdce43e42cfc4fbdf05477f9f2fcb0fb2fefd2e6dec3746ef8fcb19bb6cded13da1e84ddf4fcea8de52ef2d5c7392ea7759de8f7d4db58afc47a167ef36d9fecfcb4f7b56c6d6ccd992b1669828d67e3292ff1b5e28bf2b2690231f629b1f3a5cdd3f390fc9eafd859a357dad72e5bee479c793fa5267e6adee5ffcefd83af0fe80fbcfefce73fbffd1f123a2c7d524bf6d3579c07632fbb7c7e4c8b7ef42c9e14dbdae6c8766043ce89b9d4465adfd8981e69f3447179ebcfdee7682cf7fc149f4f0f760279e9ed79da5b138df70f9d96b367b9d9c9f6e1c73947f0c935d7e69ef387bee5c9dd35907d7772c0578efced0b317639d5f8c9e1443e29c7d7adf7f5bbdffdeebf332fafe4871d7e79a40de9c7be0e7b9bd5c6ce8eb443ce176dea440d392f137962c9fa36872ca73fadfffa56d6b7f6ae2dcdbf79ada958b9bd13bf3e497a2fd6bf181743b643bf97b65cebe0c7e6447972d71f1bdb9c537c8d9345fc846ffbc7fab7d6fb917b8ed33bc6cebdb1fc8dcf0b965d8c44efd05fa56b9ecbd7e3beed0fb031dbbc0e46ec66de8369f3630f007693cbd5c35cf4f662a7f5a5f339a4eba3c5f84ecc471a434fbeec7cbed1e8edb9ca3be711ab36c4f39b7be784fc5073f68ef2accb5cf1b95cf6d2d83a76be33efa9afb9b76e694cb0f90bdf532c3cc3ae2b7b9f7d7308eabbe3cbebf8e12b72f9116d427fa26cb33b145d8a0e639fe422ff6ef2ec447dfd7673a7d9f9dffba41c6bab4bf85b8f311fbdb9e9fa9f9fd8b6ae9c288fedb913b63ee9d879427d9aed936e39d5a6fd61197f1aeab78f674dcaf7fe235fe42fb76f92be519acf3c728bf18b419eb89a60af4ece7e09f457238ffd14a3eb6b8e9ec5bb3767a413efc8b87a7baa71faf275b8bf93fd0c6dd4fe43842eda36659bb4d7e595b5593be0c63670e372d1b818796f93f3899befcc355fda013d7b3ee5e0a967e4abcf526ea2bfbe8d1d7cfec6e2f9f8f9d2de8bb9cebc6cef548cf4f5389f258a45357a1ab3d33bce5ef2cb41e3fae5f36c9e7b39ebe4970bfdd928572c7b25cc4b43beda50c727471d2df6dbdffef64d5f5ecb8f77ece55f7448920eb74f01e100396cfc7ba89673231ba7f700ec2108e36037477102f6ea8d473ddef3d3c57a2e39c6ec68fce4039fbe8d8939421ffe489f7591ef299726c5e5419f27df699f3d2147deb2b99b27972d8ff4b5a4f15413e97dc72107c62bd83e97afcba75fb5cb27b4a93b00df7ffffd9becdf0a783ac8fc3ead393cf9a20d9ea85b3b1a57d33cfaf18badde18b2ab7d4fc4d56d2df8e4355f6be157e7f9f42e271d6c63b5fb0c3d677639bd33bf36801ff3d5867cfe1d47b6bef5da77b86b516f9d7ce2f9886fb0ec7dce283ff2b3f5505b2c7bd714faae2d7ff3eab7ebe26bcd7e359044bea4bcad8b535f5ecffd75c16768a3f67faafdfbdffffe76386cd87e9ced00b45177d3daccb4439b5d3e3f5fe8e950e0b4b777e817b4359ef9a72ff10d23f2d3e7c1a61d6478b6a5beb179d699983bdb3cad79dfd34a94ebb98ce9b3d7891e515e522f635a5e7d76bdefad1dfc6cf0a7d7967bd6d021d63bd9da45ce92cf7bb4ee9ed5d7ad3daba777f09bdffce6b1d7e5cb722fd90ff8cb5ffef276c9864f486de2366a3a1c84f48a8b2cdbc647be50976683bdbe0e4abd12874a5feb89edbf3a7fb2976ce37a253d975a87b27187b59a30b7786379a47c342e6e0d7bd175f88bd7cb7ad2f982565b1ef42ddf1adea358f9cdbd73a92996e4f75cfa8b65af1fe738d647afef8c37673ec2171b5363fdc85f1c6c3d7acfbe266afb3765cf3e972fcfbd643fa03ff4faeb5ffffaa661d32f7b28fab1d7b8bc367263974bbe62f9fc214fec8657b776791d94cdd3275f79d9f93607c5a39c2eccc6d664edf988bc282f3b51e35392dcfce9c8575ccc3aa33c6bcce77d958be28d8b4576f97d1d760e3dd4aa0b3acaf34eb67eebaca975effae46c5d3496b3145fffce93ce9fb0c3bae52dc5760ecfb16c1c9b777ead7aaffddf119e7a5dbe2cf792fd8036f8b7df7efbf65f7cc166b56189711b37bb5af0bb54c5f670e50ff5ebdfcbb078be62fa6c4e072ab65eefc827c7c5e83275a9c9a75b77fff55b979cff6d493df6909a2facf3c4613fd9672ea7f92056cf7d7efec5b8f85efa51ac312d76da518e6f0649eb49172f966dae58fff608ebd533db58aeafdd2267f356d7970d35e1ebd39a826eaebecedf7cf3cde3bc972fcbbd643fa0d7735eb2e7a7c0a58decf0dbd4d186ef7272e08ae58bf58583983f09e3b443b4f027dbb7b1dee2c67ac65eb2e5588bbee5b964bb701acb294eb3c5ac81bf7eb1636bc8575dd23b686cfe65fdded549b1c44553ce99d7b8b9767e7d09d6d6c733eabdefe43d5a6f397a845af35b6f585be4e32f576cfdecc49ab3ad8dafbd782fd9afc3bd647f02fdbaa0c3fadd77dfbd6d5407a90bc96510bdcaf21afb24966ffdd5b4e11b9faf3e5f144fd4368743e2a0b85cb2e5651beb2376e6f846514e74796677f8f247eb69beea8af5372cca6bee9eaf1e7a667bb628377ff5f9b2f5ad3639df5db6b9cc5b8e6f00f2f3cb83fedeabbcfa44feea43fcac075b8ff00ca1ded78b0e39cda536aa6f9cee79ac2bf2cbdd9a9d0f7c6976e8517fefaeaf25db7e6cdc7a8bf58f767b2797d7712fd99f409bf24f7ffad3db66b549b3dbe46dde6c87aa5839c55c3afc91ed136d3ebd4e8ad7a358367139268d8b277c61fe44efec6ab7afcb2bc45adb5e46496b54dfe5595ecfd63c8d3d7bbabc2866add640e7ab3ed424f58df29ec68bf7598ee795d738ce1afdd21b539726fc513feb2daffacd2bfe5417c5ca4fbcd728c79c9e05d619db577e58c33e47e4d7aff97c0df4ac267fd2ff1d61e7babc867bc9fe0f68c3f649ae1f9bfb1542174c7f78d001f41a1db8741bb94bb88ddc38e900eca7b8c6f2915dcc05ee903840f91c8e3d646c35c9da0e9fbcc6cd554ecf163d8b6f02d6543c29bfe74f5cc6d9f25aafb9f2153357587bbe6a3d433a7fbe7494b3cf9f6427d60c736c4e3d9fde51f03fb1cf5c5de3443fb271f9bb2e5a6da47dddd5a74f29cf9ae52cdb13d692df3ab2d38d9b972eb70bd6378ecb6bb997ecff81366d174997c71e867c5d5a2e952e2487c001689c3fca938b6c87ad1afd3b2461ced8cb6875f5e7618ccd497cf3f0a9c701347fa2aebcfe5a5bb17edc2cd6379bc8b707b76f46c55b7fb1ea7df22d2fffd33bd86f42611dc5c33bcc1f72f3f1e73bf3f4cb273fcd1ff9c23b8f7c8dc3fb2fa62f3c6f7ef9d5ea995fcdd39aaab1667ef3c9d777e7803abaf8e6794ff777b15f977bc97e217a8d2b6decbd381207a098719b3d3bca2d96df85e2d3a183a20e8dbb1c1d203ad29b9b6d2e87567dfdd35d145d7ce62d2f519b94d345db5c1dd862d6576eb569cf2f262fb1ce9d235a033696dddcf5cb5ec4eb9b5ef1bc3b27df536ebdcaddba1d07bb7ee5a7c37bac57fe68bdfae66f9c2d1ee68f72bd03f39a233147765accbc493534bbbcecbeb6f713ecd7e55eb22fc6eb75603a641d94ecc4c108be721d9a0ec7195793347628b33b700ef0e66c4d22077aebe550b263ebfb44ebf7b38d7b2e17606b4847be7d9e347fb5448d7793ad473158a7b8be67cdda9e83cff3e865fe7d77a1e7b23d493e76cfb53ecf1e6c31f3a6f36d1fb9f2cbb1b67cd66b2ef312bd8ba57de3bb7c7dee257bf95fb3879a4497807197669f7cbb04121743740194bb9f9aa39ac6918f1d8dab811e906bfe7a376714335752afb3ffabf1ecc1b6d638d74f7a466b76799eebd6239ee297ff1fee257b7909b65597439f7abb681397858be3bc34ce8ba2b1cb427eb217b69ef2d849396a56fe13f02ed2ecd6ee392fff19dc4bf6f272ba04bb14bb6c93fdd1d66518c6d16522e7b4cbd9dca5b9ba58fb5df1fd11f9f2efc0bd642f5f8d2e4062dbadaf4bd305bce44fe490f2fc2d88e872f5a9f872f977e15eb297ff37ce8b76b76276e2720d7949fe3ea976c9fa3debe5f2efc8bd642fff96b42d5da8c65da47d6af52b83cbe53f817bc95e2e97cbcbf8d9cffe0b7c60da7af46c7f480000000049454e44ae426082, 1, 220.00);

INSERT INTO `order_products` 
(`id`, `order_id`, `product_id`, `type_id`, `color_id`, `size_id`, `brand_id`, `series_id`, `module`, `pcode`, `title`, `type_title`, `color_title`, `size_title`, `color_hex`, `brand_title`, `series_title`, `qty`, `price`) 
VALUES 
(NULL, '1', '1', '1', '1', '1', '1', '1', 'catalog', '123445', 'Мужская футболка Панда', 'Мужская футболка', 'белый', 'M', 'FFFFFF', 'Sols', '', '1', '220'),
(NULL, '1', '2', '2', '1', '1', '1', '1', 'prints', '23456', 'Женская футболка Панда', 'Женская футболка', 'белый', 'M', 'FFFFFF', 'Sols', '', '1', '220');

DROP TABLE IF EXISTS `order_files`;
CREATE TABLE IF NOT EXISTS `order_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_order` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `order_files` (`id`, `order_id`, `filename`) VALUES
(1, 1, 'panda.jpg'),
(2, 1, 'panda2.jpg');
-- -----------------------------------------------------------------------------
-- 16.05.2018
-- удаление placement из изображений типа и связанного с ним кода
-- -----------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `product_types_images_bi`;
DROP TRIGGER IF EXISTS `product_types_images_bu`;

ALTER TABLE `product_types_images` DROP `placement`;
-- -----------------------------------------------------------------------------
-- 30.05.2018
-- добавление индикатора дублирования категории в верхнем меню 
-- -----------------------------------------------------------------------------
ALTER TABLE `ru_main` ADD `show_on_top` TINYINT(1) unsigned NOT NULL DEFAULT '0' AFTER `menutype`;
-- -----------------------------------------------------------------------------
-- 31.05.2018
-- добавление типа фильтров Тип товара
-- -----------------------------------------------------------------------------
INSERT INTO `ru_filter_types` (`id`, `title`, `type`, `colname`, `active`) VALUES ('6', 'Тип товара', 'int', 'type_id', '1');
INSERT INTO `ru_filters` (`id`, `tid`, `aid`, `title`, `order`, `created`, `modified`) VALUES (NULL, '6', '0', 'Товары под нанесение', '7', '2018-05-31 00:00:00', CURRENT_TIMESTAMP);
-- 31.05.2018
-- запроспереименования колонки scale на width в типах товара
-- -----------------------------------------------------------------------------
UPDATE `ru_product_types` SET `dimensions` = REPLACE(`dimensions`, 'scale', 'width');
-- -----------------------------------------------------------------------------
-- 04.06.2018 user_7
-- добавил сортировку типов фильтров
-- -----------------------------------------------------------------------------
ALTER TABLE `ru_filter_types` 
    ADD `order` INT(3) NOT NULL DEFAULT '0' AFTER `active`, 
    ADD INDEX `idx_order` (`order`);
UPDATE `ru_filter_types` SET `order` = IF(`id` = 6, '1', `id`+1);
-- -----------------------------------------------------------------------------
-- 06.06.2018 user_7
-- добавление в настройки параметра округления цены и установка в обязательные
INSERT INTO `ru_settings` (`name`, `value`, `require`) VALUES ('pricePrecision', '0', 1);
UPDATE `ru_settings` SET `require`=1 WHERE `name`='eurRate' LIMIT 1;
-- -----------------------------------------------------------------------------
-- 12.06.2018 user_7
-- добавление сеопути для ассортимента
ALTER TABLE `print_assortment` 
  ADD `seo_path` VARCHAR(255) NOT NULL DEFAULT '' AFTER `color_id`,
  ADD KEY `idx_seo_path` (`seo_path`);
UPDATE `print_assortment` a 
    INNER JOIN `ru_product_types` t ON t.`id`=a.`type_id`
    INNER JOIN `ru_prints` p ON p.`id`=a.`print_id`
    INNER JOIN `ru_printfiles` f ON f.`id`=a.`file_id`
    LEFT JOIN `print_assortment_colors` c ON c.`assortment_id`=a.`id` AND c.`color_id`=a.`color_id`
SET a.`seo_path` = CONCAT(t.`seo_path`, '-', p.`seo_path`, IF(c.`id` IS NOT NULL, '', CONCAT('_', f.`order`)));
-- добавление сеопути для ассортимента
UPDATE `modules_params` SET `seotable` = 'PRINT_ASSORTMENT_TABLE' WHERE `module` = 'prints';
-- -----------------------------------------------------------------------------


-- -----------------------------------------------------------------------------
-- 13.06.2018 user_7 добавлены записи из базы от Саши за 2018-04-21_12-40-32
-- достпуы
UPDATE `users_access` SET `modules`=CONCAT(`modules`, ',colors') WHERE CONCAT(',', `modules`, ',') NOT LIKE '%,colors,%' AND `uid`=0 AND `gid`=1;
UPDATE `users_access` SET `modules`=CONCAT(`modules`, ',product_types') WHERE CONCAT(',', `modules`, ',') NOT LIKE '%,product_types,%' AND `uid`=0 AND `gid`=1;
UPDATE `users_access` SET `modules`=CONCAT(`modules`, ',sizes') WHERE CONCAT(',', `modules`, ',') NOT LIKE '%,sizes,%' AND `uid`=0 AND `gid`=1;
UPDATE `users_access` SET `modules`=CONCAT(`modules`, ',size_grids') WHERE CONCAT(',', `modules`, ',') NOT LIKE '%,size_grids,%' AND `uid`=0 AND `gid`=1;

-- картинки
-- DELETE FROM `images_params` WHERE `module` IN ('colors','product_types','sizes','size_grids');
DELETE FROM `images_params` WHERE `module` = 'brands' AND title='';

-- настройки модулей
DELETE FROM `modules_params` WHERE `module` IN ('colors','product_types','sizes','size_grids');
INSERT INTO `modules_params` (`module`, `title`, `short_title`, `seotable`, `seogroup`, `images`, `access`, `history`, `menu`, `order`) VALUES
('colors','Цвета','Цвета','COLORS_TABLE',1,0,1,1,0,26),
('product_types','Типы товаров','Типы товаров','PRODUCT_TYPES_TABLE',1,0,1,1,1,27),
('sizes','Размеры','Размеры','',0,0,1,1,0,28),
('size_grids','Таблицы размеров','Таблицы размеров','',0,0,1,1,1,29);

-- обновление мейн
DELETE FROM `ru_main` WHERE `module` IN ('request','subscribe','callback') AND `id` NOT IN (5,6,7);
UPDATE `ru_main` SET `title`='Запрос на просчет', `menutype`=0,`module`='request',`seo_path`='request' WHERE `id`=5;
UPDATE `ru_main` SET `title`='Подписка на новости', `menutype`=0,`module`='subscribe',`seo_path`='subscribe' WHERE `id`=6;
UPDATE `ru_main` SET `title`='Заказ обратного звонка', `menutype`=0,`module`='callback',`seo_path`='callback' WHERE `id`=7;

-- -- необязательные тестовые данные
-- DELETE FROM `ru_size_grids` WHERE `id` IN (1);
-- INSERT INTO `ru_size_grids` (`id`, `title`, `descr`) VALUES (1, 'Таблица размеров мужская футболка', '&lt;table&gt;\r\n&lt;thead&gt;\r\n&lt;tr&gt;&lt;th colspan=&quot;5&quot;&gt;Таблица размеров женская футболка&lt;/th&gt;&lt;/tr&gt;\r\n&lt;/thead&gt;\r\n&lt;tbody&gt;\r\n&lt;tr&gt;\r\n&lt;td class=&quot;image&quot; rowspan=&quot;9&quot;&gt;&lt;img src=&quot;/images/tmp/table-size.png&quot; alt=&quot;&quot; /&gt;&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr class=&quot;thin&quot;&gt;&lt;th&gt;Размер:&lt;/th&gt;&lt;th&gt;Ширина А *&lt;/th&gt;&lt;th&gt;Высота В *&lt;/th&gt;&lt;th&gt;Вес, кг **&lt;/th&gt;&lt;/tr&gt;\r\n&lt;tr class=&quot;thin&quot;&gt;\r\n&lt;td&gt;XS&lt;/td&gt;\r\n&lt;td&gt;41,5&lt;/td&gt;\r\n&lt;td&gt;62&lt;/td&gt;\r\n&lt;td&gt;53 - 59&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr class=&quot;thin&quot;&gt;\r\n&lt;td&gt;S&lt;/td&gt;\r\n&lt;td&gt;42&lt;/td&gt;\r\n&lt;td&gt;65&lt;/td&gt;\r\n&lt;td&gt;53 - 59&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr class=&quot;thin&quot;&gt;\r\n&lt;td&gt;M&lt;/td&gt;\r\n&lt;td&gt;42&lt;/td&gt;\r\n&lt;td&gt;65&lt;/td&gt;\r\n&lt;td&gt;53 - 59&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr class=&quot;thin&quot;&gt;\r\n&lt;td&gt;L&lt;/td&gt;\r\n&lt;td&gt;42&lt;/td&gt;\r\n&lt;td&gt;65&lt;/td&gt;\r\n&lt;td&gt;53 - 59&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr class=&quot;thin&quot;&gt;\r\n&lt;td&gt;XL&lt;/td&gt;\r\n&lt;td&gt;42&lt;/td&gt;\r\n&lt;td&gt;65&lt;/td&gt;\r\n&lt;td&gt;53 - 59&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr class=&quot;thin&quot;&gt;\r\n&lt;td&gt;XXL&lt;/td&gt;\r\n&lt;td&gt;42&lt;/td&gt;\r\n&lt;td&gt;65&lt;/td&gt;\r\n&lt;td&gt;53 - 59&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;tr class=&quot;thin&quot;&gt;\r\n&lt;td&gt;Tol +/-&lt;/td&gt;\r\n&lt;td&gt;42&lt;/td&gt;\r\n&lt;td&gt;65&lt;/td&gt;\r\n&lt;td&gt;Рост 160-170&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;/tbody&gt;\r\n&lt;tfoot&gt;\r\n&lt;tr&gt;\r\n&lt;td colspan=&quot;5&quot;&gt;&lt;span&gt;* &amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;измеряется поперек изделия на 1 см ниже проема рукава&lt;br /&gt;&lt;/span&gt; &lt;span&gt;** &amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;измеряется от самой высокой точки на плече до нижнего края изделия&lt;br /&gt;&lt;/span&gt; &lt;span&gt;*** &amp;nbsp;&amp;nbsp;измеряется при среднем росте женщины&lt;br /&gt;&lt;/span&gt;&lt;/td&gt;\r\n&lt;/tr&gt;\r\n&lt;/tfoot&gt;\r\n&lt;/table&gt;');
-- DELETE FROM `ru_sizes` WHERE `id` IN (1,2,3,4,5,6,7);
-- INSERT INTO `ru_sizes` VALUES (1,'XXS',1),(2,'XS',2),(3,'S',3),(4,'M',4),(5,'L',5),(6,'XL',6),(7,'XXL',7);
-- -----------------------------------------------------------------------------

-- 19.06.2018 user_7
-- обновление сеопути для ассортимента
UPDATE `print_assortment` a SET a.`seo_path` = '';
UPDATE `print_assortment` a 
    INNER JOIN `ru_product_types` t ON t.`id`=a.`type_id`
    INNER JOIN `ru_prints` p ON p.`id`=a.`print_id`
SET a.`seo_path` = CONCAT(t.`seo_path`, '-', p.`seo_path`)
WHERE a.`color_id` > 0;
-- -----------------------------------------------------------------------------
-- -----------------------------------------------------------------------------
-- -----------------------------------------------------------------------------
-- 20.06.2018 user_5
-- добавление сортировки для типов товаров
ALTER TABLE `ru_product_types` ADD `size_grid_id` INT(11) unsigned NOT NULL DEFAULT '0' AFTER `price`;
ALTER TABLE `ru_product_types` ADD `order` INT(11) unsigned NOT NULL DEFAULT '1';

-- 21.06.2018 user_3
-- добавление в слайдер еще одного изображения для адаптивной версии
ALTER TABLE `ru_homeslider` ADD `image_adaptive` VARCHAR(100) NULL DEFAULT NULL AFTER `image`;
-- добавление в настройки настройки нового изображения в слайдере
INSERT INTO `images_params` 
(`id`, `module`, `aliases`, `max_width`, `max_height`, `crop_width`, `crop_height`, `crop_color`, `title`, `column`, `ptable`, `ftable`) 
VALUES 
(NULL, 'homeslider', '', '', '', '', '', '', 'Изображение (адаптив)', 'image_adaptive', 'HOMESLIDER_TABLE', '');