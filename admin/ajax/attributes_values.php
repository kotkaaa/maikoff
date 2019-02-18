<?php defined('WEBlife') or die( 'Restricted access' );

$itemID = !empty($_GET["itemID"]) ? intval($_GET["itemID"]) : false;

$arrPageData['itemID']      = $itemID;
$arrPageData['current_url'] = $arrPageData['admin_url'].($itemID ? "&itemID={$itemID}" : "");
$arrPageData['headTitle']   = ATTRIBUTES.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['files_url']   = UPLOAD_URL_DIR.'attributes/';
$arrPageData['files_path']  = prepareDirPath($arrPageData["files_url"], true);
$arrPageData['headCss'][]   = '<link href="/js/jquery/themes/smoothness/jquery.ui.all.css" type="text/css" rel="stylesheet" />';

if ($itemID and $item = getSimpleItemRow($itemID, ATTRIBUTES_TABLE) and !empty($item)) {
    $item['arValues'] = array();//getRowItemsInKey("id", ATTRIBUTES_VALUES_TABLE, '*', 'WHERE `aid`='.$itemID, 'ORDER BY `order`');
    $SQLquery = "SELECT av.*, "
            . "((SELECT COUNT(*) FROM `".MODEL_ATTRIBUTES_TABLE."` WHERE `aid`=$itemID AND `value`=av.`id`) + "
            . "(SELECT COUNT(*) FROM `".PRINT_ATTRIBUTES_TABLE."` WHERE `aid`=$itemID AND `value`=av.`id`)) AS `used` "
            . "FROM `".ATTRIBUTES_VALUES_TABLE."` av "
            . "WHERE av.`aid`=$itemID "
            . "ORDER BY `order`";
    $result = mysql_query($SQLquery);
    if ($result and mysql_num_rows($result)>0) {
        while ($row = mysql_fetch_assoc($result)) {
            $item['arValues'][$row["id"]] = $row;
        }
    }
//    if (!empty($item['arValues'])) {
//        foreach ($item['arValues'] as $key => $val) {
//            $item['arValues'][$key]['edit'] = $val['id'] ? !(getValueFromDB(MODEL_ATTRIBUTES_TABLE, 'COUNT(id)', 'WHERE `aid`='.$itemID.' AND `value`="'.$val['id'].'"', 'cnt') + getValueFromDB(PRINT_ATTRIBUTES_TABLE, 'COUNT(id)', 'WHERE `aid`='.$itemID.' AND `value`="'.$val['id'].'"', 'cnt')) : false;
//        }
//    }
    $item['arValuesMaxID'] = intval(getValueFromDB(ATTRIBUTES_VALUES_TABLE, 'MAX(id)', 'WHERE `aid`='.$itemID, 'max'));
    $item["seo_path"] = $UrlWL->strToUrl($item["title"]);
    if (!empty($_POST)) {
        $arValues = array();
        if (!empty($_POST['arValues'])) {
            foreach ($_POST['arValues'] as $key => $arValue) {
                $arValue['title'] = mb_strtolower($arValue['title']);
                if (array_key_exists($arValue['title'], $arValues)) {
                    $Validator->addError('Ошибка! Значение "'.$arValue['title'].'" уже присутствует в атрибуте!');
                } else {
                    if (!isset($arValues[$arValue['title']])) $arValues[$arValue['title']]=0;
                    $arValues[$arValue['title']]++;
                }                   
            }
        }
    
        // Values
        $arResults = array();        
        if ($Validator->foundErrors()) {
            $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
        } else {
            $order = 1;
            foreach ($_POST['arValues'] as $key => $arValue){
                $arUnusedKeys = array();
                $valueItem = !empty($arValue['id']) ? getItemRow(ATTRIBUTES_VALUES_TABLE, '*', 'WHERE `id`='.$arValue['id']) : array();
                if (!empty($valueItem) && !empty($valueItem['seo_path']) && $valueItem['seo_path'] == $arValue['seo_path']) {
                    $arResults[] = $valueItem['id'];
                    $arUnusedKeys[] = "seo_path";
                }
                $new_name = '';
                if(isset($arValue['delete_image']) AND !empty($valueItem)) {
                    unlinkImage($valueItem['id'], ATTRIBUTES_VALUES_TABLE, $arrPageData['files_url'], false, false);
//                        ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Удалено изображение для значения аттрибута "'.$arValue['value'].'"', $lang, $arPostData['title'], $itemID, $arrPageData['module']);
                }
                if(isset($_FILES['arValues']['tmp_name'][$key])) {     
                    $iname        = $_FILES['arValues']['name'][$key]['image']; //имя файла до его отправки на сервер (pict.gif)
                    $itmp_name    = $_FILES['arValues']['tmp_name'][$key]['image']; //содержит имя файла во временном каталоге (/tmp/phpV3b3qY)
                    $arExtAllowed = array('jpeg','jpg','gif','png');
                    if($iname AND $itmp_name) {
                        $file_ext = getFileExt($iname);
                        if (in_array($file_ext, $arExtAllowed)) {
                            if (!empty($valueItem)) unlinkImage($valueItem['id'], ATTRIBUTES_VALUES_TABLE, $arrPageData['files_url']);
                            $new_name = createUniqueFileName($arrPageData['files_url'], $file_ext, basename($iname, '.'.$file_ext));
                            $image = WideImage::load($itmp_name);
                            $image->saveToFile($arrPageData['files_path'].$new_name);
//                                ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Добавлено изображение для значения аттрибута "'.$arValue['value'].'"', $lang, $arPostData['title'], $itemID, $arrPageData['module']);
                        }
                    }
                }
                // SEO path manipulation
                if (empty($arValue['seo_path']))
                    $arValue['seo_path'] = $UrlWL->strToUniqueUrl($DB, $arValue['title'], $item["seo_path"], ATTRIBUTES_VALUES_TABLE);

                $arData = array(
                    'aid'          => $itemID,
                    'title'        => $arValue['title'],
                    'title_single' => $arValue['title_single'],
                    'title_multi'  => $arValue['title_multi'],
                    'title_male'   => $arValue['title_male'],
                    'title_female' => $arValue['title_female'],
                    'title_extra'  => $arValue['title_extra'],
                    'seo_path'     => $arValue['seo_path'],
                    'image'        => !empty($new_name) ? $new_name : (!empty($valueItem) ? $valueItem['image'] : ''),
                    'order'        => $order++
                );
                $result = $DB->postToDB($arData, ATTRIBUTES_VALUES_TABLE, !empty($valueItem) ? 'WHERE `id`='.$valueItem['id'] : '', $arUnusedKeys, (!empty($valueItem) ? 'update' : 'insert'), (!empty($valueItem) ? false : true));
                $arResults[] = !empty($valueItem) ? $valueItem['id'] : $result;
            }
            deleteItemsAndFilesFromDB('image', ATTRIBUTES_VALUES_TABLE, 'WHERE `aid`='.$itemID.(!empty($arResults) ? ' AND `id` NOT IN ('.  implode(',', $arResults).')' : ''), $arrPageData['files_url'], true);
            deleteDBLangsSync(MODEL_ATTRIBUTES_TABLE, "WHERE `aid`=$itemID".(!empty($arResults) ? " AND `value` NOT IN(".implode(",", $arResults).")" : ""));
            deleteDBLangsSync(PRINT_ATTRIBUTES_TABLE, "WHERE `aid`=$itemID".(!empty($arResults) ? " AND `value` NOT IN(".implode(",", $arResults).")" : ""));
            setSessionMessage('Данные сохранены!');
            Redirect($arrPageData['current_url']."&task=editItem&itemID={$itemID}&ajax=1");
        }
    }
}

$smarty->assign("item", $item);
