<?php

/*
 * WebLife CMS
 * Developed by http://weblife.ua/
 */

defined('WEBlife') or die('Restricted access'); // no direct access

require_once 'product/PrintProduct.php';

/*
 * Description of Importer
 *
 * @author weblife
 */
class Importer {
    /**
     * Модули в системе
     */
    const MODULE_COLORS = "colors"; // Цвета
    const MODULE_TYPES = "substrates"; // Подложки
    const MODULE_CATALOG = "catalog"; // Товары
    const MODULE_NEWS = "news"; // Новости
    const MODULE_STATIC = "main"; // Страницы
    const MODULE_PRINTS = "prints"; // Принты
    /**
     * @var array 
     */
    private $logs;
    /**
     * @var bool 
     */
    private $debug;
    /**
     * @var bool 
     */
    private $print;
    /**
     * @var DbConnector 
     */
    private $db;
    /**
     * @var UrlWL 
     */
    private $url;
    /**
     * @var DataReader 
     */
    private $reader;
    /**
     * @var DataWriter 
     */
    private $writer;
    /**
     * @param DbConnector $db
     * @param UrlWL $url
     * @param bool $debug
     * @param bool $print
     */
    public function __construct(DbConnector $db, UrlWL $url, $debug, $print) {
        $this->db = $db;
        $this->url = $url;
        $this->debug = $debug;
        $this->print = $print;
        $this->logs = array();
    }
    /**
     * @param DataReader $reader
     * @return $this
     */
    public function setReader(DataReader $reader) {
        $this->reader = $reader;
        return $this;
    }
    /**
     * @param DataWriter $writer
     * @return $this
     */
    public function setWriter(DataWriter $writer) {
        $this->writer = $writer;
        return $this;
    }
    /**
     * One public point to run the task
     * @param string $task
     * @param int $offset
     * @param int $limit
     * @return int
     * @throws Exception
     */
    public function run ($task, $offset, $limit){
        $func = 'get' . $task;
        if (!($this->reader instanceof DataReader)) {
            throw new Exception('Не инициализирована переменная $reader обьектом DataReader');
        } else if (!($this->writer instanceof DataWriter)) {
            throw new Exception('Не инициализирована переменная $reader обьектом DataReader');
        } else if (!method_exists($this, $func)) {
            throw new Exception('!!!    ERROR    !!!' . PHP_EOL . 'Error: Task "' . $task . '" not recognized. Check command line args!');
        } else {
            return $this->$func($offset, $limit);
        }
    }
    /**
     * @param string $glue
     * @return string
     */
    public function getLogs ($glue) {
        $logs = implode($glue, $this->logs);
        if($logs) $logs .= $glue;
        return $logs;
    }
    /**
     * Add source_id column into $table from database
     * @param string $table
     * @param boolean|array $truncate
     * @param string $type
     * @throws Exception
     */
    private function initSourceID ($table, $truncate = true, $type = "int(11) NOT NULL DEFAULT '0'") {
        static $tables = array();
        $DB = $this->db;
        if(!($tables[$table] = $DB::isSetDBTableColumnName($table, "source_id", array_key_exists($table, $tables)))) {
            if($truncate){
                $DB->Query("TRUNCATE TABLE `$table`");
                if(is_array($truncate)) {
                    // таблички
                    foreach($truncate['tables'] as $tbl => $tp) {
                        $this->initSourceID($tbl, true, $tp);
                    }
                    // пути 
                    foreach($truncate['folders'] as $path) {
                        if (($hndl = opendir($path))) {
                            while ($file = readdir($hndl)) {
                                if ( !($file == '.' || $file == '..' || $file == '.htaccess' || strpos($file, 'noimage') !== false)) {
                                    if (is_dir($path . DS . $file)) removeDir($path . DS . $file);
                                    else @unlink($path . DS . $file);
                                }
                            } closedir($hndl);
                        }
                    }
                }
            }
            if(!$DB->Query("ALTER TABLE `$table` ADD `source_id` {$type}, ADD INDEX `idx_source_id` (`source_id`)")) 
                throw new Exception('Не удалось добавить колонку source_id в таблицу ' . $table);
        }
    }
    /**
     * Prepare row
     * @param array $row
     * @return array
     */
    private function prepareRow($row){
        if($this->reader->getCharset() != $this->writer->getCharset()) {
            foreach($row as &$value) {
                $value = mb_convert_encoding($value, $this->writer->getCharset(), $this->reader->getCharset());
            }
        }
        return unScreenData($row);
    }
    /**
     * Prepare rows
     * @param array $rows
     * @return array
     */
    private function prepareRows($rows){
        foreach(array_keys($rows) as $key) {
            $rows[$key] = $this->prepareRow($rows[$key]);
        }
        return $rows;
    }
    /**
     * Check presence site domain or absence protocol in path
     * @param string $path
     * @return bool
     */
    private function isInternalLink($path) {
        return (strpos($path, $this->reader->getDomain()) !== false || substr($path, 0, 4) != 'http');
    }
    /**
     * Prepare file path - decode, translit, replace bad symbols
     * @param string $path
     * @return string
     */
    private function prepareFileUrl($path) {
        $path = urldecode($path);
        $parts = explode('/', $path);
        foreach($parts as &$part) {
            $part = setFilePathFormat($part);
        }
        return implode('/', $parts);
    }
    /**
     * Find and return from text array with keys images path and values new images path
     * @param text $text
     * @return array
     */
    private function getLinksFromText($text = '') {
        $dataLinks = array('links' => array(), 'images' => array(), 'garbage' => array());
        if($text) {            
            $links = array();   
            preg_match_all('/<(img|a)[^>]*?(src|href)=\"([^>]*?)\"[^>]*?>/is', $text, $links);
            if(!empty($links) && !empty($links[3])) {
                foreach($links[3] as $key => $link) {
                    $dataLinks['links'][] = $link;
                    $newlink = $this->prepareFileUrl($link);
                    if(strpos($newlink, $this->reader->getDomain()) !== false){
                        $newlink = preg_replace('/(https?:\/\/)?'. preg_quote($this->reader->getDomain()).'\/?/i', '/', $newlink);
                    }
                    if(strpos($newlink, UPLOAD_URL_DIR) !== false && $this->isInternalLink($newlink)) {
                        $dataLinks['images'][$link] = str_replace(UPLOAD_URL_DIR, UPLOAD_MEDIA_FILES_URL, $newlink);
                    } else if (strpos($link, 'file:///C:/Users') !== false) {
                        $dataLinks['garbage'][] = $links[0][$key];
                    }
                }
            }
        }
        return $dataLinks;
    }
    /**
     * 
     * @param string $module
     * @return array
     */
    private function getImagesParams($module) {
        $images_params = getRowItemsInKey('column', IMAGES_PARAMS_TABLE, '*', 'WHERE `module`="'.$module.'"');
        foreach($images_params as &$params) {
            $params["aliases"] = SystemComponent::prepareImagesParams($params["aliases"]);
        }
        return $images_params;
    }
    /**
     * 
     * @param text $text
     * @param array $images
     * @return text
     */
    private function replaceLinksInText($text = '', $dataLinks = array()) {
        if($text) {
            if($dataLinks['images']) {
                $text = str_replace(array_keys($dataLinks['images']), array_values($dataLinks['images']), $text);
            }
            if($dataLinks['garbage']) {
                $text = str_replace($dataLinks['garbage'], '', $text);
            }
        }
        return $text;
    }
    /**
     * Read colors from old database and save to current database
     * @param int $offset
     * @param int $limit
     */
    private function getAll ($offset, $limit) {
        $this->getColors($offset, $limit);
        $this->getSizes($offset, $limit);
        $this->getTypes($offset, $limit);
        $this->getNews($offset, $limit);
        $this->getStaticPages($offset, $limit);
        $this->getCategories($offset, $limit);
        $this->getProducts($offset, $limit);
    }
    /**
     * Read colors from old database and save to current database
     * @param int $offset
     * @param int $limit
     */
    private function getColors ($offset, $limit) {
        // инициализация 
        $this->initSourceID(DataWriter::TABLE_COLORS, true, "char(6) NOT NULL DEFAULT ''");
        // необходимые переменные
        $founded = $affected = $idx = 0;
        // получение данных
        $statement = $this->reader->colors($offset, $limit);        
        if($statement && ($founded = $statement->rowCount())) {
            while ($row = $statement->fetch()) {
                $data = $this->prepareRow($row);
                // приводим к требованиям текущей системы сеопуть
                $data['seo_path'] = $this->url->strToUrl($data['seo_path']);
                $data['order'] = ++$idx;
                $note = "\t-> " . $data['title'] . "\t-> source_id = {$data['source_id']}, id = ";
                $item = $this->writer->color($data);
                if ($item) {
                    // проверяем сеопуть и уникализируем его. Работаем с одинм языком
                    $seopath = $this->url->strToUniqueUrl($this->db, $data['seo_path'], self::MODULE_COLORS, DataWriter::TABLE_COLORS, $item['id'], false);
                    if($data['seo_path'] != $seopath) {
                        $this->writer->saveSeoPath($item['id'], $seopath, DataWriter::TABLE_COLORS);
                    }
                    $note .= $item['id'] ."\t-> успешно " . ($item['existID'] ? "обновлено" : "вставлено");
                    $affected++;
                } else {
                    $note .= "0\t-> не удалось записать";
                }
                if($this->print) print $note . PHP_EOL;
                if($this->debug) $this->logs[] = $note;
            }
        }
        $note = "Save colors: {$affected}/{$founded}";
        if($this->print) print $note . PHP_EOL;
        $this->logs[] = $note;
    }
    /**
     * Read sizes from old database and save to current database
     * @param int $offset
     * @param int $limit
     */
    private function getSizes ($offset, $limit) {
        // инициализация 
        $this->initSourceID(DataWriter::TABLE_SIZES, true, "char(5) NOT NULL DEFAULT ''");
        // необходимые переменные
        $founded = $affected = $idx = 0;
        // получение данных
        $statement = $this->reader->sizes($offset, $limit);        
        if($statement && ($founded = $statement->rowCount())) {
            while ($row = $statement->fetch()) {
                $data = $this->prepareRow($row);
                $data['order'] = ++$idx;
                $note = "\t-> " . $data['title'] . "\t-> source_id = {$data['source_id']}, id = ";
                $item = $this->writer->size($data);
                if ($item) {
                    $note .= $item['id'] ."\t-> успешно " . ($item['existID'] ? "обновлено" : "вставлено");
                    $affected++;
                } else {
                    $note .= "0\t-> не удалось записать";
                }
                if($this->print) print $note . PHP_EOL;
                if($this->debug) $this->logs[] = $note;
            }
        }
        $note = "Save sizes: {$affected}/{$founded}";
        if($this->print) print $note . PHP_EOL;
        $this->logs[] = $note;
    }
    /**
     * Read product types from old database and save to current database
     * @param int $offset
     * @param int $limit
     */
    private function getTypes ($offset, $limit) {
        // инициализация 
        $this->initSourceID(DataWriter::TABLE_TYPES, array(
            'tables' => array(
                DataWriter::TABLE_TYPE_SIZES => "char(8) NOT NULL DEFAULT ''", 
                DataWriter::TABLE_TYPE_IMAGES => "char(9) NOT NULL DEFAULT ''", 
            ),
            'folders' => array(
                UPLOAD_DIR.DS.Importer::MODULE_TYPES,
            )
        ), "char(6) NOT NULL DEFAULT ''");
        // необходимые переменные
        $founded = $affected = $idx = 0;
        // получение данных
        $statement = $this->reader->types($offset, $limit);        
        if($statement && ($founded = $statement->rowCount())) {
            while ($row = $statement->fetch()) {
                $data = $this->prepareRow($row);
                // приводим к требованиям текущей системы сеопуть
                $data['seo_path'] = $this->url->strToUrl($data['seo_path']);
                $data['order'] = ++$idx;
                $data["sizes"] = $this->reader->getTypeSizes($data['id']);
                $data["colors"] = $this->reader->getTypeColors($data['id']);
                $note = "\t-> " . $data['title'] . "\t-> source_id = {$data['source_id']}, id = ";
                $item = $this->writer->type($data, $this->reader->getSiteUrl().UPLOAD_URL_DIR.self::MODULE_CATALOG);
                if ($item) {
                    // проверяем сеопуть и уникализируем его. Работаем с одинм языком
                    $seopath = $this->url->strToUniqueUrl($this->db, $data['seo_path'], self::MODULE_TYPES, DataWriter::TABLE_TYPES, $item['id'], false);
                    if($data['seo_path'] != $seopath) {
                        $this->writer->saveSeoPath($item['id'], $seopath, DataWriter::TABLE_TYPES);
                    }
                    $note .= $item['id'] ."\t-> успешно " . ($item['existID'] ? "обновлено" : "вставлено");
                    if($this->debug && $item['errors']){
                        $note .= PHP_EOL . "Ошибки : ".PHP_EOL."\t\t->".implode(PHP_EOL."\t\t->",  $item['errors']);
                    }
                    $affected++;
                } else {
                    $note .= "0\t-> не удалось записать";
                }
                if($this->print) print $note . PHP_EOL;
                if($this->debug) $this->logs[] = $note;
            }
        }
        $note = "Save types: {$affected}/{$founded}";
        if($this->print) print $note . PHP_EOL;
        $this->logs[] = $note;
    }
    /**
     * Read news from old database and save to current database
     * @param int $offset
     * @param int $limit
     */
    private function getNews ($offset, $limit) {
        // инициализация 
        $this->initSourceID(DataWriter::TABLE_NEWS, true);
        // необходимые переменные
        $founded = $affected = 0;
        // получение данных
        $statement = $this->reader->news($offset, $limit);        
        if($statement && ($founded = $statement->rowCount())) {               
            // получаем из базы настройки картинок модуля
            $images_params = $this->getImagesParams(self::MODULE_NEWS);
            while ($row = $statement->fetch()) {
                $data = $this->prepareRow($row);
                // приводим к требованиям текущей системы сеопуть
                $data['seo_path'] = $this->url->strToUrl($data['seo_path']);
                // получаем картинки
                $dataLinks = $this->getLinksFromText($data['fulldescr']);
                 // замена в тексте путей изображений
                $data['fulldescr'] = $this->replaceLinksInText($data['fulldescr'], $dataLinks);
                $item = $this->writer->news($data, $dataLinks['images'], $images_params, $this->reader->getSiteUrl());
                $note= "\t-> " . $data['title'] . "\t-> изображений в тексте = ".count($dataLinks['images'])." \t-> мусора в тексте = ".count($dataLinks['garbage'])." \t-> source_id = {$data['source_id']}, id = ";
                if ($item) {
                    // проверяем сеопуть и уникализируем его. Работаем с одинм языком
                    $seopath = $this->url->strToUniqueUrl($this->db, $data['seo_path'], self::MODULE_NEWS, DataWriter::TABLE_NEWS, $item['id'], false);
                    if($data['seo_path'] != $seopath) {
                        $this->writer->saveSeoPath($item['id'], $seopath, DataWriter::TABLE_NEWS);
                    }
                    $note .= "{$item['id']}, image = {$item['image']} \t-> успешно " . ($item['existID'] ? "обновлено" : "вставлено");
                    $affected++;
                } else {
                    $note .= "0, image =  \t-> не удалось записать";
                }
                if($this->debug) {
                    $note .= PHP_EOL . 'links = '. trim(var_export($dataLinks, true)) .';';
                }
                if($this->print) print $note . PHP_EOL;
                if($this->debug) $this->logs[] = $note;
            }
        }
        $note = "Save news: {$affected}/{$founded}";
        if($this->print) print $note . PHP_EOL;
        $this->logs[] = $note;
    }
    /**
     * 
     * @param int $reader_pid
     * @param int $writer_pid
     * @param int $menutype
     * @param array $images_params
     * @param int $founded
     * @param int $affected
     * @param int $offset
     * @param int $limit
     */
    private function _getStaticPages($reader_pid, $writer_pid, $menutype, $images_params, &$founded = 0, &$affected = 0, $offset = 0, $limit = 0) {
        // получение данных
        $statement = $this->reader->staticPages($reader_pid, $offset, $limit);
        if($statement && ($found = $statement->rowCount())) { 
            $founded += $found;
            // получаем из базы настройки картинок модуля
            while ($row = $statement->fetch()) {
                $data = $this->prepareRow($row);
                // приводим к требованиям текущей системы сеопуть
                $data['seo_path'] = $this->url->strToUrl($data['seo_path']);
                // получаем картинки
                $dataLinks = $this->getLinksFromText($data['text']);
                 // замена в тексте путей изображений
                $data['text'] = $this->replaceLinksInText($data['text'], $dataLinks);
                $item = $this->writer->staticPage($data, $dataLinks['images'], $this->reader->getSiteUrl(), $writer_pid, $menutype);
                $note = "\t-> " . $data['title'] . "\t-> изображений в тексте = ".count($dataLinks['images'])." \t-> мусора в тексте = ".count($dataLinks['garbage'])." \t-> source_id = {$data['source_id']}, id = ";
                if ($item) {
                    // проверяем сеопуть и уникализируем его. Работаем с одинм языком
                    $seopath = $this->url->strToUniqueUrl($this->db, $data['seo_path'], 'category', DataWriter::TABLE_CATEGORIES, $item['id'], false);
                    if($data['seo_path'] != $seopath) {
                        $this->writer->saveSeoPath($item['id'], $seopath, DataWriter::TABLE_CATEGORIES);
                    }
                    $note .= "{$item['id']}, image = {$item['image']} \t-> успешно " . ($item['existID'] ? "обновлено" : "вставлено");
                    $affected++;
                } else {
                    $note .= "0, image =  \t-> не удалось записать";
                }
                if($this->debug) {
                    $note .= PHP_EOL . 'links = '. trim(var_export($dataLinks, true)) .';';
                }
                if($this->print) print $note . PHP_EOL;
                if($this->debug) $this->logs[] = $note;
                if($item) {
                    //запускаем шарманку для дочерних элементов          
                    $this->_getStaticPages($item['source_id'], $item['id'], $menutype, $images_params, $founded, $affected);
                }
            }
        }
    }
    /**
     * Read category static pages "Печать ЛОГО" from old database and save to current database
     * @param int $offset
     * @param int $limit
     */
    private function getStaticPages ($offset, $limit) {
        // инициализация 
        $this->initSourceID(DataWriter::TABLE_CATEGORIES, false);  
        $this->_getStaticPages(DataReader::ROOT_STATIC_ID, DataWriter::ROOT_STATIC_ID, 1, $this->getImagesParams(self::MODULE_STATIC), $founded, $affected, $offset, $limit);
        $note = "Save static pages: {$affected}/{$founded}";
        if($this->print) print $note . PHP_EOL;
        $this->logs[] = $note;
    }
    /**
     * 
     * @param int $reader_pid
     * @param int $writer_pid
     * @param int $menutype
     * @param array $images_params
     * @param int $founded
     * @param int $affected
     * @param int $offset
     * @param int $limit
     */
    private function _getCategories($reader_pid, $writer_pid, $menutype, $images_params, &$founded = 0, &$affected = 0, $offset = 0, $limit = 0) {
        // получение данных
        $statement = $this->reader->categories($reader_pid, $offset, $limit);
        if($statement && ($found = $statement->rowCount())) { 
            $founded += $found;     
            // получаем из базы настройки картинок модуля
            while ($row = $statement->fetch()) {
                $data = $this->prepareRow($row);
                // приводим к требованиям текущей системы сеопуть
                $data['seo_path'] = $this->url->strToUrl($data['seo_path']);
                // получаем картинки
                $dataLinks = $this->getLinksFromText($data['text']);
                 // замена в тексте путей изображений
                $data['text'] = $this->replaceLinksInText($data['text'], $dataLinks);
                $item = $this->writer->category($data, $dataLinks['images'], $this->reader->getSiteUrl(), $writer_pid, $menutype);
                $note = "\t-> " . $data['title'] . "\t-> изображений в тексте = ".count($dataLinks['images'])." \t-> мусора в тексте = ".count($dataLinks['garbage'])." \t-> source_id = {$data['source_id']}, id = ";
                if ($item) {
                    // проверяем сеопуть и уникализируем его. Работаем с одинм языком
                    $seopath = $this->url->strToUniqueUrl($this->db, $data['seo_path'], 'category', DataWriter::TABLE_CATEGORIES, $item['id'], false);
                    if($data['seo_path'] != $seopath) {
                        $this->writer->saveSeoPath($item['id'], $seopath, DataWriter::TABLE_CATEGORIES);
                    }
                    $note .= "{$item['id']}, image = {$item['image']} \t-> успешно " . ($item['existID'] ? "обновлено" : "вставлено");
                    $affected++;
                } else {
                    $note .= "0, image =  \t-> не удалось записать";
                }
                if($this->debug) {
                    $note .= PHP_EOL . 'links = '. trim(var_export($dataLinks, true)) .';';
                }
                if($this->print) print $note . PHP_EOL;
                if($this->debug) $this->logs[] = $note;
                if($item) {
                    //запускаем шарманку для дочерних элементов          
                    $this->_getCategories($item['source_id'], $item['id'], $menutype, $images_params, $founded, $affected);
                }
            }
        }
    }
    /**
     * Read product categories from old database and save to current database
     * @param int $offset
     * @param int $limit
     */
    private function getCategories ($offset, $limit) {
        // инициализация 
        $this->initSourceID(DataWriter::TABLE_CATEGORIES, false);  
        $this->_getCategories(DataReader::ROOT_CATEGORY_ID, DataWriter::ROOT_CATEGORY_ID, 1, $this->getImagesParams(self::MODULE_STATIC), $founded, $affected, $offset, $limit);
        $note = "Save static pages: {$affected}/{$founded}";
        if($this->print) print $note . PHP_EOL;
        $this->logs[] = $note;
    }
    /**
     * Read products from old database and save to current database
     * @param int $offset
     * @param int $limit
     */
    private function getProducts ($offset, $limit) {
        // инициализация 
        $this->initSourceID(DataWriter::TABLE_PRINTS, array(
            'tables' => array(
                DataWriter::TABLE_PRINT_FILES => "int(11) NOT NULL DEFAULT '0'",
                DataWriter::TABLE_PRINT_ASSORT => "varchar(255) NOT NULL DEFAULT ''", 
                DataWriter::TABLE_PRINT_ASSORT_COLORS => "varchar(255) NOT NULL DEFAULT ''", 
                DataWriter::TABLE_PRINT_ASSORT_SETTINGS => "varchar(255) NOT NULL DEFAULT ''", 
            ),
            'folders' => array(
                UPLOAD_DIR.DS.Importer::MODULE_PRINTS,
            )
        ));
        // необходимые переменные
        $founded = $affected = $idx = 0;
        // получение данных
        $statement = $this->reader->products($offset, $limit);        
        if($statement && ($founded = $statement->rowCount())) {        
            // получаем категории принтов
            $arCategories = getRowItemsInKey('source_id', DataWriter::TABLE_CATEGORIES, '`id`, `source_id`, `product_seo_title`, `product_meta_descr`, `product_meta_key`', 'WHERE `module`="'.self::MODULE_PRINTS.'"');
            // получаем все типы
            $arTypes = getRowItemsInKey('source_id', DataWriter::TABLE_TYPES, '`id`, `source_id`, `active`');
            // получаем все цвета
            $arColorsIDX = getRowItemsInKeyValue('source_id', 'id', DataWriter::TABLE_COLORS, '`id`, `source_id`');
            while ($row = $statement->fetch()) {
                $data = $this->prepareRow($row);
                // приводим к требованиям текущей системы сеопуть
                $data['seo_path'] = $this->url->strToUrl($data['seo_path']);
                // добавляем файлы и ассортименты
                $data['files'] = $this->reader->getProductFiles($data['source_id']);
                $data['assortment'] = $this->reader->getProductAssortment($data['source_id'], $data['seo_path']);
                // получаем картинки
                $dataLinks = $this->getLinksFromText($data['text']);
                 // замена в тексте путей изображений
                $data['text'] = $this->replaceLinksInText($data['text'], $dataLinks);
                $item = $this->writer->product($data, $dataLinks['images'], $this->reader->getSiteUrl(), $arCategories, $arTypes, $arColorsIDX);
                $note= "\t-> " . $data['title'] . "\t-> изображений в тексте = ".count($dataLinks['images'])." \t-> мусора в тексте = ".count($dataLinks['garbage'])." \t-> source_id = {$data['source_id']}, id = ";
                if ($item) {
                    // проверяем сеопуть и уникализируем его. Работаем с одинм языком
                    $seopath = $this->url->strToUniqueUrl($this->db, $data['seo_path'], self::MODULE_PRINTS, DataWriter::TABLE_PRINTS, $item['id'], false);
                    if($data['seo_path'] != $seopath) {
                        $this->writer->saveSeoPath($item['id'], $seopath, DataWriter::TABLE_PRINTS);
                    }
                    // проверяем сеопуть ассортиментов и уникализируем его. Работаем с одинм языком
                    foreach($item['assort'] as $assort) {
                        $seopath = $this->url->strToUniqueUrl($this->db, $assort['seo_path'], self::MODULE_PRINTS, DataWriter::TABLE_PRINT_ASSORT, $assort['id'], false);
                        if($assort['seo_path'] != $seopath) {
                            $this->writer->saveSeoPath($assort['id'], $seopath, DataWriter::TABLE_PRINT_ASSORT);
                        }
                    }
                    $note .= "{$item['id']} \t-> успешно " . ($item['existID'] ? "обновлено" : "вставлено");
                    $affected++;
                } else {
                    $note .= "0, image =  \t-> не удалось записать";
                }
                if($this->debug) {
                    $note .= PHP_EOL . 'links = '. trim(var_export($dataLinks, true)) .';';
                }
                if($this->print) print $note . PHP_EOL;
                if($this->debug) $this->logs[] = $note;
            }
        }
        $note = "Save print: {$affected}/{$founded}";
        if($this->print) print $note . PHP_EOL;
        $this->logs[] = $note;
    }
    
    /**
     * Drop Test Categories
     */
    private function getDropTestData(){
        // Удаление категорий
        $idSet = array(); $categories = array(DataWriter::ROOT_CATEGORY_ID);
        while(NULL !== ($parentID = array_shift($categories))) {
            foreach($this->writer->categories($parentID) as $row) {
                if(empty($row['source_id'])){
                    $idSet[] = $row['id'];
                }
                $categories[] = $row['id'];
            }
        };
        $affected = 0;
        if($idSet) {
            $affected += $this->writer->dropCategories($idSet);
        }
        return $affected;
    }
    
    
}

/*
 * Description of DataImport
 *
 * @author weblife
 */
abstract class DataImport {
    
    const QUERY_LIMIT = 1000; // лимит на получение записей в БД
    const ATTRIBUTE_SEPARATOR = ","; // Строчный разделитель атрибутов
    
    protected $DB; // Database connector
    protected $charset; // Text charset
    protected $site_url = ''; //site url
    
    /**
     * Создание объекта с подключением к нужной БД
     * @param string $db_name
     * @param string $db_host
     * @param string $db_user
     * @param string $db_password
     * @param string $db_charset
     * @throws Exception
     */
    public function __construct ($site_url, $db_name, $db_host = "localhost", $db_user = "root", $db_password = "", $db_charset="cp1251") {
        $this->site_url = $site_url;
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
        $opt = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_charset}"
        );
        try {
            $this->DB = new PDO($dsn, $db_user, $db_password, $opt);
            $this->charset = $db_charset;
        } catch (PDOException $e) {
            throw new Exception('Подключение к базе данных в классе '.get_class($this).' не удалось: ' . $e->getMessage());
        }
    }    
    /**
     * 
     * @return string
     */
    public function getCharset() {
        return $this->charset;
    }    
    /**
     * 
     * @return string
     */
    public function getSiteUrl() {
        return $this->site_url;
    }    
    /**
     * 
     * @return string
     */
    public function getDomain() {
        return str_replace(array('http://', 'https://', '/'), '', $this->site_url);
    }
    /**
     * Get row data
     * @param string $columns
     * @param string $table
     * @param string $conditions
     * @return array
     */
    protected function getRow($columns, $table, $conditions){
        $where = $conditions ? " WHERE {$conditions} " : '';
        $statement = $this->DB->query("SELECT {$columns} FROM `{$table}`{$where}LIMIT 1");
        return (($statement && $statement->rowCount()) ? $statement->fetch() : null);
    }    
    /**
     * Set limit statement to query
     * @param string $query
     * @param int $offset
     * @param int $limit
     * @return string
     */
    protected function setQueryLimit($query, $offset, $limit){
        if($offset > 0 && empty($limit)) $limit = self::QUERY_LIMIT;
        if ($limit > 0) 
            $query .= " LIMIT {$offset}, {$limit}";
        return $query;
    }    
    /**
     * Prepare insert query string
     * @param string $table
     * @param array $columns
     * @return string
     */
    protected function prepareInsertRowQuery($table, array $columns){
        $keys = $values = array();
        foreach($columns as $column) {
            $keys[] = "`{$column}`";
            $values[] = ":{$column}";
        }
        return "INSERT INTO `{$table}` (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
    }    
    /**
     * Prepare update query string
     * @param string $table
     * @param array $columns
     * @param int $rowID
     * @return string
     */
    protected function prepareUpdateRowQuery($table, array $columns, $rowID){
        $set = array();
        foreach($columns as $column) {
            $set[] = "`{$column}`=:{$column}";
        }
        return "UPDATE `{$table}` SET ".implode(', ', $set)." WHERE `id`='{$rowID}' LIMIT 1";
    }    
    /**
     * Save row in $table with $values and $rowID
     * @param string $table
     * @param array $values where key is column name and value is insert value
     * @param int $rowID if empty - insert else update
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    protected function saveRow($table, array & $values, $rowID = 0){
        $insert = empty($rowID);
        $columns = array_keys($values);
        $statement = $this->DB->prepare($insert ? $this->prepareInsertRowQuery($table, $columns) : $this->prepareUpdateRowQuery($table, $columns, $rowID));
        if($statement) {
            foreach($columns as $column) {
                // преобразовываем html сущности 
                $values[$column] = screenData($values[$column], false);
                // связываем
                $statement->bindParam(":{$column}", $values[$column]);
            }
            if($statement->execute()){
                $values['existID'] = $rowID;
                $values['id'] = $insert ? $this->DB->lastInsertId() : $rowID;
                return true;
            }
        } return false;
    }
    /**
     * 
     * @param string $filename
     * @return boolean
     */
    public static function existsRemoteFile($filename) {
        $headers = @get_headers($filename);
        foreach ($headers as $header) {
            if (strpos($header, '404 Not Found') !== false){
                return false;
            }
        }
        return true;
    }
}

/*
 * DataReader считывает данные из базы старого сайта
 *
 * @author weblife
 */
class DataReader extends DataImport {
    const ROOT_CATEGORY_ID       = 9; // ID верхней категории товаров "Футболки с принтом"
    const ROOT_STATIC_ID         = 10; // ID верхней категории "Печать ЛОГО"
    const ROOT_NEWS_ID           = 13; // ID раздела "Новости"
    /**
     * Таблицы в старой БД
     */
    const TABLE_COLORS          = "pcolors"; // Цвета
    const TABLE_SIZES           = "psizes"; // Размеры
    const TABLE_TYPES           = "ptypes"; // Типы товаров
    const TABLE_TYPE_SIZES      = "ptypes_sizes"; // Размеры типов товаров
    const TABLE_TYPE_COLORS     = "ptypes_colors"; // Цвета типов товаров
    const TABLE_NEWS            = "ru_news"; // Новости
    const TABLE_CATEGORIES      = "ru_main"; // Категории
    const TABLE_PRINTS          = "ru_catalog"; // Товары
    const TABLE_PRINT_PRICES    = "pprices"; // Цены
    const TABLE_PRINT_SEOPATHES = "catalog_seopathes"; // сеотупи
    const TABLE_PRINT_FILES     = "pfiles"; // Файлы
    /**
     * Read rows from old database
     * @param string $query
     * @param string $column key column name. If empty - set increment integer key
     * @return array
     */
    public function getRows ($query, $column = "") {
        $rows = array();
        if(($statement = $this->DB->query($query))) {
            while ($row = $statement->fetch()) {
                if($column) 
                     $rows[$row[$column]] = $row;
                else $rows[] = $row;
            }
        }
        return $rows;
    }
    /**
     * Read colors from old database
     * @param int $offset
     * @param int $limit
     * @return PDOStatement
     */
    public function colors ($offset = 0, $limit = 0) {
        $query  = "SELECT `title`, `title` `seo_path`, `code` `hex`, `order`, `code` `source_id` FROM `".self::TABLE_COLORS."` ORDER BY `title`";
        $query = $this->setQueryLimit($query, $offset, $limit);
        return $this->DB->query($query);
    }
    /**
     * Read sizes from old database
     * @param int $offset
     * @param int $limit
     * @return PDOStatement
     */
    public function sizes ($offset = 0, $limit = 0) {
        $query  = "SELECT `code` `title`, `code` `source_id` FROM `".self::TABLE_SIZES."`";
        $query = $this->setQueryLimit($query, $offset, $limit);
        return $this->DB->query($query);
    }
    /**
     * Read product types from old database
     * @param int $offset
     * @param int $limit
     * @return PDOStatement
     */
    public function types ($offset = 0, $limit = 0) {
        $query  = "SELECT *, `id` `source_id` FROM `".self::TABLE_TYPES."`";
        $query = $this->setQueryLimit($query, $offset, $limit);
        return $this->DB->query($query);
    }
    /**
     * Read product type sizes rows from old database
     * @param int $typeID
     * @return array
     */
    public function getTypeSizes ($typeID) {
        $query  = "SELECT *, CONCAT(`typeid`, '|', `sizecode`) `source_id` FROM `".self::TABLE_TYPE_SIZES."` WHERE `typeid`={$typeID} ORDER BY `order`";
        return $this->getRows($query, "source_id");
    }
    /**
     * Read product type colors rows from old database
     * @param int $typeID
     * @return array
     */
    public function getTypeColors ($typeID) {
        $query  = "SELECT *, CONCAT(`typeid`, '|', `colorcode`) `source_id` FROM `".self::TABLE_TYPE_COLORS."` WHERE `typeid`={$typeID} ORDER BY `order`";
        return $this->getRows($query, "source_id");
    }
    /**
     * Read news from old database
     * @param int $offset
     * @param int $limit
     * @return PDOStatement
     */
    public function news ($offset = 0, $limit = 0) {
        $query  = "SELECT `title`, `descr`, `fulldescr`, `image`, `meta_descr`, `meta_key`, `meta_robots`, `seo_path`, `seo_title`, `order`, `active`, `created`, `modified`, `id` `source_id` FROM `".self::TABLE_NEWS."` ORDER BY `id`";
        $query = $this->setQueryLimit($query, $offset, $limit);
        return $this->DB->query($query);
    }
    /**
     * Read category static pages "Печать ЛОГО" from old database
     * @param int $parentID
     * @return PDOStatement
     */
    public function staticPages ($parentID, $offset = 0, $limit = 0) {
        $query  = "SELECT `pid`, `redirecturl`, `title`, `text`, `descr`, `image`, `menu_icon` `image_icon`, `pagetype`, `meta_descr`, `meta_key`, `meta_robots`, `seo_path`, `seo_title`, `seo_text`, `order`, `active`, `access`, `created`, `modified`, `id` `source_id` FROM `".self::TABLE_CATEGORIES."` WHERE pid={$parentID} ORDER BY `id`";
        $query = $this->setQueryLimit($query, $offset, $limit);
        return $this->DB->query($query);
    }
    /**
     * Read product categories from old database
     * @param int $parentID
     * @return PDOStatement
     */
    public function categories ($parentID, $offset = 0, $limit = 0) {       
        $query  = "SELECT `pid`, `redirecturl`, `title`, `text`, `descr`, `image`, `menu_icon` `image_icon`, `pagetype`, `meta_descr`, `meta_key`, `meta_robots`, `seo_path`, `seo_title`, `seo_text`, `order`, `active`, `access`, `created`, `modified`, `product_seo_title`, `product_meta_descr`, `product_meta_key`, `id` `source_id` FROM `".self::TABLE_CATEGORIES."` WHERE pid={$parentID} ORDER BY `id`";
        $query = $this->setQueryLimit($query, $offset, $limit);
        return $this->DB->query($query);
    }
    /**
     * Read products from old database
     * @param int $offset
     * @param int $limit
     * @return PDOStatement
     */
    public function products ($offset = 0, $limit = 0) {
        $query  = "SELECT `cid`, `deftypeid`, `pcode`, `title`, IF(LENGTH(`fulldescr`)>0, `fulldescr`, `descr`) `text`, `meta_descr`, `meta_key`, `meta_robots`, `seo_path`, `seo_title`, `order`, `active`, `created`, `modified`, `id` `source_id` FROM `".self::TABLE_PRINTS."` ORDER BY `id`";
        $query = $this->setQueryLimit($query, $offset, $limit);
        return $this->DB->query($query);
    }
    /**
     * Read product files
     * @param type $productID
     * @return PDOStatement
     */
    public function getProductFiles($productID) {
        $query  = "SELECT `filename`, `active`, `id` `source_id` FROM `".self::TABLE_PRINT_FILES."` WHERE `productid`={$productID} ORDER BY `active` DESC, `isdefault` DESC, `id`";
        return $this->getRows($query, "source_id");
    }
    /**
     * Read product assortment - price, file position and so on
     * @param type $productID
     * @param type $productSeoPath
     * @return PDOStatement
     */
    public function getProductAssortment($productID, $productSeoPath) {
        $assort = array();        
        $query  = "SELECT a.`typeid`, MAX(a.`price`) `price`, " . PHP_EOL
                . "   IFNULL(s.`seo_path`, CONCAT(t.`seo_path`, '-{$productSeoPath}') ) `seo_path`, " . PHP_EOL
                . "   IFNULL((SELECT `colorcode` FROM `".self::TABLE_PRINT_PRICES."` WHERE `productid`={$productID} AND `typeid`=a.`typeid` ORDER BY `isdefault` DESC LIMIT 1), '') `colorcode`, " . PHP_EOL
                . "   CONCAT_WS('|', `productid`, `typeid`) `source_id` " . PHP_EOL
                . "FROM `".self::TABLE_PRINT_PRICES."` a " . PHP_EOL
                . "LEFT JOIN `".self::TABLE_PRINT_SEOPATHES."` s ON s.`pid`=a.`productid` AND s.`tid`=a.`typeid` " . PHP_EOL
                . "LEFT JOIN `".self::TABLE_TYPES."` t ON t.`id`=a.`typeid` " . PHP_EOL
                . "WHERE a.`productid`={$productID} GROUP BY a.`typeid`";
        $rows = $this->getRows($query, "source_id");
        foreach ($rows as $row) {
            $query  = "SELECT GROUP_CONCAT(`colorcode`) `colors`, `fileid`, MAX(`width`) `width`, MAX(`height`) `height`, MAX(`ypos`) `ypos`, CONCAT_WS('|', `productid`, `typeid`, `fileid`) `source_id` FROM `".self::TABLE_PRINT_PRICES."` WHERE `productid`={$productID} AND `typeid`={$row['typeid']} GROUP BY `fileid`";
            $row['settings'] = $this->getRows($query, "source_id");          
            $assort[$row['typeid']] = $row;
        }
        return $assort;
    }
}

/*
 * DataWriter записывает данные в базу нового сайта
 *
 * @author weblife
 */
class DataWriter extends DataImport {
    const ROOT_CATEGORY_ID       = 9; // ID верхней категории, куда импортируем товары "Футболки с принтом"
    const ROOT_STATIC_ID         = 10; // ID верхней категории, куда импортируем статические страницы из "Печать ЛОГО"
    const ROOT_NEWS_ID           = 13; // ID раздела "Новости"
    /**
     * Таблицы в новой БД
     */
    const TABLE_COLORS          = "ru_colors"; // Цвета
    const TABLE_SIZES           = "ru_sizes"; // Размеры
    const TABLE_TYPES           = "ru_substrates"; // Типы товаров
    const TABLE_TYPE_SIZES      = "substrates_sizes"; // Размеры типов товаров
    const TABLE_TYPE_IMAGES     = "substrates_images"; // Изображения типов товаров
    const TABLE_NEWS            = "ru_news"; // Новости
    const TABLE_CATEGORIES      = "ru_main"; // Категории
    const TABLE_PRINTS          = "ru_prints"; // Товары      
    const TABLE_PRINT_FILES     = "ru_printfiles"; //Файлы принтов
    const TABLE_PRINT_ASSORT    = "print_assortment"; //Асортименты принтов
    const TABLE_PRINT_ASSORT_COLORS = "print_assortment_colors"; //Цвета асортиментов принтов
    const TABLE_PRINT_ASSORT_SETTINGS = "print_assortment_settings"; //Цвета асортиментов принтов
    /**
     * Save prepared item to $table
     * @param string $table
     * @param array $itemrow where key is column name and value is insert value
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    private function saveItem($table, array & $itemrow){
        return (parent::saveRow($table, $itemrow, $this->getExistID($itemrow["source_id"], $table)) ? $itemrow : null);
    }    
    /**
     * Get exist id from table where `source_id`=$sourceID
     * @param int $sourceID
     * @param string $table
     * @param string $column
     * @return int
     */
    private function getExistID($sourceID, $table, $column = 'id'){
        $statement = $this->DB->query("SELECT `{$column}` FROM `{$table}` WHERE `source_id`='{$sourceID}' LIMIT 1");
        return (($statement && $statement->rowCount()) ? $statement->fetchColumn() : 0);
    }   
    /**
     * Get exist row from table where `source_id`=$sourceID
     * @param int $sourceID
     * @param string $table
     * @param string $columns
     * @return array
     */
    private function getExistRow($sourceID, $table, $columns = '*'){
        return $this->getRow($columns, $table, "`source_id`='{$sourceID}'");
    }
    /**
     * Read product categories from new database
     * @param int $parentID
     * @return array
     */
    public function categories ($parentID) {       
        $query  = "SELECT * FROM `".self::TABLE_CATEGORIES."` WHERE pid={$parentID} ORDER BY `id`";
        $statement = $this->DB->query($query);
        return ($statement ? $statement->fetchAll() : array());
    }
    /**
     * Read product categories from new database
     * @param int $parentID
     * @return array
     */
    public function dropCategories ($idSet) {
        $affected = 0;
        $query = "DELETE FROM `shortcuts` WHERE `cid` IN (".implode(',', $idSet).")";
        $affected += $this->DB->prepare($query)->execute();
        
        $query = "DELETE FROM `category_attributes` WHERE `cid` IN (".implode(',', $idSet).")";
        $affected += $this->DB->prepare($query)->execute();
        
        $query = "DELETE FROM `category_attribute_groups` WHERE `cid` IN (".implode(',', $idSet).")";
        $affected += $this->DB->prepare($query)->execute();
        
        $query = "DELETE FROM `category_filters` WHERE `cid` IN (".implode(',', $idSet).")";
        $affected += $this->DB->prepare($query)->execute();
        
        $query = "DELETE FROM `".DataWriter::TABLE_CATEGORIES."` WHERE `id` IN (".implode(',', $idSet).")";
        $affected += $this->DB->prepare($query)->execute();
        return $affected;
    }
    /**
     * Get exist id from table where `source_id`=$sourceID
     * @param int $id
     * @param string $seopath
     * @param string $table
     * @return int
     */
    public function saveSeoPath($id, $seopath, $table){
        $values = array('seo_path' => $seopath);
        return ($id ? $this->saveRow($table, $values, $id) : false);
    }
    /**
     * 
     * @param WideImage_TrueColorImage $handle
     * @param array $images_params
     * @param string $destpath
     * @param string $filename
     * @return bool
     */
    public function saveModuleImage($filename, $module, $images_params, $resource_baseurl, $resource_url = '') {
        $errors = array();
        $module_path = UPLOAD_URL_DIR.$module.'/';
        $resource_url = $resource_baseurl.($resource_url ? $resource_url : $module_path.$filename);
        if($this->existsRemoteFile($resource_url)) {
            try {
                if(($image = WideImage::loadFromFile($resource_url))) {  
                    $canvas = $image->copy();
                    if (!empty($images_params["crop_height"]) and !empty($images_params["crop_width"])) {
                        $canvas->resize($images_params["crop_width"], $images_params["crop_height"], "outside", "down")
                               ->resizeCanvas($images_params["crop_width"], $images_params["crop_height"], "center", "middle", $canvas->allocateColor(255,255,255));
                    } elseif (!empty($images_params["max_height"]) and !empty($images_params["max_width"])) {
                        $canvas->resize($images_params["max_width"], $images_params["max_height"]);
                    }
                    $canvas->saveToFile(WLCMS_ABS_ROOT.$module_path.$filename);
                    if (!empty($images_params["aliases"])) {
                        foreach ($images_params["aliases"] as $param) {
                            list($alias, $w, $h) = $param;
                            $cropped = $canvas->resize($w, $h, "outside", "down");
                            $cropped->saveToFile(WLCMS_ABS_ROOT.$module_path.$alias.$filename);
                        }
                    }
                } else {
                    $errors[] = 'Ошибка считывания WideImage\'ом файла "'.$resource_url.'"';
                }
            } catch (Exception $exc) {
                $errors[] = 'Ошибка сохранения WideImage\'ом файла/файлов "'.$resource_url.'" по пути "'.$module_path.$filename.'": '.$exc->getMessage();
            }
        } else {
            $errors[] = 'Ошибка получения файла "'.$resource_url.'"';
        }
        return $errors;
    }    
    /**
     * 
     * @param array $images
     */
    public function saveMediaImages($images, $resource_baseurl) {
        $errors = array();
        foreach($images as $oldpath => $newpath) {                    
            if($this->existsRemoteFile($resource_baseurl.$oldpath)) {   
                try {
                    if(($image = WideImage::loadFromFile($resource_baseurl.$oldpath))) {  
                        $filename = basename($newpath);
                        $filepath = cleanDirPath(dirname($newpath));
                        if($filepath && (is_dir($filepath) || mkdir($filepath, 0775, true))) {
                            $image->saveToFile(WLCMS_ABS_ROOT.$filepath.DS.$filename);
                        } else {
                            $errors[] = 'Ошибка в пути "'.$newpath.'('.$filepath.')" для сохранения файла "'.$resource_baseurl.$oldpath.'"';
                        }
                    } else {
                        $errors[] = 'Ошибка считывания WideImage\'ом файла "'.$resource_baseurl.$oldpath.'"';
                    }
                } catch (Exception $exc) {
                    $errors[] = 'Ошибка сохранения WideImage\'ом файла "'.$resource_baseurl.$oldpath.'" по пути "'.$newpath.'": '.$exc->getMessage();
                }
            } else {
                $errors[] = 'Ошибка получения файла по пути "'.$resource_baseurl.$oldpath.'" или удаленный файл не существует!';
            }
        }
        return $errors;
    }
    /**
     * Write color into new database
     * @param array $data
     * @return array
     */
    public function color ($data) {
        return $this->saveItem(self::TABLE_COLORS, $data);
    }
    /**
     * Write size into new database
     * @param array $data
     * @return array
     */
    public function size ($data) {
        return $this->saveItem(self::TABLE_SIZES, $data);
    }
    /**
     * Write type into new database
     * @param array $data
     * @param string $source_dir_url
     * @return array
     */
    public function type ($data, $source_dir_url) {
        static $sizes = array(), $colors = array();
        // получаем массивы по разположению логотипа
        // старые размеры 345*372, новые размеры 580*625
        $rate = round(580/345, 2);
        $dimensions = PrintProduct::getDimensions();
        $dimensions[PrintProduct::SIDE_FRONT] = PrintProduct::getDimension(round($data['width']*$rate), round($data['ypos']*$rate));
        // получаем краткое название
        $shortName = $data['label'];
        if(mb_strpos($shortName, ' ', 0, $this->charset) !== false){
            $shortName = trim(str_replace(array('Мужская', 'Женская', 'Детская', 'Детский', 'для девочки'), '', $shortName));
            $shortName = mb_convert_case($shortName, MB_CASE_TITLE, $this->charset);
        }
        // перестраиваем данные для сохранения
        $itemData = array (
            'title' => $data['label'],
            'title_s' => $data['label'],
            'title_p' => $data['title'],
            'title_short' => $shortName,
            'price' => $data['price'],
            'dimensions' => PrintProduct::dimensionsToDB($dimensions),
            'seo_path' => $data['seo_path'],
            'seo_title' => $data['label'],
            'order' => $data['order'],
            'active' => $data['active'],
            'source_id' => $data['source_id'],
        );
        // если все ок - то сохраняем дополнительные данные
        if(($item = $this->saveItem(self::TABLE_TYPES, $itemData))) {
            // создаем массив для будущих ошибок
            $item['errors'] = $item['sizes'] = $item['colors'] = array();
            // копируем распакованный массив
            $item['dimensions'] = $dimensions;
            // сохраняем размеры
            foreach($data['sizes'] as $sourceID => $sizerow){
                // получаем новый идентификатор размера
                if(isset($sizes[$sizerow['sizecode']])) {
                    $sizeID = $sizes[$sizerow['sizecode']];
                } else {
                    $sizeID = $sizes[$sizerow['sizecode']] = $this->getExistID($sizerow['sizecode'], self::TABLE_SIZES);
                }
                if($sizeID) {
                    $sizeData = array(
                        'substrate_id' => $item['id'],
                        'size_id' => $sizeID,
                        'source_id' => $sizerow['source_id'],
                    );
                    $item['sizes'][$sourceID] = $this->saveItem(self::TABLE_TYPE_SIZES, $sizeData);
                } else {
                    $item['errors'][] = 'Размер с кодом ' . $sizerow['sizecode'] . ' не найден в новой базе данных';
                }
            }
            // сохраняем цвета с картинками
            $itemName = empty($item['seo_path']) ? $item['title'] : $item['seo_path'];
            $destPath = WLCMS_ABS_ROOT.UPLOAD_DIR.DS.Importer::MODULE_TYPES.DS;
            foreach($data['colors'] as $sourceID => $colorow){
                // получаем новый идентификатор размера
                if(isset($colors[$colorow['colorcode']])) {
                    $color = $colors[$colorow['colorcode']];
                } else {
                    $color = $colors[$colorow['colorcode']] = $this->getExistRow($colorow['colorcode'], self::TABLE_COLORS);
                }
                if($color){
                    $colorName = empty($color['seo_path']) ? $color['title'] : $color['seo_path'];
                    $imageName = setFilePathFormat(PrintProduct::createSubstrateColorFileName($item['id'], $color['id'], PrintProduct::SIDE_FRONT, $itemName.PrintProduct::SEOTEXT_SEP.$colorName)).".jpg";
                    $colorData = array (
                        'substrate_id' => $item['id'],
                        'color_id' => $color['id'],
                        'img_'.PrintProduct::SIDE_FRONT => $imageName,
                        'order' => $colorow['order'],
                        'source_id' => $colorow['source_id'],
                    );
                    // копируем изображение
                    $resource_url = $source_dir_url.'/'.$data['folder'].'/big/'.$colorow['image'];
                    if($this->existsRemoteFile($resource_url)) {
                        try {
                            if(($image = WideImage::loadFromFile($resource_url))) {
                                $canvas = WideImage::createTrueColorImage(580, 625);
                                $canvas->fill(0, 0, $canvas->allocateColor(255,255,255));
                                $canvas
                                    ->merge($image->resizeUp(580, 625), 0, 0)
                                    ->saveToFile($destPath.$imageName);
                                $item['colors'][$sourceID] = $this->saveItem(self::TABLE_TYPE_IMAGES, $colorData);
                            } else {
                                $item['errors'][] = 'Ошибка считывания WideImage\'ом файла "'.$resource_url.'"';
                            }
                        } catch (Exception $exc) {
                            $item['errors'][] = 'Ошибка сохранения WideImage\'ом файла/файлов "'.$resource_url.'" по пути "'.$destPath.$imageName.'": '.$exc->getMessage();
                        }
                    } else {
                        $item['errors'][] = 'Ошибка получения файла "'.$resource_url.'"';
                    }
                } else {
                    $item['errors'][] = 'Цвет с кодом ' . $sizerow['colorcode'] . ' не найден в новой базе данных';
                }
            }
        }
        return $item;
    }
    /**
     * Write news into new database
     * @param array $data
     * @param array $images
     * @param array $images_params
     * @param string $reader_baseurl
     * @return array
     */
    public function news($data, $images, $images_params, $reader_baseurl) { 
        $data['cid'] = self::ROOT_NEWS_ID;
        if(($item = $this->saveItem(self::TABLE_NEWS, $data))) {
            // создаем массив для будущих ошибок
            $item['errors'] = array();
            if($images) {
                $item['errors'] = array_merge($item['errors'], $this->saveMediaImages($images, $reader_baseurl));  
            }
            if($item['image']) {
                $item['errors'] = array_merge($item['errors'], $this->saveModuleImage($item['image'], Importer::MODULE_NEWS, (isset($images_params['image']) ? $images_params['image'] : array()), $reader_baseurl));
            }
        }
        return $item;
    }
    /**
     * 
     * @param array $data
     * @param array $images
     * @param string $reader_baseurl
     * @return array
     */
    public function staticPage($data, $images, $reader_baseurl, $pid, $menutype) {    
        $data['pid'] = $pid;
        $data['menutype'] = $menutype;
        $data['module'] = '';
        $data['redirectid'] = 0;  
        if(($item = $this->saveItem(self::TABLE_CATEGORIES, $data))) {
            // создаем массив для будущих ошибок
            $item['errors'] = array();
            if($images) {
                $item['errors'] = array_merge($item['errors'], $this->saveMediaImages($images, $reader_baseurl));  
            }
            if($item['image']) {
                $item['errors'] = array_merge($item['errors'], $this->saveModuleImage($item['image'], Importer::MODULE_STATIC, (isset($images_params['image']) ? $images_params['image'] : array()), $reader_baseurl));
            }
            if($item['image_icon']) {
                $item['errors'] = array_merge($item['errors'], $this->saveModuleImage($item['image_icon'], Importer::MODULE_STATIC, (isset($images_params['image_icon']) ? $images_params['image_icon'] : array()), $reader_baseurl));
            }
        }
        return $item;
    }
    /**
     * 
     * @param array $data
     * @param array $images
     * @param string $reader_baseurl
     * @return array
     */
    public function category($data, $images, $reader_baseurl, $pid, $menutype) {        
        $data['pid'] = $pid;
        $data['menutype'] = $menutype;
        $data['module'] = Importer::MODULE_PRINTS;
        $data['redirectid'] = 0;  
        
        $data['product_seo_title'] = str_replace(array('{title}', '{ptitle}'), array('{category}', '{title}'), $data['product_seo_title']);
        $data['product_meta_descr'] = str_replace(array('{title}', '{ptitle}'), array('{category}', '{title}'), $data['product_meta_descr']);
        $data['product_meta_key'] = str_replace(array('{title}', '{ptitle}'), array('{category}', '{title}'), $data['product_meta_key']);
        
        if(($item = $this->saveItem(self::TABLE_CATEGORIES, $data))) {
            // создаем массив для будущих ошибок
            $item['errors'] = array();
            if($images) {
                $item['errors'] = array_merge($item['errors'], $this->saveMediaImages($images, $reader_baseurl));  
            }
            if($item['image']) {
                $item['errors'] = array_merge($item['errors'], $this->saveModuleImage($item['image'], Importer::MODULE_STATIC, (isset($images_params['image']) ? $images_params['image'] : array()), $reader_baseurl));
            }
            if($item['image_icon']) {
                $item['errors'] = array_merge($item['errors'], $this->saveModuleImage($item['image_icon'], Importer::MODULE_STATIC, (isset($images_params['image_icon']) ? $images_params['image_icon'] : array()), $reader_baseurl));
            }
        }
        return $item;
    }
    /**
     * Write news into new database
     * @param array $data
     * @param array $images
     * @param array $images_params
     * @param string $reader_baseurl
     * @return array
     */
    public function product($data, $images, $reader_baseurl, $arCategories, $arTypes, $arColorsIDX) { 
        // получаем массивы по разположению логотипа
        // старые размеры 345*372, новые размеры 580*625
        $rate = round(580/345, 2);
        $firstActiveTypeID = 0;
        foreach($arTypes as $type) {
            if($type['active']) {
                $firstActiveTypeID = $type['id'];
                break;
            }
        }
        // перестраиваем данные для сохранения
        $itemData = array (
            'category_id' => (isset($arCategories[$data['cid']]) ? $arCategories[$data['cid']]['id'] : self::ROOT_CATEGORY_ID),
            'substrate_id'=> (isset($arTypes[$data['deftypeid']]) && $arTypes[$data['deftypeid']]['active']>0 ? $arTypes[$data['deftypeid']]['id'] : $firstActiveTypeID),
            'placement'   => PrintProduct::SIDE_FRONT,
            'pcode'       => $data['pcode'],
            'title'       => $data['title'],
            'text'        => unScreenData($data['text']),
            'meta_descr'  => (!isset($arCategories[$data['cid']]) || $arCategories[$data['cid']]['product_meta_descr'] == '' ? $data['meta_descr'] : ''),
            'meta_key'    => (!isset($arCategories[$data['cid']]) || $arCategories[$data['cid']]['product_meta_key'] == '' ? $data['meta_key'] : ''),
            'meta_robots' => $data['meta_robots'],
            'seo_title'   => (!isset($arCategories[$data['cid']]) || $arCategories[$data['cid']]['product_seo_title'] == '' ? $data['seo_title'] : ''),
            'seo_path'    => $data['seo_path'],
            'order'       => $data['order'],
            'active'      => $data['active'],
            'created'     => $data['created'],
            'modified'    => $data['modified'],
            'source_id'   => $data['source_id'],
        );
        if(($item = $this->saveItem(self::TABLE_PRINTS, $itemData))) {                            
            // создаем массив для будущих ошибок
            $item['errors'] = $item['files'] = $item['assort'] = array();
            if($images) {
                $item['errors'] = array_merge($item['errors'], $this->saveMediaImages($images, $reader_baseurl));  
            }
            // записываем файлы
            $idx = 0;
            foreach($data['files'] as $source_id => $file) {
                $fileData = array(
                    'print_id'  => $item['id'],
                    'filename'  => '',
                    'title'     => '',
                    'order'     => ++$idx,
                    'active'    => $file['active'],
                    'created'   => 'NOW()',
                    'source_id' => $source_id,
                );
                $item['files'][$source_id] = $this->saveItem(self::TABLE_PRINT_FILES, $fileData);
                $item['files'][$source_id]['filename'] = $fileData['filename'] = PrintProduct::createLogoFileName($item['files'][$source_id]['id'], $file['filename']);
                updateRecords(self::TABLE_PRINT_FILES, '`filename`="'.$fileData['filename'].'"', 'WHERE `id`='.$item['files'][$source_id]['id'].' LIMIT 1');
                //скопировать картинку
                $item['errors'] = array_merge($item['errors'], $this->saveModuleImage($fileData['filename'], Importer::MODULE_PRINTS, array(), $reader_baseurl, UPLOAD_URL_DIR.Importer::MODULE_CATALOG.'/'.$file['filename']));
            }
            // записываем ассортименты
            $idx = 0;
            foreach ($data['assortment'] as $typeid => $assort) {
                $isDefAssort = ($data['deftypeid'] == $typeid);
                $assortData = array(
                    'print_id'  => $item['id'],
                    'substrate_id' => (isset($arTypes[$typeid]) ? $arTypes[$typeid]['id'] : 0),
                    'color_id'  => (isset($arColorsIDX[$assort['colorcode']]) ? $arColorsIDX[$assort['colorcode']] : 0),
                    'seo_path'  => $assort['seo_path'],
                    'price'     => $assort['price'],
                    'order'     => ++$idx,
                    'active'    => 1,
                    'isdefault' => $isDefAssort ? 1 : 0,
                    'source_id' => $assort['source_id'],
                );                
                $item['assort'][$assort['source_id']] = $this->saveItem(self::TABLE_PRINT_ASSORT, $assortData);                
                //записываем сеттингсы и цвета
                foreach($assort['settings'] as $settings) {
                    if(isset($item['files'][$settings['fileid']])){
                        isset($settings['height']) or $settings['height'] = 0;
                        $this->prepageProductLogoSizes(UPLOAD_URL_DIR.Importer::MODULE_PRINTS.'/', $item['files'][$settings['fileid']]['filename'], $settings['width'], $settings['height']);
                        //записывает сеттингсы
                        $assortSettings = array(
                            'assortment_id' => $assortData['id'],
                            'file_id'       => $item['files'][$settings['fileid']]['id'],
                            'offset'        => round($settings['ypos']*$rate),
                            'width'         => round($settings['width']*$rate),
                            'height'        => round($settings['height']*$rate),
                            'active'        => 1,
                            'source_id'     => $settings['source_id']
                        );                    
                        $assortSettings = $this->saveItem(self::TABLE_PRINT_ASSORT_SETTINGS, $assortSettings);
                        //цвета
                        $idx = 0;                    
                        $colors = explode(',', $settings['colors']);   
                        foreach($colors as $colorcode) {
                            $colorData = array(
                                'assortment_id' => $assortData['id'],
                                'color_id'      => (isset($arColorsIDX[$colorcode]) ? $arColorsIDX[$colorcode] : 0),
                                'file_id'       => $assortSettings['file_id'],
                                'order'         => ++$idx,
                                'isdefault'     => ($colorcode == $assort['colorcode']) ? 1 : 0,
                                'active'        => 1,
                                'source_id'     => $assortData['id'].'|'.$assortSettings['file_id'].'|'.$colorcode,
                            );
                            $colorData = $this->saveItem(self::TABLE_PRINT_ASSORT_COLORS, $colorData);
                            $item['colors'][] = $colorData['id'];
                        }   
                    } else {
                        $item['errors'][] = 'Файл с ID ' . $settings['fileid'] . ' не найден в новой базе в принте с ID ' . $item['source_id'];  
                    }
                }                 
                if($isDefAssort) {
                    updateRecords(self::TABLE_PRINTS, '`file_id`='.$assortSettings['file_id'], 'WHERE `id`='.$item['id']);
                }
            }
        }
        return $item;
    }
    

    protected function prepageProductLogoSizes($filesurl, $filename, &$width = 0, &$height = 0) {    
        $maxSize = array('w' => 345, 'h' => 372);

        $arSizes = getArrImageSize($filesurl, $filename);
        if(!empty($arSizes)) {
            if($width == 0) {
                $width = $arSizes[0];
                $height = $arSizes[1];
            } else if ($height == 0) {
                $height = $arSizes[1]/($arSizes[0]/$width);
            }
            if($width > $maxSize['w']) {
                $height = intval($height/($width/$maxSize['w']));     
                $width = $maxSize['w'];
            } 
            if($height > $maxSize['h']) {
                $width = intval($width/($height/$maxSize['h']));
                $height = $maxSize['h'];
            }
        }
    }
}