<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

if (!$IS_AJAX or $_SERVER["REQUEST_METHOD"]!="POST") die ("Ajax requests only!");
$json = array();
if(!empty($_POST['phone']) && ($_POST['phone'] = PHPHelper::clearePhone($_POST['phone']))){
    $Validator->validatePhone($_POST['phone'], "Укажите телефон!", "phone");
} else {
    $Validator->addError("Укажите телефон!", "phone");
}   
if (isset($_POST["firstname"])) $Validator->validateGeneral($_POST["firstname"], "Введите имя", "firstname");
if ($Validator->foundErrors()) {
    $json["errors"] = $Validator->getErrors();
    $json["data"]   = $_POST;
} else {
    require_once('include/classes/ActionsLog.php');
    
    $arPostData = $_POST;
    $arPostData['created'] = date('Y-m-d H:i:s');
    $arPostData['shipping_id'] = 0;
    $arPostData['user_id'] = OrderHelper::getUserID($DB, $arPostData);
    if(OrderHelper::isBanned($arPostData['user_id'])) {
        $json["messages"] = array("Ваша заявка получена");
    } else {
        $arPostData['type_id'] = OrderHelper::TYPE_CALLBACK;
        $arPostData['channel_code'] = AdManager::getCurrentClient()->getClientKey();
        if(isset($_POST["firstname"])) $arPostData['name'] = $_POST["firstname"];
        $result = $DB->postToDB($arPostData, ORDERS_TABLE);
        if ($result and is_int($result)) {
            $orderID = $result;
            $_POST['orderID'] = $orderID;
            $_POST['sitename'] = ucfirst($_SERVER["HTTP_HOST"]);
            $smarty->assign("arData", $_POST);
            $subject    = "Заявка №{$orderID}. ".'Запрос обратного звонка с сайта Maikoff.com.ua';
            $text       = $smarty->fetch("mail/callback_admin.tpl");//'Перезвоните мне, пожалуйста! '.$_POST["phone"];
            $sendResult = sendMail($objSettingsInfo->notifyEmail, $subject, $text, $objSettingsInfo->siteEmail, "html");
            if ($sendResult) $json["messages"] = array("Ваша заявка получена");
        } else {
            $json["errors"] = "Произошла ошибка! Заявка не отправлена!";
        }
    }
} die(json_encode($json));