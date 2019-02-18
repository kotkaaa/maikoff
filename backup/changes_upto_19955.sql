-- -----------------------------------------------------------------------------
-- 07.03.2018 user_5
-- добавление модуля thanks (Спасибо за заказ)
INSERT INTO `ru_main` (`id`,`pid`,`redirectid`,`redirecturl`,`title`,`text`,`descr`,`image`,`image_menu`,`image_icon`,`pagetype`,`menutype`,`show_on_top`,`module`,`meta_descr`,`meta_key`,`meta_robots`,`seo_path`,`seo_title`,`seo_text`,`filter_seo_title`,`filter_seo_text`,`filter_meta_descr`,`filter_meta_key`,`order`,`active`,`access`,`created`,`modified`,`separator`) VALUES 
(NULL,0,0,'','Заказ оформлен','',NULL,NULL,NULL,NULL,0,0,0,'thanks','Заказ оформлен','Заказ оформлен','','thank-you','Заказ оформлен','','','','','',34,1,1,'2018-07-10 14:27:15','2018-07-10 14:28:19',0);
-- модификация таблицы `order_products`, удалены директивы NOT NULL для VARCHAR полей
-- добавлена колонка `product_idkey`
RENAME TABLE `order_products` TO `order_products_old`; 

CREATE TABLE `order_products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_idkey` varchar(64) DEFAULT '',
  `type_id` int(11) unsigned NOT NULL,
  `color_id` int(11) unsigned NOT NULL,
  `size_id` int(11) unsigned NOT NULL,
  `brand_id` int(11) unsigned NOT NULL DEFAULT '0',
  `series_id` int(11) unsigned NOT NULL DEFAULT '0',
  `module` varchar(255) DEFAULT '',
  `pcode` varchar(32) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `type_title` varchar(255) DEFAULT '',
  `color_title` varchar(255) DEFAULT '',
  `size_title` varchar(255) DEFAULT '',
  `color_hex` varchar(255) DEFAULT '',
  `brand_title` varchar(255) DEFAULT '',
  `series_title` varchar(255) DEFAULT '',
  `product_image` longblob NOT NULL,
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

INSERT INTO `order_products` (order_id, product_id, type_id, color_id, size_id, brand_id, series_id, `module`, pcode, title, type_title, color_title, size_title, color_hex, brand_title, series_title, product_image, qty, price) 
SELECT order_id, product_id, type_id, color_id, size_id, brand_id, series_id, `module`, pcode, title, type_title, color_title, size_title, color_hex, brand_title, series_title, product_image, qty, price
FROM `order_products_old` ;

DROP TABLE `order_products_old`;

-- 10.07.2018 user_3
-- текст описание в модель
ALTER TABLE `ru_models` ADD `text` TEXT NOT NULL DEFAULT '' AFTER `sizes`;
ALTER TABLE `ru_prints` ADD `text` TEXT NOT NULL DEFAULT '' AFTER `title`;
-- заголовки для типов товаров
ALTER TABLE `ru_product_types` 
ADD `title_s` TEXT NOT NULL DEFAULT '' COMMENT "singular" AFTER `title`, 
ADD `title_p` TEXT NOT NULL DEFAULT '' COMMENT "plural" AFTER `title_s`;
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 11.07.2018 user_3
-- модуль переадресаций
-- данные импортировать в таблицу из файла forwards.sql.gz
DROP TABLE IF EXISTS `forwards`;
CREATE TABLE IF NOT EXISTS `forwards` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `urifrom` varchar(255) NOT NULL,
  `urito` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx_urifrom` (`urifrom`),
  KEY `idx_urito` (`urito`),
  KEY `idx_active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- добавляем настройки
DELETE FROM `modules_params` WHERE `module` IN ('forwards');
INSERT INTO `modules_params` (`module`, `title`, `short_title`, `seotable`, `seogroup`, `images`, `access`, `history`, `menu`, `order`) VALUES
('forwards', 'Переадресации', 'Forwards', '', 0, 0, 1, 1, 1, 30);
-- увеличение размера поля и добавлние нового модуля
ALTER TABLE `users_access` CHANGE `modules` `modules` TEXT NULL DEFAULT NULL;
UPDATE `users_access` SET `modules`=CONCAT(`modules`, ',forwards') WHERE CONCAT(',', `modules`, ',') NOT LIKE '%,forwards,%' AND `uid`=0 AND `gid`=1;
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 17.07.2018 user_3
-- добавление категории печать лого
INSERT INTO `ru_main` 
(`id`, `pid`, `redirectid`, `redirecturl`, `title`, `text`, `descr`, `image`, `image_menu`, `image_icon`, `pagetype`, `menutype`, `show_on_top`, `module`, `meta_descr`, `meta_key`, `meta_robots`, `seo_path`, `seo_title`, `seo_text`, `filter_seo_title`, `filter_seo_text`, `filter_meta_descr`, `filter_meta_key`, `order`, `active`, `access`, `created`, `modified`, `separator`) 
VALUES 
(11, '0', '0', '', 'Печать Лого', NULL, NULL, NULL, NULL, NULL, '0', '1', '0', '', '', '', '', 'pechat-logo', '', '', '', '', '', '', '0', '1', '1', NOW(), CURRENT_TIMESTAMP, '0');
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 23.07.2018 user_7
-- включил модули в сеогруппу по уникальным сеопутям
UPDATE `modules_params` SET `seotable` = 'PRODUCT_TYPES_TABLE', `seogroup` = '1' WHERE `module` = 'product_types' LIMIT 1 ;
UPDATE `modules_params` SET `seotable` = 'COLORS_TABLE', `seogroup` = '1' WHERE `module` = 'colors' LIMIT 1 ;
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 23.07.2018 user_3
-- добавление шаблонных метаданных для товаров в категории
ALTER TABLE `ru_main` ADD `product_seo_title` VARCHAR( 255 ) NOT NULL AFTER `filter_meta_key` ;
ALTER TABLE `ru_main` ADD `product_meta_descr` text NOT NULL AFTER `product_seo_title` ;
ALTER TABLE `ru_main` ADD `product_meta_key` VARCHAR( 255 ) NOT NULL AFTER `product_meta_descr` ;
-- добавление поля active в тип товара
ALTER TABLE `ru_product_types` ADD `active` tinyint(1) NOT NULL DEFAULT '1' AFTER `order` ;
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 24.07.2018 user_5
-- ссылки на купленные товары (для отображения на результирующей странице)
ALTER TABLE `order_products` ADD `product_url` varchar(255) NOT NULL DEFAULT '' AFTER `product_image`;
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 24.07.2018 user_7
-- приведение айдишек к старому образцу основных модулей (встречаются в текстах и нет редиректов)
-- Печать Лого
UPDATE `ru_main` SET `id` = '10' WHERE `id` = 11; 
UPDATE `ru_main` SET `pid` = '10' WHERE `pid`= 11;
UPDATE `ru_main` SET `redirectid` = '10' WHERE `redirectid`= 11;
-- События
UPDATE `ru_main` SET `id` = '13' WHERE `id` = 72; 
UPDATE `ru_main` SET `pid` = '13' WHERE `pid`= 72;
UPDATE `ru_main` SET `redirectid` = '13' WHERE `redirectid`= 72;
UPDATE `ru_news` SET `cid` = '13' WHERE `cid` = 72;
-- Футболки с принтом
UPDATE `ru_main` SET `id` = '9' WHERE `id` = 83; 
UPDATE `ru_main` SET `pid` = '9' WHERE `pid`= 83;
UPDATE `ru_main` SET `redirectid` = '9' WHERE `redirectid`= 83;
UPDATE `shortcuts` SET `cid` = '9' WHERE `id` = 83;
UPDATE `ru_prints` SET `category_id` = '9' WHERE `category_id` = 83;
UPDATE  `category_attributes` SET  `cid` =  '9' WHERE  `cid` =  83;
UPDATE  `category_attribute_groups` SET  `cid` =  '9' WHERE  `cid` =  83;
UPDATE  `category_filters` SET  `cid` =  '9' WHERE  `cid` =  83;
-- Товары под печать
UPDATE `ru_main` SET `id` = '11' WHERE `id` = 81; 
UPDATE `ru_main` SET `pid` = '11' WHERE `pid`= 81;
UPDATE `ru_main` SET `redirectid` = '11' WHERE `redirectid`= 81;
UPDATE `shortcuts` SET `cid` = '11' WHERE `id` = 81;
UPDATE  `category_attributes` SET  `cid` =  '11' WHERE  `cid` =  81;
UPDATE  `category_attribute_groups` SET  `cid` =  '11' WHERE  `cid` =  81;
UPDATE  `category_filters` SET  `cid` =  '11' WHERE  `cid` =  81;

-- Контакты
UPDATE `ru_main` SET `id` = '12' WHERE `id` = 16; 
UPDATE `ru_main` SET `pid` = '12' WHERE `pid`= 16;
UPDATE `ru_main` SET `redirectid` = '12' WHERE `redirectid`= 16;

-- О Компании
UPDATE `ru_main` SET `id` = '14' WHERE `id` = 142; 
UPDATE `ru_main` SET `pid` = '14' WHERE `pid`= 142;
UPDATE `ru_main` SET `redirectid` = '14' WHERE `redirectid`= 142;

-- Доставка и оплата
UPDATE `ru_main` SET `id` = '15' WHERE `id` = 129; 
UPDATE `ru_main` SET `pid` = '15' WHERE `pid`= 129;
UPDATE `ru_main` SET `redirectid` = '15' WHERE `redirectid`= 129;

-- Заказ оформлен
UPDATE `ru_main` SET `id` = '17' WHERE `id` = 183; 
UPDATE `ru_main` SET `pid` = '17' WHERE `pid`= 183;
UPDATE `ru_main` SET `redirectid` = '17' WHERE `redirectid`= 183;
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 24.07.2018 user_7
-- приведение к единому типу
TRUNCATE `modules_params`;
INSERT INTO `modules_params` (`module`, `title`, `short_title`, `seotable`, `seogroup`, `images`, `access`, `history`, `menu`, `order`) VALUES
('attributes', 'Атрибуты', 'Атрибуты', 'ATTRIBUTES_VALUES_TABLE', 1, 0, 1, 1, 0, 21),
('attribute_groups', 'Группы атрибутов', 'Группы атрибутов', '', 0, 0, 1, 1, 1, 13),
('banners', 'Баннеры сайта', 'Баннеры', '', 0, 0, 1, 1, 0, 4),
('brands', 'Бренды', 'Бренды', 'BRANDS_TABLE', 1, 1, 1, 1, 1, 6),
('catalog', 'Каталог товаров', 'Товары', 'CATALOG_TABLE', 1, 1, 1, 1, 0, 2),
('comments', 'Комментарии', 'Комментарии', '', 0, 0, 1, 1, 0, 12),
('currency', 'Валюта', 'Валюта', '', 0, 0, 1, 1, 0, 10),
('customers', 'Пользователи', 'Пользователи', '', 0, 0, 1, 1, 0, 11),
('filters', 'Фильтры', 'Фильтры', '', 0, 0, 1, 1, 1, 14),
('gallery', 'Галерея', 'Галерея', 'GALLERY_TABLE', 1, 0, 1, 1, 0, 8),
('homeslider', 'Слайдер', 'Слайдер', '', 0, 1, 1, 1, 1, 7),
('main', 'Структура разделов', 'Разделы', 'MAIN_TABLE', 1, 1, 1, 1, 1, 1),
('news', 'Статьи', 'Статьи', 'NEWS_TABLE', 1, 1, 1, 1, 1, 5),
('options', 'Опции товаров', 'Опции', '', 0, 0, 1, 1, 0, 18),
('orders', 'Заказы', 'Заказы', '', 0, 0, 1, 1, 1, 15),
('selections', 'Выборки', 'Выборки', '', 0, 0, 1, 1, 1, 16),
('settings', 'Настройки', 'Настройки', '', 0, 0, 1, 1, 0, 19),
('users', 'Администраторы', 'Администраторы', '', 0, 0, 1, 1, 0, 20),
('video', 'Видео', 'Видео', 'VIDEOS_TABLE', 1, 0, 1, 1, 0, 9),
('shortcuts', 'Ярлыки', 'Ярлыки', '', 0, 0, 1, 1, 0, 22),
('stocks', 'Акции', 'Акции', 'STOCKS_TABLE', 1, 0, 1, 1, 0, 23),
('series', 'Серии', 'Серии', 'SERIES_TABLE', 1, 0, 1, 1, 0, 24),
('print_types', 'Виды печати', 'Виды печати', 'PRINT_TYPES_TABLE', 1, 1, 1, 1, 1, 25),
('colors', 'Цвета', 'Цвета', '', 0, 0, 1, 1, 0, 30),
('models', 'Каталог товаров', 'Каталог товаров', '', 0, 0, 1, 1, 1, 2),
('sizes', 'Размеры', 'Размеры', '', 0, 0, 1, 1, 0, 29),
('size_grids', 'Таблицы размеров', 'Таблицы размеров', '', 0, 0, 1, 1, 1, 27),
('product_types', 'Типы товаров', 'Типы товаров', '', 0, 0, 1, 1, 1, 26),
('prints', 'Каталог принтов', 'Принты', 'PRINT_ASSORTMENT_TABLE', 1, 1, 1, 1, 1, 3),
('forwards', 'Переадресации', 'Forwards', '', 0, 0, 1, 1, 1, 17);
-- -----------------------------------------------------------------------------