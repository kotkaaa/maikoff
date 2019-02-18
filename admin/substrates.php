<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access


# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
// SET from $_GET Global Array Item ID Var = integer
$itemID        = (isset($_GET['itemID']) and intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0;
$copyID        = (isset($_GET['copyID']) and intval($_GET['copyID'])) ? intval($_GET['copyID']) : 0;
$item          = array(); // Item Info Array
$items         = array(); // Items Array of items Info arrays
$hasAccess     = $UserAccess->getAccessToModule($arrPageData['module']); 
// /////////////////////// END OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID']        = $itemID;
$arrPageData['current_url']   = $arrPageData['admin_url'];
$arrPageData['arrBreadCrumb'] = array();
$arrPageData['arrParent']     = array();
$arrPageData['arSizes']       = getComplexRowItems(SIZES_TABLE, "*", "", "`order`");
$arrPageData['headTitle']     = SUBSTRATES.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['files_url']     = UPLOAD_URL_DIR.$module.'/';
$arrPageData['files_path']    = prepareDirPath($arrPageData['files_url'], true);
$arrPageData['arGrids']       = getComplexRowItems(SIZE_GRIDS_TABLE, "*");
$arrPageData['arColors']      = array();
$squery = "SELECT c.`id`, c.`title`, c.`seo_path`, c.`hex`, "
        . "IF(pti.`order` IS NOT NULL, pti.`order`, c.`order`) AS `order`, "
        . "COUNT(pti.`id`) AS `cnt` "
        . "FROM `".COLORS_TABLE."` c "
        . "LEFT JOIN `".SUBSTRATES_IMAGES_TABLE."` pti ON(pti.`color_id`=c.`id`) "
        . "GROUP BY c.`id` ORDER BY `cnt` DESC, `order`";
$result = mysql_query($squery);
if ($result and mysql_num_rows($result)>0) {
    while ($row = mysql_fetch_assoc($result)) {
        $arrPageData['arColors'][] = $row;
    }
}
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
require_once 'include/classes/product/PrintProduct.php';
$item_title = $itemID ? getValueFromDB(SUBSTRATES_TABLE, 'title', 'WHERE `id`='.$itemID) : '';
// SET Reorder
if ($task=='reorderItems' and !empty($_POST) and isset($_POST['submit_order'])) {
    if ($hasAccess) {
        if (!empty($_POST['arPrices'])) {
            $result = updateItems(array('price'=>$_POST['arPrices']), $_POST['arPrices'], 'id', SUBSTRATES_TABLE, array('action'=>ActionsLog::ACTION_EDIT, 'comment'=>'Изменена цена', 'lang'=>$lang, 'module'=>$arrPageData['module']));
            if ($result===true) {
                setSessionMessage('Новые цены сохранены!'); 
            } elseif($result) {
                setSessionErrors($result);
            }
        } Redirect($arrPageData['current_url']);
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Set Active Status Item
elseif($itemID && $task=='publishItem' && isset($_GET['status'])) {
    if($hasAccess) {
        if($_GET['status'] == 0 && getValueFromDB(PRINTS_TABLE, 'COUNT(id)', 'WHERE `substrate_id`='.$itemID)>0) {
            setSessionErrors('Новое состояние <font color="red">НЕ было сохранено</font>! Нельзя отключить подложку, которая используется в товарах по умолчанию!');
        } else {
            $result = updateRecords(SUBSTRATES_TABLE, "`active`='{$_GET['status']}'", 'WHERE `id`='.$itemID);
            if($result===false) {
                setSessionErrors('Новое состояние <font color="red">НЕ было сохранено</font>! Error Update: '. mysql_error());
            } elseif($result) {
                setSessionMessage('Новое состояние успешно сохранено!');
                ActionsLog::getInstance()->save(ActionsLog::ACTION_PUBLICATION, 'Изменена публикация страницы на "'.($_GET['status']==1 ? 'Опубликована' : 'Неопубликована' ).'"', $lang, getValueFromDB(SUBSTRATES_TABLE, 'title', 'WHERE `id`='.$itemID), $itemID, $arrPageData['module']);
            }
        }
        Redirect($arrPageData['current_url']);
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Delete Item
elseif ($itemID AND $task=='deleteItem') {
    if ($hasAccess){
        if(getValueFromDB(PRINT_ASSORTMENT_TABLE, 'COUNT(id)', 'WHERE `substrate_id`='.$itemID)>0) {
            setSessionErrors('Новое состояние <font color="red">НЕ было сохранено</font>! Нельзя удалить подложку, которая используется в товарах!');
        } else {
            $result = deleteRecords(SUBSTRATES_TABLE, 'WHERE `id`='.$itemID);
            if (!$result) {
                setSessionErrors('Данные не удалось удалить. Возможная причина - <p>MySQL Error Delete: '.mysql_errno().'</b> Error:'.mysql_error().'</p>');
            } else {
                $files = getRowItems(SUBSTRATES_IMAGES_TABLE, '*', '`substrate_id`='.$itemID);
                foreach($files as $file) {
                    if($file['img_front']) unlink(UPLOAD_DIR.DS.$module.DS.$file['img_front']);
                    if($file['img_rear']) unlink(UPLOAD_DIR.DS.$module.DS.$file['img_rear']);
                }
                deleteRecords(SUBSTRATES_IMAGES_TABLE, 'WHERE `substrate_id`='.$itemID);
                deleteRecords(SUBSTRATES_SIZES_TABLE, 'WHERE `substrate_id`='.$itemID);                
                PrintProduct::deleteSpoolBySubstrate($itemID);
                setSessionMessage('Подложка и все связанные данные удалены!');
            }                     
        }
        Redirect($arrPageData['current_url']);   
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Delete Item
elseif ($itemID and $task=='fileUpload') {
    $json      = array("filename" => $arrPageData["files_url"]."noimage.jpg");
    $colorID   = !empty($_GET["colorID"])   ? intval($_GET["colorID"])          : 0;
    $placement = !empty($_GET["placement"]) ? trim($_GET["placement"])          : PrintProduct::SIDE_FRONT;
    $filename  = !empty($_GET["filename"])  ? basename(trim($_GET["filename"])) : "";
    if ($colorID and !empty($filename)) {
        $whereOptions = "WHERE `substrate_id`=$itemID AND `color_id`=$colorID";
        $colname = "img_".$placement;
        $duplicate = (bool)getValueFromDB(SUBSTRATES_IMAGES_TABLE, "COUNT(*)", $whereOptions);
        $exists = $new_name = getValueFromDB(SUBSTRATES_IMAGES_TABLE, $colname, $whereOptions);
        $temp_url = UPLOAD_URL_DIR."temp/";
        $temp_dir = prepareDirPath($temp_url);
        if (file_exists($temp_dir.$filename)) {
            $ext      = getFileExt($filename);
            $itemName = getValueFromDB(SUBSTRATES_TABLE, 'IF(`seo_path`="", `title`, `seo_path`)', 'WHERE `id`='.$itemID, 'name');
            $colorName = getValueFromDB(COLORS_TABLE, 'IF(`seo_path`="", `title`, `seo_path`)', 'WHERE `id`='.$colorID, 'name');
            $new_name = setFilePathFormat(PrintProduct::createSubstrateColorFileName($itemID, $colorID, $placement, $itemName.PrintProduct::SEOTEXT_SEP.$colorName)).".".$ext;
            if (rename($temp_dir.$filename, $arrPageData["files_path"].$new_name)) {
                $DB->postToDB(array(
                    $colname => $new_name,
                    "substrate_id" => $itemID,
                    "color_id" => $colorID
                ), SUBSTRATES_IMAGES_TABLE, ($duplicate ? $whereOptions : ""), array(), ($duplicate ? "update" : "insert"));
                $json["filename"] = $arrPageData["files_url"].$new_name;
                PrintProduct::deleteSpoolBySubstrateColor($itemID, $colorID, $placement);
            } unlinkFile($filename, $temp_dir);
        } 
        // если старое имя отличается от нового - то удаляем старый файл
        if ($exists && $new_name && $exists != $new_name){
            unlinkFile($exists, $arrPageData["files_path"]);
            PrintProduct::deleteSpoolBySubstrateColor($itemID, $colorID, $placement);
        }
    } die(json_encode($json));
}
//Copy item
elseif ($itemID and $task=='unLinkFile') {
    $colorID   = !empty($_GET["colorID"])   ? intval($_GET["colorID"])          : 0;
    $placement = !empty($_GET["placement"]) ? trim($_GET["placement"])          : PrintProduct::SIDE_FRONT;
    $json      = array("filename"=>$arrPageData["files_url"]."noimage.jpg");
    if ($colorID) {
        $whereOptions = "WHERE `substrate_id`=$itemID AND `color_id`=$colorID";
        $colname = "img_".$placement;
        $exists  = getValueFromDB(SUBSTRATES_IMAGES_TABLE, $colname, $whereOptions);
        $file_exists = is_file($arrPageData["files_path"].$exists);
        $json["filename"] = $exists;
        if (!empty($exists)) {
            updateRecords(SUBSTRATES_IMAGES_TABLE, "`$colname`=NULL", $whereOptions); // очищаем выбранное поле
            if ($file_exists) unlinkFile($exists, $arrPageData["files_path"]); // если файл существует - тоже удаляем
            PrintProduct::deleteSpoolBySubstrateColor($itemID, $colorID, $placement);
            $json["filename"] = $arrPageData["files_url"]."noimage.jpg";
        }
        // удаляем всю запись если не заполнено ни одно изображение
        $record = (int)getValueFromDB(SUBSTRATES_IMAGES_TABLE, "COUNT(*)", $whereOptions." AND (`img_front` IS NOT NULL OR `img_rear` IS NOT NULL)", "cnt");
        if (!$record) deleteRecords (SUBSTRATES_IMAGES_TABLE, $whereOptions);
    } die(json_encode($json));
}
//Copy item
elseif ($copyID and $task=='addItem') {
    if ($hasAccess) {
        $arrPageData['messages'][] = 'Запись успешно скопирована!';
    } else {
        $arrPageData['errors'][] = $UserAccess->getAccessError(); 
    }
}
// Insert Or Update Item in Database
elseif (!empty($_POST) and ($task=='addItem' OR $task=='editItem')) {
    if ($hasAccess) {
        $arUnusedKeys = array('id', 'old_seo_path');
        $query_type   = $itemID ? 'update'              : 'insert';
        $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';
        $Validator->validateGeneral($_POST['title'], 'Вы не ввели названия страницы!!!');
        $Validator->validateGeneral($_POST['title_s'], 'Вы не ввели название в единичном числе!!!');
        $Validator->validateGeneral($_POST['title_p'], 'Вы не ввели название в множественном числе!!!');
        $Validator->validateGeneral($_POST['price'], 'Вы не ввели стоимость!!!');
        $Validator->validateArray($_POST['sizes'], 'Вы не указали ни один размер!!!');
        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            // SEO path manipulation
            $_POST['seo_path'] = $UrlWL->strToUniqueUrl($DB, (empty($_POST['seo_path']) ? $_POST['title'] : $_POST['seo_path']), $module, SUBSTRATES_TABLE, $itemID, empty($itemID));
            // copy post data
            $arPostData = $_POST;
            $arPostData["dimensions"] = !empty($arPostData["dimensions"]) ? PrintProduct::dimensionsToDB($arPostData["dimensions"]) : '';
            $arPostData["old_seo_path"] = $itemID ? getValueFromDB(SUBSTRATES_TABLE, 'seo_path', 'WHERE `id`='.$itemID) : '';
            $result = $DB->postToDB($arPostData, SUBSTRATES_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
            if ($result) {
                if (!$itemID and $result and is_int($result)) $itemID = $result;
                if (mysql_affected_rows()) {
                    $item_title = getValueFromDB(SUBSTRATES_TABLE, 'title', 'WHERE `id`='.$itemID);
                    if ($task=='addItem') {
                        foreach (SystemComponent::getAcceptLangs() as $key => $arLang)
                            ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создано "'.$item_title.'"', $key, $item_title, $itemID, $arrPageData['module']);
                    } else {
                         ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактировано "'.$item_title.'"', $lang, $item_title, $itemID, $arrPageData['module']);
                    }
                    PrintProduct::deleteSpoolBySubstrate($itemID);
                    if($arPostData["old_seo_path"] && $arPostData["old_seo_path"] != $arPostData['seo_path']){
                        PrintProduct::updateAssortmentSeoPathes($itemID, 0);
                    }
                } setSessionMessage('Запись успешно сохранена!');
                // сохранение атрибутов
                PHPHelper::saveAttributes($itemID, $DB, SUBSTRATES_ATTRIBUTES_TABLE, 'sid', $arPostData);
                // substrate to size relations
                $arIdx = array(0);
                if (!empty($_POST["sizes"])) {
                    foreach ($_POST["sizes"] as $sizeID) {
                        $exists = (bool)getValueFromDB(SUBSTRATES_SIZES_TABLE, "COUNT(*)", "WHERE `substrate_id`=$itemID AND `size_id`=$sizeID", "cnt");
                        $where  = $exists ? "WHERE `substrate_id`={$itemID} AND `size_id`={$sizeID}" : "";
                        $qtype  = $exists ? "update" : "insert";
                        $unkeys = $exists ? array("substrate_id") : array();
                        $result = $DB->postToDB(array("substrate_id"=>$itemID, "size_id"=>$sizeID), SUBSTRATES_SIZES_TABLE, $where, $unkeys, $qtype);
                        if ($result) $arIdx[] = $sizeID;
                    }
                } deleteRecords(SUBSTRATES_SIZES_TABLE, "WHERE `substrate_id`=$itemID AND `size_id` NOT IN(".implode(",", $arIdx).")");
                // reorder images
                if (isset($_POST["arImages"]) and !empty($_POST["arImages"])) {
                    $i = 0;
                    foreach ($_POST["arImages"] as $colorID) {
                        $exists = getValueFromDB(SUBSTRATES_IMAGES_TABLE, "id", "WHERE `substrate_id`=$itemID AND `color_id`=$colorID");
                        if ($exists) {
                            $i++;
                            updateRecords(SUBSTRATES_IMAGES_TABLE, "`order`=$i", "WHERE `substrate_id`=$itemID AND `color_id`=$colorID");
                        }
                    }
                }
                Redirect($arrPageData['current_url'].(isset($_POST['submit_add']) ? '&task=addItem' : ((isset($_POST['submit_apply']) AND $itemID) ? '&task=editItem&itemID='.$itemID : '')) );
            } else {
                $arrPageData['errors'][] = 'Запись <font color="red">НЕ была сохранена</font>!';
            }
        }
    } else $arrPageData['errors'][] = $UserAccess->getAccessError();
}

if ($task=='addItem' OR $task=='editItem'){
    if (!$hasAccess) {
        setSessionErrors($UserAccess->getAccessError()); 
        Redirect($arrPageData['current_url']);
    }
    
    // select2 for attributes
    $arrPageData['headCss'][]     = '<link href="/js/libs/select2/select2.css" type="text/css" rel="stylesheet"/>';
    $arrPageData['headScripts'][] = '<script src="/js/libs/select2/select2.js" type="text/javascript"></script>';
    $arrPageData['headScripts'][] = '<script src="/js/libs/select2/select2_locale_ru.js" type="text/javascript"></script>';     
    
    PHPHelper::prepareAttrGroups($arrPageData);
    
    if (!$itemID) {
        if ($copyID) {
            $item = getSimpleItemRow($copyID, SUBSTRATES_TABLE);
            $item["sizes"] = getArrValueFromDB(SUBSTRATES_SIZES_TABLE, "size_id", "WHERE `substrate_id`=".$copyID);
            $item["images"] = array();
            $item["dimensions"] = PrintProduct::dimensionsFromDB(unScreenData($item["dimensions"]));
            //attributes
            PHPHelper::prepareItemAttributes($item, SUBSTRATES_ATTRIBUTES_TABLE, 'sid', $arrPageData['attrGroups']);
        } else { 
            $item = array_combine_multi($DB->getTableColumnsNames(SUBSTRATES_TABLE), '');
            $item["sizes"] = array();
            $item["images"] = array();
            $item["dimensions"] = PrintProduct::getDimensions();
            $item['attributes'] = $item['attrGroups'] = array();
        }
        $item['arHistory'] = array();
    } elseif ($itemID) {
        $query = "SELECT * FROM ".SUBSTRATES_TABLE." WHERE id = $itemID LIMIT 1";
        $result = mysql_query($query);
        if (!$result) {
            $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
        } elseif (!mysql_num_rows($result)) {
            $arrPageData['errors'][] = "SELECT OPERATIONS: No this Item in DataBase";
        } else {
            $item = mysql_fetch_assoc($result);
            $item["images"] = getRowItemsInKey("color_id", SUBSTRATES_IMAGES_TABLE, "*", "WHERE `substrate_id`=$itemID");
            $item["dimensions"] = PrintProduct::dimensionsFromDB(unScreenData($item["dimensions"]));
            $item["sizes"] = getArrValueFromDB(SUBSTRATES_SIZES_TABLE, "size_id", "WHERE `substrate_id`=".$itemID);
            //attributes
            PHPHelper::prepareItemAttributes($item, SUBSTRATES_ATTRIBUTES_TABLE, 'sid', $arrPageData['attrGroups']);
            $item['arHistory'] = ActionsLog::getInstance()->getHistory(array('modules'=>array($arrPageData['module']), 'oid'=>$item['id'], 'langs'=>array($lang)));
        }
    }
    if (!empty($_POST)) $item = array_merge($item, $_POST);
    $arrPageData['arrBreadCrumb'][] = array('title'=>($task=='addItem' ? ADMIN_ADD_NEW_PAGE : ADMIN_EDIT_PAGE));
} else {
    $query  = "SELECT st.*, '1' AS `edit`, ".PHP_EOL
            . "IF(img_front IS NOT NULL, img_front, img_rear) AS `image`, ".PHP_EOL
            . "GROUP_CONCAT(DISTINCT s.`title`) AS `sizes` ".PHP_EOL
            . "FROM `".SUBSTRATES_TABLE."` st ".PHP_EOL
            . "LEFT JOIN `".SUBSTRATES_IMAGES_TABLE."` sti ON(sti.`substrate_id`=st.`id` AND sti.`order`='1') ".PHP_EOL
            . "LEFT JOIN `".SUBSTRATES_SIZES_TABLE."` sts ON(sts.`substrate_id`=st.`id`) ".PHP_EOL
            . "LEFT JOIN `".SIZES_TABLE."` s ON(s.`id`=sts.`size_id`) ".PHP_EOL
            . "GROUP BY st.`id`";
    $result = mysql_query($query);
    if (!$result) {
        $arrPageData['errors'][] = "SELECT OPERATIONS: " . mysql_error();
    } else {
        while ($row = mysql_fetch_assoc($result)) {
            $row["image"] = (!empty($row["image"]) and file_exists($arrPageData["files_path"].$row["image"])) ? $arrPageData["files_url"].$row["image"] : $arrPageData["files_url"]."noimage.jpg";
            $items[] = $row;
        }
    }
}
// Include Need CSS and Scripts For This Page To Array
$arrPageData['headScripts'][] = '<script src="/js/libs/blueimp-file-upload/js/jquery.fileupload.min.js" type="text/javascript"></script>';
//$arrPageData['headScripts'][] = '<script src="/js/libs/blueimp-file-upload/js/jquery.fileupload-image.min.js" type="text/javascript"></script>';
$arrPageData['headScripts'][] = '<script src="/js/libs/blueimp-file-upload/js/jquery.iframe-transport.min.js" type="text/javascript"></script>';
$arrPageData['headScripts'][] = '<script src="/js/admin/substrates.js" type="text/javascript"></script>';
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
///////////////////// SMARTY BASE PAGE VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$smarty->assign('item',          $item);
$smarty->assign('items',         $items);
//\\\\\\\\\\\\\\\\\ END SMARTY BASE PAGE VARIABLES /////////////////////////////
# ##############################################################################

/*
DROP TABLE IF EXISTS `ru_substrates`;
CREATE TABLE IF NOT EXISTS `ru_substrates` (
  `id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `title_short` varchar(255) NOT NULL DEFAULT '',
  `price` float(11,2) NOT NULL DEFAULT '0.00',
  `dimensions` text,
  `meta_descr` text NOT NULL,
  `meta_key` text NOT NULL,
  `meta_robots` varchar(255) NOT NULL DEFAULT '',
  `seo_path` varchar(255) NOT NULL DEFAULT '',
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  `size_grid_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_title` (`title`),
  KEY `idx_price` (`price`),
  KEY `idx_path` (`seo_path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
 * 
DROP TABLE IF EXISTS `substrates_images`;
CREATE TABLE IF NOT EXISTS `substrates_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `substrate_id` int(11) unsigned NOT NULL DEFAULT '0',
  `color_id` int(11) unsigned NOT NULL DEFAULT '0',
  `img_front` varchar(255) DEFAULT NULL,
  `img_rear` varchar(255) DEFAULT NULL,
  `order` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_sid` (`substrate_id`),
  KEY `idx_cid` (`color_id`),
  KEY `idx_order` (`order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
 * 
DROP TABLE IF EXISTS `substrates_sizes`;
CREATE TABLE IF NOT EXISTS `substrates_sizes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `substrate_id` int(11) unsigned NOT NULL DEFAULT '0',
  `size_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_sid` (`substrate_id`),
  KEY `idx_sid` (`size_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
*/
