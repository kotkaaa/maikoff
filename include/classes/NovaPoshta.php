<?php

/*
 * WebLife CMS
 * Created on 24.09.2018, 10:03:55
 * Developed by http://weblife.ua/
 */

namespace NovaPoshta;

/**
 * Description of NovaPoshta
 *
 * @author user5
 */
class Api {
    //put your code here
    const NP_API_URL = "https://api.novaposhta.ua/v2.0/xml/";
    const NP_API_KEY = "45d1186110fd743bee21e8851355c829"; // ключ API с Лагранде

    static $items = [];

    private static function getItemsExternal($xml) {
        self::$items = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::NP_API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        if (!@curl_errno($ch)) {
            $objXML = simplexml_load_string($response);
            if (is_object($objXML)) {
                $arrXML = (array)$objXML;
                if ($arrXML["success"]) {
                    foreach ($arrXML["data"]->item as $item) {
                        self::$items[] = $item;
                    }
                }
            }
        } return self::$items;
    }

    private static function getCitiesExternal($raw = "") {
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
                <file>
                    <apiKey>".self::NP_API_KEY."</apiKey>
                    <modelName>Address</modelName>
                    <calledMethod>getCities</calledMethod>
                    <methodProperties>
                        <Language>ru</Language>".
                        (!empty($raw) ? "<FindByString>{$raw}</FindByString>" : "").
                    "</methodProperties>
                </file>";
        return self::getItemsExternal($xml);
    }

    private static function getWarehousesExternal($raw = "") {
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
                <file>
                    <apiKey>".self::NP_API_KEY."</apiKey>
                    <modelName>AddressGeneral</modelName>
                    <calledMethod>getWarehouses</calledMethod>
                    <methodProperties>
                        <Language>ru</Language>".
                        (!empty($raw) ? "<CityName>{$raw}</CityName>" : "").
                    "</methodProperties>
                </file>";
        return self::getItemsExternal($xml);
    }

    public static function getCities($raw = "", $external = false) {
        // get cities from external api
        if ($external) return self::getCitiesExternal($raw);
        // get cities from database
        if (!empty($raw) and mb_strlen($raw)>2) {
            $query  = "SELECT * FROM `".NP_CITY_TABLE."` WHERE `DescriptionRu` LIKE '%$raw%' ORDER BY `DescriptionRu`";
            $result = mysql_query($query);
            if ($result and mysql_num_rows($result)>0) {
                while ($row = mysql_fetch_object($result)) {
                    self::$items[] = $row;
                }
            }
        } return self::$items;
    }

    public static function getWarehouses($raw = "", $city = "", $external = false) {
        // get cities from external api
        if ($external) return self::getWarehousesExternal($raw);
        // get cities from database
        if (!empty($raw) and (mb_strlen($raw)>=2 and mb_strlen($city)>2)) {
            $query  = "SELECT * FROM `".NP_WAREHOUSE_TABLE."` WHERE `DescriptionRu` LIKE '%$raw%' AND `CityDescriptionRu`='$city' ORDER BY `DescriptionRu`";            
            $result = mysql_query($query);
            if ($result and mysql_num_rows($result)>0) {
                while ($row = mysql_fetch_object($result)) {
                    self::$items[] = $row;
                }
            }
        } return self::$items;
    }
}
