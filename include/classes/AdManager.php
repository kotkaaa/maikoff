<?php

/**
 * Description of AdManager class
 * Created on 26.09.2014, 13:02:36
 * @author Andreas, WebLife
 * @copyright 2014
 */
abstract class AdManager {
    const SESSION_KEY = 'AdManagerClient';
    const COOKIE_KEY = 'AdManagerClient';
    
    const GOOGLE_KEY = 'gclid';
    const SALESDOUBLE_KEY = 'aff_sub';

    public static $CLIENTS = array(
        self::GOOGLE_KEY => array(
            'name' => 'Google Adwords',
            'orderIdPrefix' => 'A',
            'orderTypeId' => 5,
            'cookieLifetime' => 30, //days
            'postback' => 0,
        ),
        /*self::SALESDOUBLE_KEY => array(
            'name' => 'Sales Doubler',
            'orderIdPrefix' => 'A',
            'orderTypeId' => 12,
            'cookieLifetime' => 15,
            'postback' => 1,
        ),*/
    );
    
    private static function getAdManagerClient($clientKey) {
        if(array_key_exists($clientKey, self::$CLIENTS)){
            return new AdManagerClient(self::$CLIENTS[$clientKey]['name'], $clientKey, self::$CLIENTS[$clientKey]['orderIdPrefix'], self::$CLIENTS[$clientKey]['orderTypeId']);
        }
        return new AdManagerClient();
    }
    
    public static function setCurrentClient($clientKey, $clientValue) {
        if(array_key_exists($clientKey, self::$CLIENTS)) {             
            $arCookie = self::getClientFromCookie();            
            if($arCookie['clientKey'] != $clientKey || $arCookie['clientValue'] != $clientValue) {
                $arCookie = array('clientKey' => $clientKey, 'clientValue' => $clientValue);
                $domain = !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
                setcookie(self::COOKIE_KEY, serialize($arCookie), (time()+60*60*24*self::$CLIENTS[$clientKey]['cookieLifetime']), '/', $domain);
            } 
        }
    }
    
    public static function getClientNameByCode($code) {
        if(array_key_exists($code, self::$CLIENTS)) {
            return self::$CLIENTS[$code]['name'];
        } else return '';
    }
    
    public static function getCurrentClient() {
        $arCookie = self::getClientFromCookie();
        $clientKey = $arCookie['clientKey'] ? $arCookie['clientKey'] : '';
        return self::getAdManagerClient($clientKey);
    }

    public static function getClientByKey($clientKey) {
        return self::getAdManagerClient($clientKey);
    }

    public static function getClientKeys() {
        return array_keys(self::$CLIENTS);
    }
    
    public static function getClientFromCookie() {
        return isset($_COOKIE[self::COOKIE_KEY]) ? unserialize($_COOKIE[self::COOKIE_KEY]) : array('clientKey' => null, 'clientValue' => null);
    }
    
    public static function getAddID() {
        $arCookie = self::getClientFromCookie();
        if($arCookie['clientKey'] && $arCookie['clientValue'] && array_key_exists($arCookie['clientKey'], self::$CLIENTS) && self::$CLIENTS[$arCookie['clientKey']]['postback']) {
            return $arCookie['clientValue'];
        }
        return '';
    }
    
    public static function isGoogleAdwords() {
        $currentKey = self::getCurrentClient()->getClientKey();
        return (int)($currentKey && $currentKey == self::GOOGLE_KEY);
    }
}

/**
 * Description of AdManagerClient class
 * Created on 26.09.2014, 13:02:36
 * @author Andreas, WebLife
 * @copyright 2014
 */
class AdManagerClient {
    /**
     * @var $name String название рекламной компании
     */
    private $name;
    /**
     * @var $clientKey String ключ Request
     */
    private $clientKey;
    /**
     * @var $orderIdPrefix String префикс который будет добавлятся к ID заказа
     */
    private $orderIdPrefix;
    /**
     * @var $orderTypeId int идентификатор типа заказа который будет устанавливатся по умолчанию к заказу
     */
    private $orderTypeId;
    
    public function __construct($name='', $clientKey='', $orderIdPrefix='', $orderTypeId=0) {
        $this->name = $name;
        $this->clientKey = $clientKey;
        $this->orderIdPrefix = $orderIdPrefix;
        $this->orderTypeId = $orderTypeId;
    }
    
    public function getClientKey() {
        return $this->clientKey;
    }
    
    public function getOrderIdPrefix() {
        return $this->orderIdPrefix;
    }
    
    public function getOrderTypeId() {
        return $this->orderTypeId;
    }
    
}

/**
 * 
 * 
 * 
 * foreach(AdManager::getClientKeys() as $k){
    if(isset($$k)){
        AdManager::setCurrentClient($k);
        //goToUrl($engine_u);
    }
}
 * 
 * 
 * AdManager::getCurrentClient()->getOrderIdPrefix()
 */