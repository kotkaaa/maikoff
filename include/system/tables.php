<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

if(!defined('BANNERS_TABLE')){              define('BANNERS_TABLE',             $lang.DBTABLE_LANG_SEP.'banners'); }
if(!defined('SUBSTRATES_TABLE')){           define('SUBSTRATES_TABLE',          $lang.DBTABLE_LANG_SEP.'substrates'); }
if(!defined('MODELS_TABLE')){               define('MODELS_TABLE',              $lang.DBTABLE_LANG_SEP.'models'); }
if(!defined('CATALOG_TABLE')){              define('CATALOG_TABLE',             $lang.DBTABLE_LANG_SEP.'catalog'); }
if(!defined('CURRENCY_INFO_TABLE')){        define('CURRENCY_INFO_TABLE',       $lang.DBTABLE_LANG_SEP.'currency_info'); }
if(!defined('GALLERY_TABLE')){              define('GALLERY_TABLE',             $lang.DBTABLE_LANG_SEP.'gallery'); }
if(!defined('HOMESLIDER_TABLE')){           define('HOMESLIDER_TABLE',          $lang.DBTABLE_LANG_SEP.'homeslider'); }
if(!defined('MAIN_TABLE')){                 define('MAIN_TABLE',                $lang.DBTABLE_LANG_SEP.'main'); }
if(!defined('NEWS_TABLE')){                 define('NEWS_TABLE',                $lang.DBTABLE_LANG_SEP.'news'); }
if(!defined('PRINT_TYPES_TABLE')){          define('PRINT_TYPES_TABLE',         $lang.DBTABLE_LANG_SEP.'print_types'); }
if(!defined('SIZE_GRIDS_TABLE')){           define('SIZE_GRIDS_TABLE',          $lang.DBTABLE_LANG_SEP.'size_grids'); }
if(!defined('SETTINGS_TABLE')){             define('SETTINGS_TABLE',            $lang.DBTABLE_LANG_SEP.'settings'); }
if(!defined('VIDEOS_TABLE')){               define('VIDEOS_TABLE',              $lang.DBTABLE_LANG_SEP.'videos'); }
if(!defined('BRANDS_TABLE')){               define('BRANDS_TABLE',              $lang.DBTABLE_LANG_SEP.'brands'); }
if(!defined('BRANDS_GALLERY_TABLE')){       define('BRANDS_GALLERY_TABLE',      $lang.DBTABLE_LANG_SEP.'brands_gallery'); }
if(!defined('SERIES_TABLE')){               define('SERIES_TABLE',              $lang.DBTABLE_LANG_SEP.'series'); }
if(!defined('ATTRIBUTES_TABLE')){           define('ATTRIBUTES_TABLE',          $lang.DBTABLE_LANG_SEP.'attributes'); }
if(!defined('ATTRIBUTE_GROUPS_TABLE')){     define('ATTRIBUTE_GROUPS_TABLE',    $lang.DBTABLE_LANG_SEP.'attribute_groups'); }
if(!defined('ATTRIBUTE_TYPES_TABLE')){      define('ATTRIBUTE_TYPES_TABLE',     $lang.DBTABLE_LANG_SEP.'attribute_types'); }
if(!defined('ATTRIBUTES_VALUES_TABLE')){    define('ATTRIBUTES_VALUES_TABLE',   $lang.DBTABLE_LANG_SEP.'attributes_values'); }
if(!defined('RANGES_TABLE')){               define('RANGES_TABLE',              $lang.DBTABLE_LANG_SEP.'ranges'); }
if(!defined('FILTERS_TABLE')){              define('FILTERS_TABLE',             $lang.DBTABLE_LANG_SEP.'filters'); }
if(!defined('FILTER_TYPES_TABLE')){         define('FILTER_TYPES_TABLE',        $lang.DBTABLE_LANG_SEP.'filter_types'); }
if(!defined('CATALOGFILES_TABLE')){         define('CATALOGFILES_TABLE',        $lang.DBTABLE_LANG_SEP.'catalogfiles'); }
if(!defined('COLORS_TABLE')){               define('COLORS_TABLE',              $lang.DBTABLE_LANG_SEP.'colors');}
if(!defined('SIZES_TABLE')){                define('SIZES_TABLE',               $lang.DBTABLE_LANG_SEP.'sizes');}
if(!defined('PRINTS_TABLE')){               define('PRINTS_TABLE',              $lang.DBTABLE_LANG_SEP.'prints');}
if(!defined('PRINTFILES_TABLE')){           define('PRINTFILES_TABLE',          $lang.DBTABLE_LANG_SEP.'printfiles');}
if(!defined('SELECTIONS_TABLE')){           define('SELECTIONS_TABLE',          $lang.DBTABLE_LANG_SEP.'selections');}
if(!defined('SELECTIONFILES_TABLE')){       define('SELECTIONFILES_TABLE',      $lang.DBTABLE_LANG_SEP.'selectionfiles');}
if(!defined('PAYMENT_TYPES_TABLE')){        define('PAYMENT_TYPES_TABLE',       $lang.DBTABLE_LANG_SEP.'payment_types'); }
if(!defined('SHIPPING_TYPES_TABLE')){       define('SHIPPING_TYPES_TABLE',      $lang.DBTABLE_LANG_SEP.'shipping_types'); }

if(!defined('SELECTION_PRODUCTS_TABLE')){       define('SELECTION_PRODUCTS_TABLE',          'selection_products');}
if(!defined('PRINT_ASSORTMENT_TABLE')){         define('PRINT_ASSORTMENT_TABLE',            'print_assortment');}
if(!defined('PRINT_ASSORTMENT_COLORS_TABLE')){  define('PRINT_ASSORTMENT_COLORS_TABLE',     'print_assortment_colors');}
if(!defined('PRINT_ASSORTMENT_SETTINGS_TABLE')){define('PRINT_ASSORTMENT_SETTINGS_TABLE',   'print_assortment_settings');}
if(!defined('PRINT_ATTRIBUTES_TABLE')){         define('PRINT_ATTRIBUTES_TABLE',            'print_attributes');}
if(!defined('MODEL_ATTRIBUTES_TABLE')){         define('MODEL_ATTRIBUTES_TABLE',            'model_attributes'); }
if(!defined('SUBSTRATES_ATTRIBUTES_TABLE')){    define('SUBSTRATES_ATTRIBUTES_TABLE',       'substrates_attributes'); }
if(!defined('CATEGORY_ATTRIBUTE_GROUPS_TABLE')){define('CATEGORY_ATTRIBUTE_GROUPS_TABLE',   'category_attribute_groups');}
if(!defined('CATEGORY_ATTRIBUTES_TABLE')){      define('CATEGORY_ATTRIBUTES_TABLE',         'category_attributes');}
if(!defined('CATEGORY_FILTERS_TABLE')){         define('CATEGORY_FILTERS_TABLE',            'category_filters');}

if(!defined('SUBSTRATES_IMAGES_TABLE')){    define('SUBSTRATES_IMAGES_TABLE',   'substrates_images'); }
if(!defined('SUBSTRATES_SIZES_TABLE')){     define('SUBSTRATES_SIZES_TABLE',    'substrates_sizes'); }
if(!defined('ORDERS_TABLE')){               define('ORDERS_TABLE',              'orders'); }
if(!defined('ORDER_FILES_TABLE')){          define('ORDER_FILES_TABLE',         'order_files'); }
if(!defined('ORDER_PRODUCTS_TABLE')){       define('ORDER_PRODUCTS_TABLE',      'order_products'); }
if(!defined('ORDER_STATUS_TABLE')){         define('ORDER_STATUS_TABLE',        'order_status');}
if(!defined('ORDER_TYPES_TABLE')){          define('ORDER_TYPES_TABLE',         'order_types');}
if(!defined('CURRENCY_TABLE')){             define('CURRENCY_TABLE',            'currency'); }
if(!defined('MENUTYPES_TABLE')){            define('MENUTYPES_TABLE',           'menutypes'); }
if(!defined('PAGETYPES_TABLE')){            define('PAGETYPES_TABLE',           'pagetypes'); }
if(!defined('USERS_TABLE')){                define('USERS_TABLE',               'users'); }
if(!defined('USERFILES_TABLE')){            define('USERFILES_TABLE',           'userfiles'); }
if(!defined('USERTYPES_TABLE')){            define('USERTYPES_TABLE',           'usertypes'); }
if(!defined('IMAGES_PARAMS_TABLE')){        define('IMAGES_PARAMS_TABLE',       'images_params'); }
if(!defined('MODULES_PARAMS_TABLE')){       define('MODULES_PARAMS_TABLE',      'modules_params'); }
if(!defined('USERS_ACCESS_TABLE')){         define('USERS_ACCESS_TABLE',        'users_access');}
if(!defined('SHORTCUTS_TABLE')){            define('SHORTCUTS_TABLE',           'shortcuts');}
if(!defined('PRODUCT_SIZES_TABLE')){        define('PRODUCT_SIZES_TABLE',       'product_sizes');}
if(!defined('SUBSCRIBITIONS_TABLE')){       define('SUBSCRIBITIONS_TABLE',      'subscribitions');}
if(!defined('FORWARDS_TABLE')){             define('FORWARDS_TABLE',            'forwards');}
if(!defined('SEO_FILTERS_TABLE')){          define('SEO_FILTERS_TABLE',         'seo_filters');}
if(!defined('SEO_FILTER_SET_TABLE')){       define('SEO_FILTER_SET_TABLE',      'seo_filter_set');}

if(!defined('NP_CITY_TABLE')){              define('NP_CITY_TABLE',             'np_city');}
if(!defined('NP_WAREHOUSE_TABLE')){         define('NP_WAREHOUSE_TABLE',        'np_warehouse');}
if(!defined('MODEL_COLORCODES_TABLE')){     define('MODEL_COLORCODES_TABLE',    'model_colorcodes');}

// index tables
if(!defined('CATALOG_INDEX_TABLE')){        define('CATALOG_INDEX_TABLE',       $lang.DBTABLE_LANG_SEP.'catalog_index'); }
if(!defined('PRINT_INDEX_TABLE')){          define('PRINT_INDEX_TABLE',         $lang.DBTABLE_LANG_SEP.'print_index'); }
