-- переопределение дефолтного цвета в ассортиментах
-- превью выборки новых дефолтных для 18го дефолтного
SELECT * FROM  print_assortment a 
LEFT JOIN (
    SELECT IFNULL(color_id, 0) color_id , assortment_id 
    FROM print_assortment_colors 
    WHERE color_id<>18 AND active=1
    GROUP by assortment_id 
) t ON t.assortment_id = a.id 
WHERE a.color_id=18;
-- обновление
UPDATE print_assortment a 
LEFT JOIN (
    SELECT IFNULL(color_id, 0) color_id , assortment_id 
    FROM print_assortment_colors 
    WHERE color_id<>18 AND active=1
    GROUP by assortment_id 
) t ON t.assortment_id = a.id
SET a.color_id=t.color_id 
WHERE a.color_id=18 AND t.color_id>0;
-- отключение если нет чего поставить
UPDATE print_assortment a 
LEFT JOIN (
    SELECT IFNULL(color_id, 0) color_id , assortment_id 
    FROM print_assortment_colors 
    WHERE color_id<>18 AND active=1
    GROUP by assortment_id 
) t ON t.assortment_id = a.id
SET a.active=0 
WHERE a.color_id=18 AND t.color_id=0;
-- превью переназначсения дефолтного в цветах ассортиментов
SELECT * FROM print_assortment_colors p 
LEFT JOIN print_assortment a ON a.id=p.assortment_id 
WHERE p.assortment_id IN (SELECT assortment_id FROM print_assortment_colors WHERE color_id=18 AND isdefault=1) 
  AND p.color_id=a.color_id;
-- переназначение дефолтного в цветах ассортиментов
UPDATE print_assortment_colors p 
LEFT JOIN print_assortment a ON a.id=p.assortment_id 
SET p.isdefault = 1 
WHERE p.color_id=a.color_id;
-- отключаем ненужный цвет из дефолтных
UPDATE print_assortment_colors SET isdefault=0 WHERE color_id=18 AND isdefault=1;
-- отключаем из активных
UPDATE print_assortment_colors SET active=0 WHERE color_id=18;
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 13.09.2018 user_5
-- добавление склонений значений атрибутов
ALTER TABLE `ru_attributes_values` ADD `title_single` varchar(255) NOT NULL DEFAULT '' AFTER `title`;
ALTER TABLE `ru_attributes_values` ADD `title_multi` varchar(255) NOT NULL DEFAULT '' AFTER `title_single`;
-- добавление H1 в шаблоны метаданных таблицы main
ALTER TABLE `ru_main` ADD `filter_title` text NOT NULL DEFAULT '' AFTER `seo_text`;
-- добавление метаданных серии
ALTER TABLE `ru_series` ADD `seo_title` varchar(255) NOT NULL DEFAULT '' AFTER `title`;
ALTER TABLE `ru_series` ADD `meta_descr` varchar(255) NOT NULL DEFAULT '' AFTER `seo_title`;
ALTER TABLE `ru_series` ADD `meta_key` varchar(255) NOT NULL DEFAULT '' AFTER `meta_descr`;
ALTER TABLE `ru_series` ADD `seo_text` text NOT NULL DEFAULT '' AFTER `meta_key`;

-- -----------------------------------------------------------------------------
-- 13.09.2018 user_5
-- добавление полей в таблицу заказов ------------------------------------------
ALTER TABLE `orders` ADD `recepient` varchar(255) NOT NULL DEFAULT '' AFTER `name`;
ALTER TABLE `orders` ADD `city` varchar(255) NOT NULL DEFAULT '' AFTER `recepient`;
ALTER TABLE `orders` ADD `address` text NOT NULL DEFAULT '' AFTER `city`;
-- города Новой почты ----------------------------------------------------------
CREATE TABLE `np_city` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `CityId` int(11) unsigned DEFAULT '0' COMMENT 'ID города в справочнике Новой почты',
  `Description` varchar(50) DEFAULT NULL COMMENT 'Название города (Укр)',
  `DescriptionRu` varchar(50) DEFAULT NULL COMMENT 'Название города (Рус)',
  `Ref` varchar(255) DEFAULT NULL COMMENT 'Hash код города',
  `SettlementType` varchar(255) DEFAULT NULL COMMENT 'Тип населенного пункта (Hash код)',
  `SettlementTypeDescription` varchar(255) DEFAULT NULL COMMENT 'Описание типа населенног пункта (Укр)',
  `SettlementTypeDescriptionRu` varchar(255) DEFAULT NULL COMMENT 'Описание типа населенног пункта (Рус)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_city_id` (`CityId`),
  KEY `idx_ref` (`Ref`),
  KEY `idx_descr` (`Description`,`DescriptionRu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Города Новой почты';
-- отделения Новой почты -------------------------------------------------------
CREATE TABLE `np_warehouse` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `SiteKey` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Код отделения',
  `Ref` varchar(36) DEFAULT NULL COMMENT 'Идентификатор адреса',
  `Description` varchar(99) DEFAULT NULL COMMENT 'Название отделения на Украинском',
  `DescriptionRu` varchar(99) DEFAULT NULL COMMENT 'Название отделения на русском',
  `Number` int(11) unsigned DEFAULT '0' COMMENT 'Номер отделения',
  `CityRef` varchar(36) DEFAULT NULL COMMENT 'Идентификатор населенного пункта',
  `CityDescription` varchar(50) DEFAULT NULL COMMENT 'Название населенного пункта на Украинском',
  `CityDescriptionRu` varchar(80) DEFAULT NULL COMMENT 'Название населенного пункта на русском',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_key` (`SiteKey`),
  KEY `idx_descr` (`Description`,`DescriptionRu`,`CityDescription`,`CityDescriptionRu`),
  KEY `idx_ref` (`Ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='отделения Новой почты';


-- -----------------------------------------------------------------------------
-- 28.08.2018 user_3
-- реструктуризация базы 

-- заказы
-- пользователь
ALTER TABLE `orders` ADD `user_id` INT(11) NOT NULL DEFAULT '0' AFTER `id`;
-- менеджер
ALTER TABLE `orders` ADD `manager_id` INT(11) NOT NULL DEFAULT '0' AFTER `user_id`;
-- статус
ALTER TABLE `orders` ADD `status_id` INT(11) NOT NULL DEFAULT '1' AFTER `manager_id`;
-- номер декларации 
ALTER TABLE `orders` ADD `track_code` VARCHAR(32) NOT NULL DEFAULT '' AFTER `shipping_price`;
-- комментарий админа
ALTER TABLE `orders` ADD `admin_comment` TEXT NOT NULL DEFAULT '' AFTER `comment`;
-- канал
ALTER TABLE `orders` ADD `channel_code` varchar(255) NOT NULL DEFAULT '' AFTER `admin_comment`;
-- город
-- ALTER TABLE `orders` ADD `city` varchar(255) NOT NULL DEFAULT '' AFTER `email`;
-- адрес
-- ALTER TABLE `orders` ADD `address` varchar(255) NOT NULL DEFAULT '' AFTER `city`;
-- дата запланированого выполнения
ALTER TABLE `orders` ADD `planned` DATETIME NULL DEFAULT NULL AFTER `created`;
-- дата выполнения
ALTER TABLE `orders` ADD `closed` DATETIME NULL DEFAULT NULL AFTER `planned`;
-- дата выполнения
ALTER TABLE `orders` ADD `sms_sended` DATETIME NULL DEFAULT NULL AFTER `closed`;
-- индексы
ALTER TABLE `orders` 
ADD INDEX `idx_user` (`user_id`) USING BTREE,
ADD INDEX `idx_manager` (`manager_id`) USING BTREE,
ADD INDEX `idx_status` (`status_id`) USING BTREE,
ADD INDEX `idx_phone` (`phone`) USING BTREE,
ADD INDEX `idx_email` (`email`) USING BTREE;

-- товары в заказе
-- скидка
ALTER TABLE `order_products` ADD `discount_value` float(11,2) NOT NULL DEFAULT 0 AFTER `price`;
-- комментарий
ALTER TABLE `order_products` ADD `admin_comment` TEXT NOT NULL DEFAULT '' AFTER `discount_value`;

-- статусы
DROP TABLE IF EXISTS `order_status`;
CREATE TABLE IF NOT EXISTS `order_status` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `color_hex` varchar(6) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `order_status` (`title`, `color_hex`) VALUES
('Новый', ''),
('В ожидании', 'f8f3c3'),
('На производстве', 'ecddf6'),
('На доставке', 'cceef8'),
('Выполнен', '83f2c7'),
('Отклонен', 'e3e3e3');

-- обновление текущих заказов
UPDATE `orders` SET status_id=5, closed=created;

-- удаление лишних таблиц
DROP table `order_types`;
DROP table `orders_history`;
DROP table `catalog_kits`;
DROP table `catalog_related`;
DROP table `stocks_related`;
DROP table `stocks`;
DROP table `options`;
DROP table `options_values`;
DROP table `product_options_values`;
DROP table `comments`;
DROP table `product_options`;
DROP table `options_types`;
DROP table `coupons`;
DROP table `ru_brands_description`;
DROP table `ru_options`;
DROP table `ru_options_values`;
DROP table `ru_stocks`;

-- переименования
RENAME TABLE shipping_types TO ru_shipping_types;
RENAME TABLE payment_types TO ru_payment_types;

-- индексы к таблицам
ALTER TABLE `category_attributes` 
ADD INDEX `idx_cid` (`cid`) USING BTREE,
ADD INDEX `idx_aid` (`aid`) USING BTREE;

ALTER TABLE `category_attribute_groups` 
ADD INDEX `idx_cid` (`cid`) USING BTREE,
ADD INDEX `idx_gid` (`gid`) USING BTREE;

ALTER TABLE `category_filters` 
ADD INDEX `idx_cid` (`cid`) USING BTREE,
ADD INDEX `idx_fid` (`fid`) USING BTREE,
ADD INDEX `idx_type` (`type`) USING BTREE;

ALTER TABLE `images_params` 
ADD INDEX `idx_module` (`module`) USING BTREE;
ALTER TABLE `modules_params` 
ADD INDEX `idx_module` (`module`) USING BTREE;
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 23.08.2018 user_3
-- удаление ненужных и добавление нужных типов пользователей
DELETE FROM usertypes WHERE id>2 and id<>5;
UPDATE usertypes SET id=3 WHERE name='User';
INSERT INTO usertypes (id, `name`, name_en, name_ru, name_ua, active) VALUES
(4, 'Industrialist', 'Industrialist', 'Производственник', 'Производственник', 1);

-- настройки достпуов
TRUNCATE `users_access`;
INSERT INTO `users_access` (`id`, `uid`, `gid`, `modules`) VALUES
(1, 0, 1, 'auth,main,catalog,models,prints,banners,news,brands,homeslider,gallery,video,currency,customers,comments,attribute_groups,filters,orders,selections,forwards,options,settings,cms_settings,users,attributes,shortcuts,stocks,series,print_types,product_types,size_grids,sizes,colors'),
(2, 0, 2, 'auth,main,catalog,models,prints,banners,news,brands,homeslider,gallery,video,currency,customers,comments,attribute_groups,filters,orders,selections,forwards,options,settings,cms_settings,users,attributes,shortcuts,stocks,series,print_types,product_types,size_grids,sizes,colors'),
(3, 0, 4, 'auth,orders');
-- -----------------------------------------------------------------------------

-- модификации по таблицам users и orders
ALTER TABLE `users` CHANGE `type` `type` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'User';
-- -----------------------------------------------------------------------------

-- востановление пароля - пропавший модуляка 
INSERT INTO `ru_main` (`id`, `pid`, `redirectid`, `redirecturl`, `title`, `text`, `descr`, `image`, `image_menu`, `image_icon`, `pagetype`, `menutype`, `show_on_top`, `module`, `meta_descr`, `meta_key`, `meta_robots`, `seo_path`, `seo_title`, `seo_text`, `filter_seo_title`, `filter_seo_text`, `filter_meta_descr`, `filter_meta_key`, `product_seo_title`, `product_meta_descr`, `product_meta_key`, `order`, `active`, `access`, `created`, `modified`, `separator`, `source_id`) VALUES
(549, 0, 0, '', 'Восстановление пароля', '', NULL, NULL, NULL, NULL, 1, 0, 0, 'recovery', '', '', '', 'vosstanovlenie-parolya', '', '', '', '', '', '', '', '', '', 93, 1, 1, '2018-08-27 16:25:28', '2018-08-27 13:25:48', 0, 0);

-- -----------------------------------------------------------------------------

-- приведение всех телефонов к одному виду в заказах
UPDATE `orders` SET phone = REPLACE(phone, '+38', '');
UPDATE `orders` SET phone = REPLACE(phone, '-', '');
UPDATE `orders` SET phone = REPLACE(phone, ' ', '');
ALTER TABLE `orders` CHANGE `phone` `phone` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
-- -----------------------------------------------------------------------------

-- очистка мусоряки
DELETE FROM `orders` WHERE id<31 OR id=32 OR id=36 OR id=10008;
DELETE FROM `order_products` WHERE order_id<31 OR order_id=32 OR order_id=36 OR order_id=10008;

-- -----------------------------------------------------------------------------

-- заполнение пользователей из заказа
INSERT INTO `users` (`login`, `firstname`, `email`, `phone`, `created`)  
SELECT CONCAT('user_', `phone`) `login`, `name` `firstname`, `email`, `phone`, `created`
FROM `orders` GROUP BY `phone`;

UPDATE `orders` o SET o.`user_id`=(SELECT `id` FROM `users` WHERE `phone`=o.`phone` LIMIT 1);
-- -----------------------------------------------------------------------------

ALTER TABLE `ru_shipping_types` ADD `comment` VARCHAR(255) NOT NULL DEFAULT '' AFTER `price`;
UPDATE `ru_shipping_types` SET `comment` = 'по тарифам транспортной компании' WHERE `id` = 2;
UPDATE `ru_shipping_types` SET `comment` = 'бесплатно' WHERE `id` = 3;
UPDATE `ru_shipping_types` SET `comment` = 'бесплатно' WHERE `id` = 1;

ALTER TABLE `order_products` ADD `placement` VARCHAR(50) NOT NULL AFTER `series_title`;
UPDATE `order_products` SET `placement` = 'front';

-- -----------------------------------------------------------------------------
ALTER TABLE `order_products` ADD `is_cuted` TINYINT(1) NOT NULL DEFAULT '0' AFTER `admin_comment`;
ALTER TABLE `order_products` ADD `is_printed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_cuted`;

-- -----------------------------------------------------------------------------
ALTER TABLE `orders` ADD `processed` DATETIME NULL DEFAULT NULL AFTER `planned`;
ALTER TABLE `orders` ADD `prepay` float(11,2) NOT NULL DEFAULT 0 AFTER `total_price`;

ALTER TABLE `orders` ADD `type_id` int(11) NOT NULL AFTER `manager_id`;
UPDATE `orders` SET `type_id`=3;

-- типы заказа
DROP TABLE IF EXISTS `order_types`;
CREATE TABLE IF NOT EXISTS `order_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `order_types` (`title`) VALUES
('Админзона'),
('Обратный звонок'),
('Корзина'),
('В один клик'),
('Заявка');

-- смски
ALTER TABLE `orders` ADD `sms_track_code` DATETIME NULL DEFAULT NULL AFTER `sms_sended`;
UPDATE `orders` SET sms_track_code = sms_sended;
ALTER TABLE `orders` DROP `sms_sended`;
ALTER TABLE `orders` ADD `sms_prepay` DATETIME NULL DEFAULT NULL AFTER `sms_track_code`;
ALTER TABLE `orders` ADD `sms_order_id` DATETIME NULL DEFAULT NULL AFTER `sms_prepay`;

# лена - добавление кода цвета в товар без принта
ALTER TABLE `ru_catalog` ADD `color_code` VARCHAR(25) NOT NULL DEFAULT '' AFTER `pcode`;

# добавление кодов цветов в модели
CREATE TABLE `model_colorcodes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Айди модели',
  `color_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Айди цвета',
  `code` varchar(255) DEFAULT '' COMMENT 'Код цвета',
  PRIMARY KEY (`id`),
  KEY `idx_model` (`model_id`),
  KEY `idx_color` (`color_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='кода цветов в модели';

ALTER TABLE `ru_catalog` DROP `color_code`;

# добавление категории для модуля Конструктор
INSERT INTO `ru_main` (`id`, `pid`, `redirectid`, `redirecturl`, `title`, `text`, `descr`, `image`, `image_menu`, `image_icon`, `pagetype`, `menutype`, `show_on_top`, `module`, `meta_descr`, `meta_key`, `meta_robots`, `seo_path`, `seo_title`, `seo_text`, `filter_title`, `filter_seo_title`, `filter_seo_text`, `filter_meta_descr`, `filter_meta_key`, `product_seo_title`, `product_meta_descr`, `product_meta_key`, `order`, `active`, `access`, `created`, `modified`, `separator`, `source_id`) VALUES
(NULL, 0, 0, '', 'Конструктор', '&lt;p&gt;&lt;iframe style=&quot;width: 100%; height: 600px; position: relative; margin-top: 10px;&quot; src=&quot;https://cosuv.ru/app/3374&quot; frameborder=&quot;0&quot; width=&quot;320&quot; height=&quot;240&quot;&gt;&lt;/iframe&gt;&lt;/p&gt;', NULL, NULL, NULL, NULL, 0, 1, 1, 'constructor', '', '', '', 'konstruktor', '', '', '', '', '', '', '', '', '', '', 22, 1, 1, '2018-12-17 13:46:17', '2018-12-17 07:23:57', 0, 0);

INSERT INTO `usertypes` (`id`, `name`, `name_en`, `name_ru`, `name_ua`, `active`) VALUES 
(NULL, 'Publisher', 'Publisher', 'Публикатор', 'Публикатор', '1');

-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
-- STEP 1
-- apply code below

-- -----------------------------------------------------------------------------
-- 06.04.2018 ------------------------------------------------------------------
-- SEO тексты для фильтров -----------------------------------------------------
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `seo_filters`;
CREATE TABLE `seo_filters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID категории',
  `hash` varchar(32) DEFAULT NULL COMMENT 'MD5 хеш сериализованного массива набора фильтров',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT 'H1 заголовок',
  `seo_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'title заголовок',
  `meta_descr` varchar(255) NOT NULL DEFAULT '' COMMENT 'мета описание',
  `meta_key` varchar(255) NOT NULL DEFAULT '' COMMENT 'ключевые слова',
  `meta_robots` varchar(32) NOT NULL DEFAULT '' COMMENT 'meta robots',
  `seo_text` text COMMENT 'сео-текст',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_hash` (`hash`),
  KEY `idx_cid` (`category_id`),
  KEY `idx_active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Сео-тексты для фильтров';

DROP TABLE IF EXISTS `seo_filter_set`;
CREATE TABLE `seo_filter_set` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `sf_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID группы сео-фильтров',
  `filter_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID фильтра',
  `value_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID значения фильтра',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT 'текстовое значение фильтра',
  PRIMARY KEY (`id`),
  KEY `idx_sf` (`sf_id`),
  KEY `idx_filter` (`filter_id`),
  KEY `idx_value` (`value_id`),
  KEY `idx_cid` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Набор фильтров';
# ##############################################################################


-- -----------------------------------------------------------------------------
-- 06.11.2018 ------------------------------------------------------------------
-- Категория, поджопки, переименование поджопок --------------------------------
-- -----------------------------------------------------------------------------
# ##############################################################################
# ЛЕНА :P - Добавление поля категории в модели! 
# О-па, ваще такая, даже с индексом сразу!!!
ALTER TABLE `ru_models` ADD `category_id` INT(11) NOT NULL AFTER `id`;
ALTER TABLE `ru_models` ADD INDEX `idx_category` (`category_id`);
# пендюрим все модели в категорию Каталог одежды
UPDATE `ru_models` SET `category_id`=11;

# ##############################################################################
# ЛЕНА :P - Добавление атрибутов в поджопки! 
DROP TABLE IF EXISTS `wrapper_attributes`;
DROP TABLE IF EXISTS `substrates_attributes`;
CREATE TABLE IF NOT EXISTS `substrates_attributes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL DEFAULT '1' COMMENT 'Attribute ID',
  `sid` int(11) NOT NULL DEFAULT '1' COMMENT 'Substrate ID',
  `value` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `idx_aid` (`aid`),
   KEY `idx_sid` (`sid`),   
   KEY `idx_value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Substrates Attributes Values';

# переименование типов товаров в подложки
DROP TRIGGER IF EXISTS `ru_product_types_ai`;
DROP TRIGGER IF EXISTS `ru_product_types_au`;
DROP TRIGGER IF EXISTS `ru_product_types_ad`;

RENAME TABLE ru_product_types TO ru_substrates;
RENAME TABLE product_types_images TO substrates_images;
RENAME TABLE product_types_sizes TO substrates_sizes;

ALTER TABLE `substrates_images` CHANGE `type_id` `substrate_id` INT(11) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `substrates_images` DROP INDEX `idx_tid`, ADD INDEX `idx_substrate` (`substrate_id`) USING BTREE;

ALTER TABLE `substrates_sizes` CHANGE `type_id` `substrate_id` INT(11) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `substrates_sizes` DROP INDEX `idx_tid`, ADD INDEX `idx_substrate` (`substrate_id`) USING BTREE;

ALTER TABLE `order_products` CHANGE `type_id` `substrate_id` INT(11) UNSIGNED NOT NULL;
ALTER TABLE `order_products` DROP INDEX `idx_type`, ADD INDEX `idx_substrate` (`substrate_id`) USING BTREE;

ALTER TABLE `print_assortment` CHANGE `type_id` `substrate_id` INT(11) UNSIGNED NOT NULL COMMENT 'Substrate ID';
ALTER TABLE `print_assortment` DROP INDEX `idx_type_id`, ADD INDEX `idx_substrate_id` (`substrate_id`) USING BTREE;

ALTER TABLE `ru_models` CHANGE `type_id` `substrate_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `ru_models` DROP INDEX `idx_type_id`, ADD INDEX `idx_substrate_id` (`substrate_id`) USING BTREE;

ALTER TABLE `ru_prints` CHANGE `type_id` `substrate_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Product Default Substrate ID';
ALTER TABLE `ru_prints` DROP INDEX `idx_type_id`, ADD INDEX `idx_substrate_id` (`substrate_id`) USING BTREE;

ALTER TABLE `order_products` CHANGE `type_title` `substrate_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

UPDATE `modules_params` SET `module` = 'substrates', `title` = 'Подложки', `short_title` = 'Подложки' WHERE `modules_params`.`module` = 'product_types' ;
UPDATE users_access set modules = REPLACE(modules, 'product_types', 'substrates');

-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
-- STEP 2
-- run drop index command: php cronjobs/indexer.php --print=1 --command=drop


-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
-- STEP 3
-- apply code below

UPDATE ru_prints SET meta_key=REPLACE(meta_key, '{type', '{substrate'), meta_descr=REPLACE(meta_descr, '{type', '{substrate'), seo_title=REPLACE(seo_title, '{type', '{substrate');
UPDATE ru_main SET 
product_seo_title=REPLACE(product_seo_title, '{type', '{substrate'),
meta_key=REPLACE(meta_key, '{type', '{substrate'),
product_meta_descr=REPLACE(product_meta_descr, '{type', '{substrate'),
meta_descr=REPLACE(meta_descr, '{type', '{substrate'),
seo_title=REPLACE(seo_title, '{type', '{substrate'),
seo_text=REPLACE(seo_text, '{type', '{substrate');

# удаление фильтра Тип товара
DELETE FROM `ru_filters` WHERE `tid` = 6; # old id =14
DELETE FROM `ru_filter_types` WHERE `id` = 6;
DELETE FROM `category_filters` WHERE `fid` = 14;

# добавление фильтра по категории
INSERT INTO  `ru_filter_types` (
    `id` ,
    `title` ,
    `type` ,
    `colname` ,
    `active` ,
    `order`
) VALUES (
    '6',  'Категория',  'int', NULL ,  '1',  '1'
);
INSERT INTO `ru_filters` (
    `id`, `tid`, `aid`, `title`, `order`, `created`, `modified`
) VALUES ('14', '6', '0', 'Тематика', '1', '2018-11-20 00:00:00', CURRENT_TIMESTAMP);
# ##############################################################################

-- -----------------------------------------------------------------------------
-- 10.11.2018 ------------------------------------------------------------------
-- Удаление подложки из одежды -------------------------------------------------
-- -----------------------------------------------------------------------------
UPDATE `modules_params` SET `title` = 'Каталог одежды', `short_title` = 'Одежда' WHERE `module` = 'catalog';
UPDATE `modules_params` SET `title` = 'Каталог одежды', `short_title` = 'Каталог одежды' WHERE `module` = 'models';

ALTER TABLE `ru_models` DROP `substrate_id`;
ALTER TABLE `order_products` ADD `model_id` INT(11) NOT NULL AFTER `substrate_id`;
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 10.11.2018 ------------------------------------------------------------------
-- Редиректы с категорий (/admin.php?module=forwards&task=addCategoriesRedirects)---
-- -----------------------------------------------------------------------------
ALTER TABLE `forwards` ADD `old_cid` INT(1) NOT NULL DEFAULT '0' AFTER `source_id`;
ALTER TABLE `forwards` ADD `old_urito` VARCHAR(255) NOT NULL DEFAULT '' AFTER `urito`;

-- -----------------------------------------------------------------------------
-- 17.11.2018 ------------------------------------------------------------------
-- Добавлена настройка группировки подложек по атрибутам -----------------------
-- -----------------------------------------------------------------------------
ALTER TABLE `ru_filters` ADD `separate` TINYINT(1) NOT NULL DEFAULT '0';
UPDATE  `ru_filters` SET  `separate` =  '1' WHERE  `ru_filters`.`id` =3;
UPDATE  `ru_filters` SET  `separate` =  '1' WHERE  `ru_filters`.`id` =18;

-- -----------------------------------------------------------------------------
-- 20.11.2018 ------------------------------------------------------------------
-- Хлебные крошки --------------------------------------------------------------
-- -----------------------------------------------------------------------------
ALTER TABLE `ru_filters` ADD `alias` VARCHAR(255) NOT NULL AFTER `title`;
UPDATE ru_filters SET `alias`="sex" WHERE id=3;
UPDATE ru_filters SET `alias`="type" WHERE id=18;
UPDATE ru_filters SET `alias`="category" WHERE id=14;
UPDATE ru_main SET `title`="Одежда с принтами" WHERE id=9;

-- -----------------------------------------------------------------------------
-- 17.11.2018 ------------------------------------------------------------------
-- Удаление старых связей принтов с полом и типом одежды -----------------------
-- -----------------------------------------------------------------------------
DELETE FROM `print_attributes` WHERE `aid` IN('16', '9');

-- -----------------------------------------------------------------------------
-- 20.11.2018 ------------------------------------------------------------------
-- добавление связей к подложкам и добавление недостающей атрибутики -----------
-- -----------------------------------------------------------------------------
-- `id`, `aid`, `title`, `title_single`, `title_multi`, `image`, `seo_path`, `order`
INSERT INTO `ru_attributes_values` VALUES 
(77,16,'Свитшоты','Свитшот','','','svitshoti',3),
(78,16,'Майки','Майка','','','mayky',1),
(79,16,'Кенгурушки','Кенгурушка','','','kengurushki',2)	;
-- `id`, `aid`, `sid`, `value`, `created`, `modified`
INSERT INTO `substrates_attributes` VALUES 
(1,16,1,76,'2018-11-20 10:46:58','2018-11-20 10:46:58'),
(2,9,1,30,'2018-11-20 10:46:58','2018-11-20 10:46:58'),
(3,9,2,31,'2018-11-20 10:47:27','2018-11-20 10:47:27'),
(4,16,2,76,'2018-11-20 10:47:27','2018-11-20 10:47:27'),
(5,9,6,30,'2018-11-20 10:47:44','2018-11-20 10:47:44'),
(6,16,6,74,'2018-11-20 10:47:44','2018-11-20 10:47:44'),
(7,9,7,31,'2018-11-20 10:47:58','2018-11-20 10:47:58'),
(8,16,7,74,'2018-11-20 10:47:58','2018-11-20 10:47:58'),
(9,16,8,77,'2018-11-20 10:49:21','2018-11-20 10:49:21'),
(10,9,9,32,'2018-11-20 10:49:43','2018-11-20 10:49:43'),
(11,16,9,76,'2018-11-20 10:49:43','2018-11-20 10:49:43'),
(12,9,10,42,'2018-11-20 10:50:43','2018-11-20 10:50:43'),
(13,9,10,32,'2018-11-20 10:50:43','2018-11-20 10:50:43'),
(14,16,10,76,'2018-11-20 10:50:43','2018-11-20 10:50:43'),
(15,9,11,32,'2018-11-20 10:51:10','2018-11-20 10:51:10'),
(16,16,11,77,'2018-11-20 10:51:10','2018-11-20 10:51:10'),
(17,16,3,73,'2018-11-20 15:37:23','2018-11-20 15:37:23'),
(18,16,4,79,'2018-11-20 15:44:25','2018-11-20 15:44:25'),
(19,16,5,78,'2018-11-20 15:44:45','2018-11-20 15:44:45');

# наполняем адекватными фильтрами базовую категорию
DELETE FROM `category_filters` WHERE `cid` = 9;
INSERT INTO `category_filters` (`cid`, `fid`, `type`, `order`) VALUES
(9, 14, 1, 1),
(9, 18, 1, 2),
(9, 3, 1, 3),
(9, 11, 1, 4),
(9, 18, 2, 1),
(9, 3, 2, 2),
(9, 14, 2, 3);

-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
-- STEP 4
-- run init index command: php cronjobs/indexer.php --print=1 --command=init

-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
-- STEP 5
-- run full index command: php cronjobs/indexer.php --print=1 --command=full

-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
-- STEP 6
-- rename folder product_types to substrates

-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
-- STEP 7
-- run on web for add redirects : /admin.php?module=forwards&task=addCategoriesRedirects
-- check categories config & surf site for test

# ##############################################################################


# добавление типа оплаты 
ALTER TABLE `orders` ADD `payment_id` INT(11) NOT NULL DEFAULT '0' AFTER `email`;

# типы оплаты
DROP TABLE IF EXISTS `ru_payment_types`;
CREATE TABLE IF NOT EXISTS `ru_payment_types` (
  `id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_card` int(1) NOT NULL DEFAULT '0',
  `card_info`varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `ru_payment_types` (`id`, `title`, `is_card`, `card_info`, `active`) VALUES
(1, 'Наличными', 0, '', 1),
(2, 'Б/н расчет', 0, '', 0),
(3, 'LiqPAY', 0, '', 0),
(4, 'На карту', 1, 'Приват карта ФОП 5169 3305 1479 6330 Костерная О.В.', 1),
(5, 'WebMoney', 0, '', 0);

ALTER TABLE `ru_payment_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active` (`active`);

ALTER TABLE `ru_payment_types`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;

# ##############################################################################


# ##############################################################################
# добавление бана для нихороших еюзверов
# ##############################################################################
ALTER TABLE `users` ADD `banned` TINYINT(1) NOT NULL DEFAULT '0' AFTER `seo_path`;

# ##############################################################################
# добавление склонений для атрибутов и цветов ##################################
# ##############################################################################
ALTER TABLE `ru_attributes_values` ADD `title_male` varchar(255) NOT NULL DEFAULT '' AFTER `title_multi`;
ALTER TABLE `ru_attributes_values` ADD `title_female` varchar(255) NOT NULL DEFAULT '' AFTER `title_male`;
ALTER TABLE `ru_attributes_values` ADD `title_extra` varchar(255) NOT NULL DEFAULT '' AFTER `title_female`;
ALTER TABLE `ru_colors` ADD `title_single` varchar(255) NOT NULL DEFAULT '' AFTER `title`;
ALTER TABLE `ru_colors` ADD `title_multi` varchar(255) NOT NULL DEFAULT '' AFTER `title_single`;
ALTER TABLE `ru_colors` ADD `title_male` varchar(255) NOT NULL DEFAULT '' AFTER `title_multi`;
ALTER TABLE `ru_colors` ADD `title_female` varchar(255) NOT NULL DEFAULT '' AFTER `title_male`;
ALTER TABLE `ru_colors` ADD `title_extra` varchar(255) NOT NULL DEFAULT '' AFTER `title_female`;

# ##############################################################################
# добавление новых полей в таблицу seo-фильтров ################################
# ##############################################################################
ALTER TABLE `seo_filters` ADD `title_var` varchar(255) NOT NULL DEFAULT '' AFTER `title`;
ALTER TABLE `seo_filters` ADD `seo_title_var` varchar(255) NOT NULL DEFAULT '' AFTER `seo_title`;
ALTER TABLE `seo_filters` ADD `meta_descr_var` varchar(255) NOT NULL DEFAULT '' AFTER `meta_descr`;
ALTER TABLE `seo_filters` ADD `meta_key_var` varchar(255) NOT NULL DEFAULT '' AFTER `meta_key`;
ALTER TABLE `seo_filters` ADD `seo_text_var` varchar(255) NOT NULL DEFAULT '' AFTER `seo_text`;