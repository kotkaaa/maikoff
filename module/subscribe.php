<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

if (!$IS_AJAX or $_SERVER["REQUEST_METHOD"]!="POST") die ("Ajax requests only!");

$json = array();
$position = $UrlWL->getParam("position", "inline");

$Validator->validateEmail($_POST["email"], "Введите e-mail", "email");
if ($Validator->foundErrors()) $json["errors"] = $Validator->getErrors();
else {
    $squery = "INSERT INTO `".SUBSCRIBITIONS_TABLE."` (`email`, `created`) "
            . "VALUES ('{$_POST["email"]}', '".date("Y-m-d H:i:s")."') "
            . "ON DUPLICATE KEY UPDATE `active`=1";
    $result = mysql_query($squery);
    if ($result) $json["messages"] = $arrPageData["messages"] = array("Ваша заявка получена");
    else $json["errors"] = $arrPageData["errors"] = array("Оформить подписку не удалось. Возможная причина: ".mysql_error());
} 

$smarty->assign("UrlWL",        $UrlWL);
$smarty->assign("arrPageData",  $arrPageData);
$smarty->assign("arrModules",   $arrModules);
$smarty->assign("position",     $position);
$json["output"] = $smarty->fetch("ajax/subscribe.tpl");

die(json_encode($json));