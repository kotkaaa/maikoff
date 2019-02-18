<?php

/**
 * Description of ImportExport class
 * This class provides methods for Import / Export data
 * @author WebLife
 * @copyright 2013
 */
class ImportExport {

    const CSV_DELIMITER     = ';';
    const CSV_ENCLOSURE     = '"';

    const YML_TYPE_DEFAULT  = 'vendor.model';

    public function __construct() {
        setlocale(LC_ALL, array('ru_RU.utf8', 'ru_RU', 'ru', 'rus_RUS'));
    }

    public static function __outputCSV(&$vals, $key, $filehandler) {
        fputcsv($filehandler, $vals, self::CSV_DELIMITER, self::CSV_ENCLOSURE); // add parameters if you want
    }

    public static function __outputYML($filehandler, $str) {
        fwrite($filehandler, $str);
    }

    public static function __fgetcsv($handle, $length, $delimiter=',', $enclosure='"'){
        if (version_compare(PHP_VERSION, "5.2.1", ">")) {
            $arLine = fgetcsv($handle, $length, $delimiter, $enclosure);
        } else {
            $line   = fgets($handle);
            $arLine = $line ? explode($delimiter, trim($line)) : $line;
            if(is_array($arLine)){
                foreach($arLine as $k=>$v) {
                    $arLine[$k] = ltrim(rtrim($v, $enclosure), $enclosure);
                }
            }
        } return $arLine;
    }

    public static function outputCSV(array $arCSVData, $filename='output', $exit=true){
        //  Вывод примера файла с десятю строчками информации о товарах
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=\"{$filename}.csv\";");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-Transfer-Encoding: binary");
        $outstream = fopen("php://output", "w");
        array_walk($arCSVData, "ImportExport::__outputCSV", $outstream);
        fclose($outstream);
        if($exit) exit();
    }

    public static function outputYML(array $arYMLData, $filename='output', $type="", $exit=true){
        header("Content-type: text/xml; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"{$filename}.yml\";");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-Transfer-Encoding: binary");
        $outstream = fopen("php://output", "w");
        //add <!DOCTYPE yml_catalog SYSTEM "shops.dtd">
        ImportExport::__outputYML($outstream, ImportExport::generateYML($arYMLData));
        fclose($outstream);
        if($exit) exit();
    }

    public static function generateYML(array $arYMLData){
        // http://partner.market.yandex.ru/legal/tt/#id1164252226643
        $dom = new domDocument("1.0", "utf-8");
        $root = $dom->createElement("yml_catalog");
        $root->setAttribute("date", date("Y-m-d h:m"));
        $dom->appendChild($root);

        $shop = $dom->createElement("shop");
        // основные данные про гамазин
        $shop->appendChild($dom->createElement("name", $arYMLData['name']));
        $shop->appendChild($dom->createElement("company", $arYMLData['company']));
        $shop->appendChild($dom->createElement("url", $arYMLData['url']));
        $shop->appendChild($dom->createElement("email", $arYMLData['email']));

        // валюты
        $currencies = $dom->createElement("currencies");
        if(!empty($arYMLData['arCurrencies'])) {
            foreach($arYMLData['arCurrencies'] as $value){
                $currency = $dom->createElement("currency");
                $currency->setAttribute("id", $value['id']);
                $currency->setAttribute("rate", $value['rate']);
                $currencies->appendChild($currency);
            }
        }
        $shop->appendChild($currencies);

        // категории
        $categories = $dom->createElement("categories");
        if(!empty($arYMLData['arCategories'])) {
            foreach($arYMLData['arCategories'] as $cat) {
                $category = $dom->createElement("category", $cat['title']);
                $category->setAttribute("id", $cat['id']);
                if($cat['pid']!=$arYMLData['catalog_root_id'])
                    $category->setAttribute("parentId", $cat['pid']);
                $categories->appendChild($category);
            }
        }
        $shop->appendChild($categories);

        // стоимость доставки
        $shop->appendChild($dom->createElement("local_delivery_cost", $arYMLData['local_delivery_cost']));

        // товары
        // произвольный тип (vendor.model), параметры: ? - необязательный, !-обязательный
        // 1. url(? max 512) price(!) currencyId(!) categoryId(!) picture(? (для одежды обязательно)) store(?true|false)
        // pickup(?true|false) delivery(?true|false) local_delivery_cost(?) typePrefix(? категория/группа)
        // name(!) vendor(!) vendorCode(?(артикул от производитеял)) model(!) description(?)
        // sales_notes(? мин сум заказа) seller_warranty и manufacturer_warranty(?false|true - имеет|нет оф гарантию/строка гарантии (ISO 8601, например: P1Y2M10DT2H30M)
        // country_of_origin(?) downloadable(?) adult(?) age(? =0, 6, 12, 16, 18)
        // barcode(? штирхкод производителя) cpa(?) rec(? рекомендованные товары) expiry(?)
        // weight(? kg+tara kg) dimensions(?) param(? атрибуты - значения)
        $offers = $dom->createElement("offers");
        if(!empty($arYMLData['arProducts'])) {
            foreach($arYMLData['arProducts'] as $product) {
                $offer = $dom->createElement("offer");
                $offer->setAttribute("type", self::YML_TYPE_DEFAULT);
                $offer->setAttribute("id", $product['id']);
                $offer->setAttribute("bid", '');
                $offer->setAttribute("available", 'true');

                foreach($product['arParams'] as $key=>$value) {
                    $offer->appendChild($dom->createElement($key, $value));
                }

                if(!empty($product['arAttributes'])) {
                    foreach($product['arAttributes'] as $attr) {
                        $param = $dom->createElement('param', $attr['value']);
                        $param->setAttribute("name", $attr['title']);
                        $offer->appendChild($param);
                    }
                }
                $offers->appendChild($offer);
            }
        }
        $shop->appendChild($offers);
        $root->appendChild($shop);
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    public static function outputSqlFiles(array $arFiles, $outfilename, $exit=true){
        // set headers
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-type: text/sql");
        header("Content-Disposition: attachment; filename=\"{$outfilename}.sql\"");
        header("Content-Transfer-Encoding: binary");

        $outstream = fopen("php://output", "w");
        foreach ($arFiles as $file) {
            if(file_exists($file) AND is_file($file) AND is_readable($file)){
                $file = @fopen($file, "rb");
                if($file) {
                    while(!feof($file)) {
                        fwrite($outstream, fread($file, 1024 * 8));
                        if( connection_status()!=0 ) {
                            @fclose($file);
                            die();
                        }
                    } @fclose($file);
                }
            }
        }
        fclose($outstream);
        if($exit) exit();
    }

    public static function outputFile($file, $exit=true){
        if( $file AND strpos($file, "\0") === FALSE/*Nullbyte hack fix*/){
            // Make sure program execution doesn't time out
            // Set maximum script execution time in seconds (0 means no limit)
            @set_time_limit(0);

            // Make sure that header not sent by error
            // Sets which PHP errors are reported
            @error_reporting(0);

            // Allow direct file download (hotlinking)?  Empty - allow hotlinking
            // If set to nonempty value (Example: example.com) will only allow downloads when referrer contains this text
            $allowed_referrer = $_SERVER['SERVER_NAME'];

            // If hotlinking not allowed then make hackers think there are some server problems
            if ( !empty($allowed_referrer) AND
                 (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']), strtoupper($allowed_referrer)) === false)
            ) die("Internal server error. Please contact system administrator.");

            // if don't exist and isn't file and  can't read them - die
            if (!file_exists($file) AND !is_file($file) AND !is_readable($file)) {
              header ("HTTP/1.0 404 Not Found");
              exit();
            }

            // Get real file name.
            // Remove any path info to avoid hacking by adding relative path, etc.
            $fname = basename($file);

            // file size in bytes
            $fsize = filesize($file);

            // get mime type
            $mtype = '';
            // mime type is not set, get from server settings
            if (function_exists('mime_content_type')) {
                $mtype = mime_content_type($file);
            } else if (function_exists('finfo_file')) {
                $finfo = finfo_open(FILEINFO_MIME); // return mime type
                $mtype = finfo_file($finfo, $file);
                finfo_close($finfo);
            }
            if ($mtype == '') {
                $mtype = "application/force-download";
            }

            // Browser will try to save file with this filename, regardless original filename.
            // You can override it if needed.

            // remove some bad chars
            $asfname = str_replace(array('"',"'",'\\','/'), '', $fname);
            if ($asfname === '') $asfname = 'NoName';

            // set headers
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Type: $mtype");
            header("Content-Disposition: attachment; filename=\"$asfname\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . $fsize);

            // download
            // @readfile($file);
            $file = @fopen($file, "rb");
            if($file) {
                while(!feof($file)) {
                    print(fread($file, 1024 * 8));
                    flush();
                    if( connection_status()!=0 ) {
                        @fclose($file);
                        die();
                    }
                } @fclose($file);
            }
            if($exit) exit();
        } else { die(); }
    }

    public static function parseFile($csvfile, $presentColumnNames=true) {
        if(empty($csvfile) || !is_file($csvfile)) return false;

	$filesize       = filesize($csvfile);
	$handle         = fopen($csvfile, "r");
        if(!$filesize || !$handle) return false;

        $csvfilename    = basename($csvfile);
        $arLine         = array();
        if($presentColumnNames){
            //Получаем первую строку из CSV файла и приводим ее в нужный вид (удаляем пробелы в начале и в конце строки, понижаем в нижний регистр)
            $arLine = self::__fgetcsv($handle, $filesize, self::CSV_DELIMITER, self::CSV_ENCLOSURE);
            if(!empty($arLine)){
                foreach($arLine as $column=>$val) {
                    $arLine[$column] = mb_strtolower(trim($val));
                }
            } //print_r($arLine); exit();
            // создаем массив с ключем(название колонки в базе) и позицией в строке CSV начиная с 0
            $arLine = array_flip($arLine); // Поменять местами ключи и значения массива, т.е. ключи становятся значениями, а значения становятся ключами
        }

        // Создаем результирующий массив
        $arData = array('columns'=>$arLine, 'data'=>array(), 'count'=>0);

        // Импортируем из CSV файла данные в пустую базу
        while( ($arLine = self::__fgetcsv($handle, $filesize, self::CSV_DELIMITER, self::CSV_ENCLOSURE)) !== FALSE ) {
            // проверка на правильное количество колонок. если не одинаковое - пропускаем
            if($presentColumnNames AND count($arLine)!=count($arData['columns'])) continue; //например строки где разделение каким-то текстом или неверный формат
            $arData['data'][] = $arLine;
            $arData['count']++;
        }
        // закрываем файл
        fclose($handle);
        // удаляем файл
//        @unlink($csvfile);
        // возвращаем
        return $arData;
    }

    public static function assocArrayToQuery(array $arData, $arSkip=array(), $type='insert') {
        if(empty($arData)) return false;
        $keys = $values = $arSkiped = array();
        foreach($arSkip as $key) { if(key_exists($key, $arData)) $arSkiped[$key] = $arData[$key]; }
        switch($type) {
            case 'insert':
                foreach($arData as $key=>$value) {
                    if (!in_array($key, $arSkip)) {
                        $keys[]   = $key;
                        $values[] = ($value===NULL || $value==="NULL") ? 'NULL' : ($value==="NOW()" ? "NOW()" : "'".self::forSql($value)."'");
                    }
                } break;
            case 'update':
                foreach($arData as $key => $value) {
                    if (!in_array($key, $arSkip)) {
                        $values[] = ($value===NULL || $value==="NULL") ? $key."=NULL" : ($value==="NOW()" ? "NOW()" : $key."='".self::forSql($value)."'");
                    }
                } break;
        } return array("keys"=>implode(", ",$keys), "values"=>implode(", ",$values), 'arSkiped'=>$arSkiped);
    }

    public static function forSql($str, $imaxln=0) {
        $str = str_replace("\0",  '', $str);
        $str = str_replace("'", '"', $str);
        if($imaxln>0) $str = mb_substr($str, 0, $imaxln);
        return $str;
    }
}