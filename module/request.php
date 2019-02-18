<?php (defined('WEBlife') and $IS_AJAX) or die( 'Restricted access' ); // no direct access

$json     = array();
$items    = array();
$colorIDX = array(0);
$show_products = $UrlWL->getParam("products", true);
$temp_url = UPLOAD_URL_DIR."temp/";
$temp_dir = prepareDirPath($temp_url, true);
$smarty->assign("UrlWL",      $UrlWL);
$smarty->assign("arrModules", $arrModules);
$smarty->assign("show_products", $show_products);

$arrPageData["files_url"]  = UPLOAD_URL_DIR."orders/";
$arrPageData["files_path"] = prepareDirPath($arrPageData["files_url"], true);

$arrPageData["product_substrates"]  = array();
$squery = "SELECT st.*, GROUP_CONCAT(sti.`color_id`) AS `colors` FROM `".SUBSTRATES_TABLE."` st "
        . "LEFT JOIN `".SUBSTRATES_IMAGES_TABLE."` sti ON(sti.`substrate_id`=st.`id`) "
        . "GROUP BY st.`id`";
$result = mysql_query($squery);
if ($result and mysql_num_rows($result)>0) {
    while ($type = mysql_fetch_assoc($result)) {
        if (!empty($type["colors"])) $colorIDX = array_merge_recursive($colorIDX, getArrValueFromDB(COLORS_TABLE, "id", "WHERE `id` IN({$type["colors"]})"));
        $type["colors"] = explode(",", $type["colors"]);
        $arrPageData["product_substrates"][$type["id"]] = $type;
    }
} $arrPageData["product_colors"] = getRowItemsInKey("id", COLORS_TABLE, "*", "WHERE `id` IN(".implode(",", $colorIDX).")");
$arrPageData["print_areas"] = [
    "front" => "спереди",
    "rear"  => "на спине",
    "arm"   => "на рукаве"
];

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $Validator->validateGeneral($_POST["firstname"], "Укажите имя!", "firstname");
    if(!empty($_POST['phone']) && ($_POST['phone'] = PHPHelper::clearePhone($_POST['phone']))){
        $Validator->validatePhone($_POST['phone'], "Укажите телефон!", "phone");
    } else {
        $Validator->addError("Укажите телефон!", "phone");
    } 
    //check attachments
    if(!empty($_FILES) && !empty($_FILES['file']) && $_FILES['file']['tmp_name']) {
        if(!($ext = getFileExt($_FILES['file']['tmp_name'])) || !in_array($ext, $allowed_ext) || $_FILES['file']['size'] > $max_size) {
            $Validator->addError("", "file");
        }
    }
    
    if ($Validator->foundErrors()) {
        $json["errors"] = $Validator->getErrors();
    } else {      
        require_once('include/classes/ActionsLog.php');
        
        $_POST["comment"]   = cleanText($_POST["comment"]);
        
        $arPostData = $_POST;
        $arPostData['created'] = date('Y-m-d H:i:s');
        $arPostData['shipping_id'] = 0;
        $arPostData['user_id'] = OrderHelper::getUserID($DB, $arPostData);
        if(OrderHelper::isBanned($arPostData['user_id'])) {
            $arrPageData["messages"][] = "Сообщение отправлено!";
            $smarty->assign("arrPageData", $arrPageData);
            $json["output"] = $smarty->fetch("module/request.tpl");
        } else {
            $arPostData['type_id'] = OrderHelper::TYPE_REQUEST;
            $arPostData['channel_code'] = AdManager::getCurrentClient()->getClientKey();
            $arPostData['name'] = $_POST["firstname"];        
            $result = $DB->postToDB($arPostData, ORDERS_TABLE);
            if ($result and is_int($result)) {
                $orderID = $result;
                $_POST['orderID'] = $orderID;
                $arData             = $_POST;
                $arData["sitename"] = WLCMS_HTTP_HOST;
                $arData["items"]    = isset($_POST["items"]) ? $_POST["items"] : array();
                if (!empty($arData["items"])) {
                    foreach ($arData["items"] as $arKey=>$arItem) {
                        if (empty($arItem["type"])) unset($arData["items"][$arKey]);
                    } $arData["items"] = array_values($arData["items"]);
                }                 
                if (!empty($arData["items"])) {
                    foreach ($arData["items"] as $product) {
                        $comment = '';
                        if(!empty($product['print'])) {
                            foreach($product['print'] as $placement) {
                                //для рукава колонок нет, а на фроненде есть
                                if($placement == 'arm') {
                                    $side = 'рукав';
                                } else {
                                    $side = PrintProduct::getSides($placement);
                                    $side = $side['title'];
                                }                            
                                $comment.= $side.': '.$product[$placement]."\n";
                            }
                        } else $comment = 'без нанесения';
                        /**
                         * @tutorial $product_id - в принтах это идентификатор принта, а в каталоге одежды - это идентификатор таблицы catalog 
                         */
                        $data = array(
                            'order_id'      => $orderID, //+
                            'product_id'    => 0, //+
                            'product_idkey' => '', //+
                            'substrate_id'  => 0, //+
                            'color_id'      => 0, //+
                            'size_id'       => 0, //+
                            'brand_id'      => 0, //+
                            'series_id'     => 0, //+
                            'module'        => 'catalog', //+
                            'pcode'         => '', //+
                            'title'         => '', //+
                            'substrate_title' => $product['type'], //+
                            'color_title'   => $product['color'], //+
                            'size_title'    => '', //+
                            'color_hex'     => '', //+
                            'brand_title'   => '',
                            'series_title'  => '',
                            'product_image' => '', //+
                            'product_url'   => '', //+
                            'qty'           => $product['qty'], //+
                            'price'         => 0, //+
                            'placement'     => 'front', //+
                            'admin_comment' => $comment,
                        );
                        $DB->postToDB($data, ORDER_PRODUCTS_TABLE);
                    }
                }        
                $arAttachments = OrderHelper::prepareAttachments($DB, $orderID, (!empty($_POST["attachments"]) ? $_POST["attachments"] : array()), $temp_dir, $arrPageData["files_path"], $arrPageData["files_url"], $UrlWL->getBaseUrl());
                $subject     = "Заявка №{$orderID}. Новая заявка на просчет!";
                $smarty->assign("arrPageData", $arrPageData);
                $smarty->assign("arData",      $arData);
                $smarty->assign("subject",     $subject);
                $smarty->assign("attachments", $arAttachments['attachments']);            
                $text = $smarty->fetch("mail/request_admin.tpl");
                if (@sendMultipartMail($objSettingsInfo->notifyEmail, $objSettingsInfo->siteEmail, $subject, $text, $arAttachments['attachmentsToEmail'], "html")) {
                    $arrPageData["messages"][] = "Сообщение отправлено!";
                    $smarty->assign("arrPageData", $arrPageData);
                    $json["output"] = $smarty->fetch("module/request.tpl");
                }
            }
        }
    } die(json_encode($json));
}

$smarty->assign("arrPageData", $arrPageData);
die($smarty->fetch("module/request.tpl"));