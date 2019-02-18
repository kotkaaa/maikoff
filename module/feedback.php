<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

$item = array();

$arrPageData["headCss"][] = "/css/smart/feedback.css";
$arrPageData["headScripts"][] = "/js/smart/feedback.js";

if (!empty($_POST)) {
    $_POST['message'] = cleanText($_POST['message']);
    $Validator->validateGeneral($_POST['firstname'], sprintf(FEEDBACK_FILL_REQUIRED_FIELD, FEEDBACK_FIRST_NAME), "firstname");
    if(!empty($_POST['phone']) && ($_POST['phone'] = PHPHelper::clearePhone($_POST['phone']))){
        $Validator->validatePhone($_POST['phone'], "Укажите телефон!", "phone");
    } else {
        $Validator->addError("Укажите телефон!", "phone");
    }  
    $Validator->validateGeneral($_POST['message'], sprintf(FEEDBACK_FILL_REQUIRED_FIELD, FEEDBACK_STRING_TEXT), "message");
    if (!empty($_POST['email'])) $Validator->validateEmail($_POST['email'], sprintf(FEEDBACK_FILL_REQUIRED_FIELD_CORRECT, FEEDBACK_EMAIL), "email");
    if ($Validator->foundErrors()) {
        $arrPageData['errors'] = $Validator->getErrors();
        $item = array_merge($item, $_POST);
    } else {  
        require_once('include/classes/ActionsLog.php');
    
        $_POST["message"]   = cleanText($_POST["message"]);
        
        $arPostData = $_POST;
        $arPostData['created'] = date('Y-m-d H:i:s');
        $arPostData['shipping_id'] = 0;
        $arPostData['user_id'] = OrderHelper::getUserID($DB, $arPostData);
        $arPostData['type_id'] = OrderHelper::TYPE_REQUEST;
        if(OrderHelper::isBanned($arPostData['user_id'])) {
            $arrPageData["messages"][] = FEEDBACK_STRING_SEND_EMAIL;
        } else {
            $arPostData['channel_code'] = AdManager::getCurrentClient()->getClientKey();        
            $arPostData['name'] = $_POST["firstname"];
            $arPostData['comment'] = $_POST["message"];
            $result = $DB->postToDB($arPostData, ORDERS_TABLE);
            if ($result and is_int($result)) {
                $orderID = $result;
                $_POST['orderID'] = $orderID;
                $arData = screenData($_POST);
                $arData['created']  = date('Y-m-d H:i:s');
                $arData['ip']       = $_SERVER['REMOTE_ADDR'];
                $arData['server']   = WLCMS_HTTP_HOST;
                $arData['sitename'] = ucfirst($_SERVER["HTTP_HOST"]);
                $smarty->assign('arData', $arData);
                $text    = $smarty->fetch('mail/feedback_admin.tpl');
                $subject = "Заявка №{$orderID}. ".$arData['sitename'].': '.sprintf(FEEDBACK_MESSAGE_FROM, $arCategory['title']);
                if (sendMail($objSettingsInfo->notifyEmail, $subject, $text, $objSettingsInfo->siteEmail)) $arrPageData["messages"][] = FEEDBACK_STRING_SEND_EMAIL;
                else $arrPageData['errors'][] = FEEDBACK_MESSAGE_SEND_ERROR.'. '.TRY_AGAIN_TITLE;
            }
        }
    }
}

$smarty->assign("item", $item);

if ($IS_AJAX) {
    $smarty->assign("UrlWL",            $UrlWL);
    $smarty->assign("arrPageData",      $arrPageData);
    $smarty->assign("arCategory",       $arCategory);
    $smarty->assign("arrModules",       $arrModules);
    $smarty->assign("objSettingsInfo",  $objSettingsInfo);
    $smarty->assign("HTMLHelper",       $HTMLHelper);
    die($smarty->fetch("ajax/feedback-form.tpl"));
}