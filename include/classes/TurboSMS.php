<?php

/**
 * WEBlife CMS
 * Created on 05.01.2011, 13:02:36
 * Developed by http://weblife.ua/
 * Andreas Gladkevych
 */
defined('WEBlife') or die('Restricted access'); // no direct access

//Все данные будут возвращатся в кодировке UTF-8 если раскоментить строку ниже
//header ('Content-type: text/html; charset=utf-8');

/**
 * Description of TurboSMS class
 * Для работы данного класса необходимо подключить SOAP-расширение.
 * @author WebLife
 * @copyright 2011
 */
class TurboSMS {

    const SERVICE_LINK      = 'http://turbosms.in.ua/api/wsdl.html';
    //https://turbosms.ua/route.html
    const SERVICE_LOGIN     = 'maikoff';
    const SERVICE_PASSWORD  = '34sdf23gxY4df2wTSDYR';
    const SENDER_SIGNATURE  = 'Maikoff';
    const COUNTRY_CODE      = '38';
    const SERVICE_URL       = 'https://turbosms.ua/';
    
    const DELIVERY_STATUS   = 'Сообщение доставлено получателю';
    
    protected $client       = null;
    protected $credits      = 0;
    protected $smsText      = '';
    protected $wapPushLink  = '';
    protected $arNumbers    = array();
    protected $arSended     = array();

    /**
     * TurboSMS::__construct()
     *
     * Construct function.
     * @return
     */
    public function __construct() {
        ;
    }


    /**
     * TurboSMS::__destruct()
     *
     * Destruct function..
     * @return
     */
    public function __destruct() {
        ;
    }


    /**
     * TurboSMS::init()
     *
     * Init SoapClient function if not int yet
     * Init client result, and credits 
     * @return
     */
    private function init() {
        if(is_object($this->client)) return;
        try {
            // Подключаемся к серверу
            $this->client = new SoapClient(self::SERVICE_LINK);

            //Авторизируемся на сервере для получения данных авторизации
            $this->client->Auth(array('login'=>self::SERVICE_LOGIN, 'password'=>self::SERVICE_PASSWORD));

            // Получаем количество доступных кредитов
            $result = $this->client->GetCreditBalance();

            // Записываем количество кредитов на текущий момент
            $this->credits = intval($result->GetCreditBalanceResult);

        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
        }
    }


    /**
     * TurboSMS::correctNumbers()
     *
     * Check and Correct phone numbers
     */
    private function correctNumbers() {
        $prefix = '+'.self::COUNTRY_CODE;
        for($i=0; $i < count($this->arNumbers); $i++) {
            if(strpos($this->arNumbers[$i], $prefix) === FALSE){
                if(strpos($this->arNumbers[$i], self::COUNTRY_CODE) === 0)
                    $this->arNumbers[$i] = str_replace(self::COUNTRY_CODE, '', $this->arNumbers[$i]);                
                $this->arNumbers[$i] = $prefix.$this->arNumbers[$i];
            }
        }    
    }


    /**
     * TurboSMS::correctPushLink()
     *
     * Check and Correct WAP Push Link
     */
    private function correctPushLink(){
        $this->wapPushLink = trim($this->wapPushLink);
        if($this->wapPushLink && (strpos($this->wapPushLink, 'http://') === FALSE)) // wapPushLink Должна включать http://
            $this->wapPushLink = 'http://'.$this->wapPushLink;
    }


    /**
     * TurboSMS::sendSMS()
     *
     * Init SoapClient function if not int yet
     * Init client result, and credits 
     * @param array $arNumbers - массив номеров для отправки
     * @param String $smsText - текст для отправки
     * @param String $wapPushLink - сообщение с WAPPush ссылкой. Должна включать http://
     * @return mixed - количество отправленных сообщений или false в случае неверно переданных данных
     */
    public function sendSMS(array $arNumbers, $smsText, $wapPushLink='') {

        // Проверка на валидность обязательных переменных
        if(empty($arNumbers) || empty($smsText)) { return false; }
        
        // Инициализируем Soap соединение если оно еще не создано
        $this->init();
        
        // Переносим переданные данные в переменные класса
        $this->arNumbers    = $arNumbers;
        $this->smsText      = $smsText;
        $this->wapPushLink  = $wapPushLink;
        
        // Проверка и корректировка телефонных номеров и wapPushLink
        $this->correctNumbers();
        $this->correctPushLink();

        if($this->credits > 0) {
            // Данные для отправки
            $sms = Array (
                    'sender' => self::SENDER_SIGNATURE,// Подпись отправителя может содержать английские буквы и цифры. Максимальная длина - 11 символов.
                    'destination' => implode(",", $this->arNumbers),// Номер указывается в полном формате, включая плюс и код страны
                    'text' => $this->smsText  //ОБЯЗАТЕЛЬНО в UTF-8
            );            
             //сообщение с WAPPush ссылкой. 
            if($this->wapPushLink) array_push($sms, $this->wapPushLink);
            // Отправляем сообщение
            $result = $this->client->SendSMS($sms);
            //Обнуляем отправленные сообщения
            $this->arSended = array();
            //Получаем результаты отправки
            for($i=1; $i <= count($this->arNumbers); $i++) {
                if(!empty($result->SendSMSResult->ResultArray[$i])) {                    
                    $this->arSended[$this->arNumbers[$i-1]] = $result->SendSMSResult->ResultArray[$i];
                }
            }        
        } return $this->arSended;
    }
    
    public function GetMessageStatus($sms) {
        $this->init();
        return $this->client->GetMessageStatus ($sms);
    }


    # ##########################################################################
    // Additinals functions ----------------------------------------------------

    public function conv($str, $from = "WINDOWS-1251", $to ="UTF-8", $translit = false) {
        return iconv($from, $to.($translit ? "//TRANSLIT" : ''), $str);
    }
    
    public function printSoapClientFunctions() {
        $this->init();
        echo $this->conv("Cписок доступных функций сервера :)<br/>\n");
        echo "<hr/>\n";
        // Можно просмотреть список доступных функций сервера
        echo '<pre>';
        print_r ($this->client->__getFunctions());
        echo '</pre>';    
    }

    public function printLoginStatusToSite($result) {
        echo $this->conv("Авторизуемся на сайт :)<br/>\n");
        echo "<hr/>\n";
        // Результат авторизации
        echo $this->conv("Результат авторизации: ").$result->AuthResult . '<br />';
        echo "<hr/>\n";    
    }

    public function printCreditBalanceResult($result) {
        echo $this->conv("Количество доступных кредитов: ").$result->GetCreditBalanceResult . '<br />';
        echo "<hr/>\n";
    }

    public function printMessageStatus($smsID = 'c9482a41-27d1-44f8-bd5c-d34104ca5ba9') {
        $this->init();
        // Запрашиваем статус конкретного сообщения по ID
        $status = $this->client->GetMessageStatus(array('MessageId' => $smsID));
        echo $status->GetMessageStatusResult . '<br />';
    }

    public function printAllMessagesStatus() {
        $this->init();
        // Запрашиваем массив ID сообщений, у которых неизвестен статус отправки
        $result = $this->client->GetNewMessages();

        // Есть сообщения
        if (!empty ($result->GetNewMessagesResult->ResultArray)) {
            echo '<pre>';
            print_r($result->GetNewMessagesResult->ResultArray);
            echo '</pre>';

            // Запрашиваем статус каждого сообщения по ID
            foreach ($result->GetNewMessagesResult->ResultArray as $msg_id) {
                $status = $this->client->GetMessageStatus(array('MessageId' => $msg_id));
                echo '<b>' . $msg_id . '</b> - ' . $status->GetMessageStatusResult . '<br />';
            }
        }
    }

    public static function isAvailible() {
        //check, if a valid url is provided
        $domain = self::SERVICE_URL;
        
        if(!filter_var($domain, FILTER_VALIDATE_URL)) {
            return false;
        }

        //initialize curl
        $curlInit = curl_init($domain);
        curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($curlInit,CURLOPT_HEADER,true);
        curl_setopt($curlInit,CURLOPT_NOBODY,true);
        curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

        //get answer
        $response = curl_exec($curlInit);

        curl_close($curlInit);

        if ($response) return true;

        return false;
    }
}
