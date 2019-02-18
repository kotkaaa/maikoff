<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

require_once('include/classes/NovaPoshta.php');
use \NovaPoshta\Api as Api;

$formData  = array();
$purchases = $Basket->getItems();
$action    = $UrlWL->getParam("action");
$raw       = $UrlWL->getParam("raw", "");
$quick     = $UrlWL->getParam("quick");
$arrPageData['headCss'][]  = '/css/smart/checkout.css';
$arrPageData['headScripts'][] = '/js/smart/checkout'.(!$IS_DEV ? ".min" : "").'.js';
$arrPageData["files_url"]  = UPLOAD_URL_DIR."orders/";
$arrPageData["files_path"] = prepareDirPath($arrPageData["files_url"], true);
$arrPageData['arPaymentTypes'] = getRowItemsInKey('id', PAYMENT_TYPES_TABLE, '*', 'WHERE active>0');

if ($IS_AJAX and $action=="getCities") {
    $items = Api::getCities($raw);
    exit(json_encode(array("items"=>$items)));
} elseif ($IS_AJAX and $action=="getWarehouses") {
    $city  = $UrlWL->getParam("city", "");
    $items = Api::getWarehouses($raw, $city);
    exit(json_encode(array("items"=>$items)));
}

if (!empty($_POST) and !empty($purchases)) {
    if(!empty($_POST['phone']) && ($_POST['phone'] = PHPHelper::clearePhone($_POST['phone']))){
        $Validator->validatePhone($_POST['phone'], sprintf(ORDER_FILL_REQUIRED_FIELD, ORDER_TEL), "phone");
    } else {
        $Validator->addError(sprintf(ORDER_FILL_REQUIRED_FIELD, ORDER_TEL), "phone");
    }   
    if(empty($_POST['address'])) $_POST['address'] = '';
    if (!$quick) {
        $_POST['comment'] = cleanText($_POST['comment']);
        $Validator->validateGeneral($_POST['name'], sprintf(ORDER_FILL_REQUIRED_FIELD, ORDER_FIRST_NAME), "name");
        if (!empty($_POST['email']) || (array_key_exists($_POST['payment_id'], $arrPageData['arPaymentTypes']) && $arrPageData['arPaymentTypes'][$_POST["payment_id"]]['is_card'])) {
            $Validator->validateEmail($_POST['email'], sprintf(ORDER_FILL_REQUIRED_FIELD, ORDER_EMAIL), "email");
        }
        // Курьером по Киеву или по Украине
        if ($_POST["shipping_id"]==1 or $_POST["shipping_id"]==2) {
            $Validator->validateGeneral($_POST['address'], "Необходимо указать адрес!", "address");
        }
        // По Украине
        if ($_POST["shipping_id"]==2) {
            $Validator->validateGeneral($_POST['recepient'], "Необходимо указать ФИО получателя!", "recepient");
        }
    }
    if ($Validator->foundErrors()) {
        $arrPageData['errors'] = $Validator->getErrors();
        $formData = array_merge($formData, $_POST);
    } else {
        require_once('include/classes/ActionsLog.php');
        
        $arPostData = screenData($_POST);
        $arPostData['created']        = date('Y-m-d H:i:s');
        $arPostData['total_price']    = $Basket->getTotalPrice();
        $arPostData['shipping_price'] = $Basket->getShippingPrice();
        $arPostData['total_qty']      = $Basket->getTotalAmount();
        $arPostData['payment_id']     = $Basket->getPaymentID();
        $arPostData['shipping_id']    = $Basket->getShippingID();
        $arPostData['user_id']        = OrderHelper::getUserID($DB, $arPostData);
        $arPostData['type_id']        = $quick ? OrderHelper::TYPE_FAST : OrderHelper::TYPE_BASKET;
        if(OrderHelper::isBanned($arPostData['user_id'])) {
            // Drop basket
            $Basket->dropBasket();
            Redirect($UrlWL->buildCategoryUrl($arrModules["thanks"]));
        } else {
            $arPostData['channel_code']   = AdManager::getCurrentClient()->getClientKey();
            $result = $DB->postToDB($arPostData, ORDERS_TABLE);
            if ($result and is_int($result)) { 
                $orderID = $result;
                foreach(SystemComponent::getAcceptLangs() as $key => $arLang)
                    ActionsLog::getAuthInstance(ActionsLog::SYSTEM_USER, getRealIp())->save(ActionsLog::ACTION_CREATE, 'Создан Заказ №'.$orderID, $key, 'Заказ №'.$orderID, $orderID, 'orders');
                // Start Google Conversion 
                $GoogleConversion = new GoogleConversion($orderID, $arPostData['total_price'], "Maikoff", $Basket->getShippingPrice());
                // write order products
                foreach ($purchases as $arItem) {   
                    $arItem["image"] = isset($arItem["middle_image"]) ? OrderHelper::getProductImageSource($arItem["middle_image"]) : '';
                    /**
                     * @tutorial $product_id - в принтах это идентификатор принта, а в каталоге одежды - это идентификатор таблицы catalog 
                     */
                    $data = array(
                        'order_id'      => $orderID, //+
                        'product_id'    => isset($arItem['print_id']) ? $arItem['print_id'] : $arItem['product_id'], //+
                        'product_idkey' => $arItem['idKey'], //+
                        'substrate_id'  => isset($arItem['substrate_id']) ? $arItem['substrate_id'] : 0, //+
                        'model_id'      => isset($arItem['model_id']) ? $arItem['model_id'] : 0, //+
                        'color_id'      => $arItem['color_id'], //+
                        'size_id'       => $arItem['size_id'], //+
                        'brand_id'      => $arItem['brand_id'], //+
                        'series_id'     => $arItem['series_id'], //+
                        'module'        => $arItem['module'], //+
                        'pcode'         => $arItem['pcode'], //+
                        'title'         => $arItem['title'], //+
                        'substrate_title' => isset($arItem['substrate_title']) ? $arItem['substrate_title'] : '', //+
                        'color_title'   => $arItem['color_title'], //+
                        'size_title'    => $arItem['size_title'], //+
                        'color_hex'     => $arItem['color_hex'], //+
                        'brand_title'   => $arItem['brand_title'],
                        'series_title'  => $arItem['series_title'],
                        'product_image' => $arItem["image"], //+
                        'product_url'   => $UrlWL->buildItemUrl($arItem["arCategory"], $arItem, null, ($arItem['module']=="prints" ? $arItem['color_hex'] : "")), //+
                        'qty'           => $arItem['quantity'], //+
                        'price'         => $arItem['price'], //+
                        'placement'     => isset($arItem['placement']) ? $arItem['placement'] : '', //+
                    );
                    // Add Google Conversion Item
                    $GoogleConversion->addItem(new GoogleConversionItem($orderID, $arItem['pcode'], $arItem['price'], $arItem['quantity'], htmlspecialchars_decode($arItem['title'], ENT_QUOTES)));
                    $DB->postToDB($data, ORDER_PRODUCTS_TABLE);
                }
                $arAttachments = OrderHelper::prepareAttachments($DB, $orderID, $Basket->getFiles(), $Basket->getFilesPath(), $arrPageData["files_path"], $arrPageData["files_url"], $UrlWL->getBaseUrl());  
                // email notifications
                $arData = array_merge($arPostData, $formData);
                $arData['order_id']       = $orderID;
                $arData['shipping']       = getValueFromDB(SHIPPING_TYPES_TABLE, "title", "WHERE `id`={$arData["shipping_id"]}");
                $arData['price']          = $Basket->getTotalPrice();
                $arData['total_price']    = $Basket->getTotalPrice(true);
                $arData['shipping_price'] = $Basket->getShippingPrice();
                $smarty->assign('arData',   $arData);
                $smarty->assign('Basket',   $Basket);
                $smarty->assign('objSettingsInfo', $objSettingsInfo);
                $smarty->assign('HTMLHelper', $HTMLHelper);
                $smarty->assign('UrlWL', $UrlWL);
                $smarty->assign('arrModules', $arrModules);
                $smarty->assign('arPayment', $arrPageData['arPaymentTypes'][$arData['payment_id']]);
                $smarty->assign("attachments", $arAttachments['attachments']);
                $text    = $smarty->fetch('mail/order_admin.tpl');
                $subject = 'Интернет-магазин Maikoff: '.sprintf(NEW_ORDER_NUMBER, $orderID);
                // Send admin email
                if (@sendMultipartMail($objSettingsInfo->notifyEmail, $objSettingsInfo->siteEmail, $subject, $text, $arAttachments['attachmentsToEmail'], "html")){
                    if (!$quick and !empty($arData["email"])) {
                        $text = $smarty->fetch('mail/order_user.tpl');
                        $subject = 'Интернет-магазин Maikoff: '.sprintf(NEW_ORDER_COMPLETED, $orderID);
                        // Send user email
                        @sendMail($arData['email'], $subject, $text, $objSettingsInfo->siteEmail, 'html');
                    }
                }
                // Save gogle conversion data
                $GoogleConversion->setPurchased(true);
                TrackingEcommerce::save($GoogleConversion, true);
                // Drop basket
                $Basket->dropBasket();
                // Save order ID to session
                Checkout::saveOrderID($orderID);
                // Redirect to result page
                Redirect($UrlWL->buildCategoryUrl($arrModules["thanks"]));
            }
        }
    }
} elseif (empty($purchases) and empty($arrPageData['messages']) and empty($arrPageData['errors'])) {
    Redirect('/');
}

$smarty->assign('formData', $formData);