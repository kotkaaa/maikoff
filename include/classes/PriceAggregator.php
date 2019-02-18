<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class AgregatorsManager {
    private $agregators;
    private $keys;
    
    private $_UrlWL;
    private $_baseUrl;
    private $excludeIDX;
    
    /**
     * 
     * @return type
     */
    public static function getAgregators() {
        return array(
            1 => array('id' => 1, 'name' => 'GoogleMerchantCenter'),
        );
    }
    /**
     * 
     * @param type $UrlWL
     */
    public function __construct($UrlWL) {
        $this->_UrlWL = $UrlWL;
        $this->_baseUrl = $UrlWL->getBaseUrl("https://");
        
        $this->agregators = array();
        foreach(self::getAgregators() as $agregator){
            $class = 'Price'.$agregator['name'].'Aggregator';
            $this->agregators[$agregator['id']] = new $class();
        }
        $this->keys = array_keys($this->agregators);    
        
        //exclude items
        $arBlockedWords = array("sex","fuck","steroid","секс","хуле","сука","porn","ёб","херня","marijuana");
        $this->excludeIDX = getArrValueFromDB(PRINTS_TABLE, 'id', 'WHERE title LIKE "%'.implode('%" OR title LIKE "%', $arBlockedWords).'%"');
        
    }
    /**
     * 
     * @param type $temppath
     * @param type $destpath
     */
    public function run($temppath, $destpath) {
        // проверка переданных путей к файлам
        if(!$temppath || !is_dir($temppath) || !is_writable($temppath)){
             die('Folder ['.$temppath.'] is empty OR is not exists OR is not writable!');
        } elseif(!$destpath || !is_dir($destpath) || !is_writable($destpath)){
             die('Folder ['.$destpath.'] is empty OR is not exists OR is not writable!');
        }             
        //header
        foreach($this->keys as $key){
            $this->agregators[$key]
                // open file
                ->openFile($temppath.'/'.$this->agregators[$key]->getFileName())
                // write header
                ->writeHeader($this->_baseUrl);
        }        
        //items
        $query = PrintProduct::getItemsSql('WHERE ti.`is_deleted`=0 AND ti.category_id<>338 AND ti.`is_available`=1 '.($this->excludeIDX ? 'AND ti.print_id NOT IN('.implode(',', $this->excludeIDX).')' : ''), ' GROUP BY ti.`print_id`');
        $result = mysql_query($query) or die('products: '.mysql_error());
        if ($result && mysql_num_rows($result)>0) {
            while(($row = mysql_fetch_assoc($result))) {
                $row = PrintProduct::getItem($row, $this->_UrlWL);
                foreach($this->keys as $key){
                    $this->agregators[$key]->writeItem($row, $this->_baseUrl);
                }
            }
        }
        //footer
        foreach($this->keys as $key){
            $this->agregators[$key]
                // write fotter
                ->writeFooter()
                // close file
                ->closeFile()
                // copy file
                ->copyFile($destpath.'/'.$this->agregators[$key]->getFileName());
        }
    }     
}

/**
 * Description of PriceAggregator
 *
 * @author user5
 */
abstract class PriceAggregator {  
    /**
     * Opened file resource link
     * @var resource 
     */
    protected $_handle = null;     
    /**
     * Filepath
     * @var resource 
     */
    protected $_file = null;  
    /**
     * Formatted xml
     * @var bool 
     */
    private $_format = true;       
    /**
     * Open file
     * @return $this
     */
    public function openFile($filename) {
        if($this->_handle && is_resource($this->_handle)) die('File opened! Call closeFile() before reopen!');
        elseif ($filename) {
            //open/create file
            $this->_handle = @fopen($filename, 'w');
            if(!$this->_handle) die('Error open/create file!');
            else $this->_file = $filename;
        } else die('Missed filename!');
        return $this;
    }    
    /**
     * Close file
     * @return $this
     */
    public function closeFile() {
        if($this->_handle !== null){
            if(is_resource($this->_handle)){
                fclose($this->_handle);
            }
            $this->_handle = null;
        }
        return $this;
    }
    /**
     * Close file
     * @param string $dest
     * @return $this
     */
    public function copyFile($dest) {
        $this->closeFile();
        if(!$this->_file)  die('Missed source file!');
        if(!$dest)  die('Missed destination file!');
        if($this->_file == $dest) die('Source ['.$this->_file.'] IS EQUAL to destination ['.$dest.']!');
        if(!copy($this->_file, $dest)) die('Failure copy file from ['.$this->_file.'] to ['.$dest.']!');
        return $this;
    }    
    /**
     * 
     * @param type $txt
     */
    protected function write($txt) {
        if($this->_handle && is_resource($this->_handle)) fwrite($this->_handle, $txt);
        else die('Error write to file! File is closed. Call openFile($filename) to wtite');
    }
    /**
     * 
     * @param type $line
     * @param type $indent
     * @return type
     */
    protected function formatLine($line, $indent = 0) {
        return $this->_format ? str_repeat('    ', $indent) . $line . PHP_EOL : $line;
    }   
    abstract public function getFileName();
    abstract public function getProductName($item);
    abstract public function getProductUrl($item, $baseUrl);
    abstract public function writeHeader($baseUrl);
    abstract public function writeItem($item, $baseUrl);
    abstract public function writeFooter();    
}

class PriceGoogleMerchantCenterAggregator extends PriceAggregator {
    /**
     * 
     * @return string
     */
    public function getFileName() {
        return 'google_merchant_center_feed.xml';
    }
    /**
     * 
     * @param type $title
     * @return type
     */
    public function getProductName($item) {
        $title = unScreenData($item['substrate_title'].' '.$item['title']);
        $title = str_replace(array('<--', '-->'), '', $title);
        $title = strip_tags($title);
        $title = mb_strtolower($title);  
        if (($title = trim($title))) {        
            $words = explode(' ', $title);
            foreach($words as &$word) {
                $word = trim($word);
                $word = mb_convert_case($word, MB_CASE_TITLE, WLCMS_SYSTEM_ENCODING);
            }
            $title = implode(' ', $words);
        }     
        return screenData($title);
    }
    /**
     * 
     * @param type $title
     * @return type
     */
    public function getProductUrl($item, $baseUrl) {
        $link = '';
        if($item['big_image']) {
            $link = explode('?', $item['big_image']);
            $link = $baseUrl.$link[0];
        }
        return $link;
    }
    /**
     * 
     * @return $this
     */
    public function writeHeader($baseUrl) {
        $xml = $this->formatLine('<?xml version="1.0"?>');
        $xml.= $this->formatLine('<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">');
        $xml.= $this->formatLine('<channel>');
        $xml.= $this->formatLine('<title>Maikoff.com.ua</title>');        
        $xml.= $this->formatLine('<link>'.$baseUrl.'</link>');
        $xml.= $this->formatLine('<description>Печать на одежде</description>');
        $this->write($xml);
        return $this;
    }
    /**
     * 
     * @param type $item
     * @return $this
     */
    public function writeItem($item, $baseUrl) {
        $xml = $this->formatLine('<item>');
        $xml.= $this->formatLine('<g:id>'.$item['print_id'].'</g:id>');
        $xml.= $this->formatLine('<g:title><![CDATA['.$this->getProductName($item).']]></g:title>');
        $xml.= $this->formatLine('<g:description>'.($item['descr'] ? '<![CDATA['.$item['descr'].']]>' : '').'</g:description>');
        $xml.= $this->formatLine('<g:link>'.$baseUrl.$item['product_url'].'</g:link>');
        $xml.= $this->formatLine('<g:image_link>'.$this->getProductUrl($item, $baseUrl).'</g:image_link>');
        $xml.= $this->formatLine('<g:condition>new</g:condition>');   
        $xml.= $this->formatLine('<g:availability>in stock</g:availability>');
        $xml.= $this->formatLine('<g:price>'.number_format($item['price'], 0).' UAH</g:price>');
        $xml.= $this->formatLine('<g:brand></g:brand>');                  
        $xml.= $this->formatLine('<g:google_product_category>1604</g:google_product_category>');                   
        $xml.= $this->formatLine('</item>');
        $this->write($xml);
        return $this;
    }
    /**
     * 
     * @return $this
     */
    public function writeFooter() {
        $this->write('</channel></rss>');
        return $this;
    }
}