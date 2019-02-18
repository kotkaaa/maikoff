<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access


require_once 'include/classes/product/PrintProduct.php';
# ##############################################################################
// //////////////////////// OPERATION PAGE VARIABLE \\\\\\\\\\\\\\\\\\\\\\\\\\\\
$printID = (isset($_GET['printID']) && intval($_GET['printID'])) ? intval($_GET['printID']) : 0;
$itemID  = (isset($_GET['itemID']) && intval($_GET['itemID'])) ? intval($_GET['itemID']) : 0; //file id
$item    = array(); //принт
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ///////////// REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\\
$arrPageData['itemID'] = $itemID;
$arrPageData['printID'] = $printID;
$arrPageData['file_url'] = UPLOAD_URL_DIR.'prints/';
$arrPageData['file_path'] = prepareDirPath($arrPageData['file_url']);
$arrPageData['substrate_url'] = UPLOAD_URL_DIR.'substrates/';
$arrPageData['substrate_path'] = prepareDirPath($arrPageData['substrate_url']);
$arrPageData['current_url'] = $arrPageData['admin_url'].'&printID='.$printID;
$arrPageData['headTitle'] = ($itemID ? 'Добавление нового' : 'Редактирование').' логотипа'.$arrPageData['seo_separator'].$arrPageData['headTitle'];
$arrPageData['arAssortment'] = PrintProduct::getAssortment($printID, $itemID);
$arrPageData['arColors'] = getRowItemsInKey('id', COLORS_TABLE, '*');
$arrPageData['arPrint'] = getSimpleItemRow($printID, PRINTS_TABLE);
// ////////// END REQUIRED LOCAL PAGE REINIALIZING VARIABLES \\\\\\\\\\\\\\\\\\\
# ##############################################################################


# ##############################################################################
// ////////////////////////// POST AND GET OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\
// Delete Item
if($itemID && $task=='deleteItem') {      
    //проверяем нет ли в этом принте дефолтного
    if($arrPageData['arPrint']['file_id'] == $itemID) {
        setSessionErrors('Ошибка! Нельзя удалять логотип, который назначен как дефолтный!');       
    } else if(PHPHelper::deletePrintFile($itemID, $printID, $arrPageData['file_path'])){
        PrintProduct::deleteSpoolByPrintLogo($printID, $itemID);
        setSessionMessage('Логотип и его ассортимент удален!');
    } else {
        setSessionErrors('Ошибка удаления логотипа и его ассортимента!');
    }
    Redirect('/admin.php?module=prints&task=editItem&itemID='.$printID.'#logos');
}
else if(!empty($_POST) && ($task=='addItem' OR $task=='editItem')) {
    // copy post data
    $arPostData = $_POST;
    //var_dump($_POST);
    $arUnusedKeys = array('id');
    $query_type   = $itemID ? 'update'            : 'insert';
    $conditions   = $itemID ? 'WHERE `id`='.$itemID : '';

    //проверяем есть ли во входных данных дефолтная подложка и проверяем по каждому типу дефолтный цвет
    $defColorID = 0;
    if(!empty($arPostData['arAssortment'])) {
        foreach($arPostData['arAssortment'] as $substrateID => $assort) { 
            if(!isset($assort['color_id'])) $assort['color_id'] = $arPostData['arAssortment'][$substrateID]['color_id'] = $_POST['arAssortment'][$substrateID]['color_id'] = 0;            
            if($assort['arSettings']['active']) {
                //если во входных данных нет дефолтного цвета, то смотрим в базе
                if($assort['color_id'] == 0 && ($defColorID = getValueFromDB(PRINT_ASSORTMENT_TABLE, 'color_id', 'WHERE `id`='.$assort['id'])) == 0) {
                    $Validator->addError('Ошибка! Установите дефолтный цвет для подложки '.$arrPageData['arAssortment'][$substrateID]['title'].'!');
                } 
            //если других настроек нет
            } else if($assort['isdefault'] && getValueFromDB(PRINT_ASSORTMENT_COLORS_TABLE, 'COUNT(id)', 'WHERE `assortment_id`='.$assort['id'].' AND `isdefault`=1 AND `file_id`<>'.$itemID, 'cnt') == 0) {
                $Validator->addError('Ошибка! Нельзя удалить дефолтную подложку!');
            }
        }
    }
        
    //если нет ошибок в предыдущих проверках, то проверяем и грузим логотип
    if(!$Validator->foundErrors()) {
        if($task == 'addItem' && (empty($_FILES['filename']) || !$_FILES['filename']['name'])) {
            $Validator->addError('Не выбран логотип!');
        } else if($_FILES['filename']['name']) {
            $arPostData['filename'] = PrintProduct::createLogoFileName($itemID, strtolower(setFilePathFormat($_FILES['filename']['name'])));
            if(!$itemID && file_exists($arrPageData['file_path'].$arPostData['filename'])) {
                $Validator->addError('Логотип с таким именем уже существует!');
            } else if (move_uploaded_file($_FILES['filename']['tmp_name'], $arrPageData['file_path'].$arPostData['filename'])) {
                if(isset($_POST['logo_delete']) && $_POST['logo_delete']==$arPostData['filename']){
                    unset($arPostData['filename']);
                }
                if($itemID) PrintProduct::deleteSpoolByPrintLogo($printID, $itemID);
            } else {
                $Validator->addError('Ошибка загрузки файла!');
            }
        } else {
            $arUnusedKeys[] = 'filename';
        }
    }
    
    if ($Validator->foundErrors()) {
        $arrPageData['errors'][] = "<font color='#990033'>Пожалуйста, введите правильное значение :  </font>".$Validator->getListedErrors();
    } else {
        $arPostData['print_id'] = $printID;
        if (empty($arPostData['createdDate'])) $arPostData['createdDate'] = date('Y-m-d');
        if (empty($arPostData['createdTime'])) $arPostData['createdTime'] = date('H:i:s');
        $arPostData['created'] = "{$arPostData['createdDate']} {$arPostData['createdTime']}";
        //анлинкаем старое лого если надо
        if(isset($_POST['logo_delete']) && $_POST['logo_delete']){
            unlinkImage($itemID, PRINTFILES_TABLE, $arrPageData['file_path'], false, false, 'filename');
        }
        $result = $DB->postToDB($arPostData, PRINTFILES_TABLE, $conditions,  $arUnusedKeys, $query_type, ($itemID ? false : true));
        if ($result){
            $affected = mysql_affected_rows();
            if (!$itemID && $result && is_int($result)){
                $itemID = $result;
                // если новый файл то нужно переименовать логотип по новым стандартам
                if(isset($arPostData['filename']) && $arPostData['filename'] != ($new_filename = PrintProduct::createLogoFileName($itemID, str_replace(PrintProduct::createLogoFileName(0, ''), '', $arPostData['filename'])))) {
                    if (rename($arrPageData['file_path'].$arPostData['filename'], $arrPageData['file_path'].$new_filename)) {
                        $arPostData['filename'] = $new_filename;
                        updateRecords(PRINTFILES_TABLE, '`filename`="'.$new_filename.'"', 'WHERE `id`='.$itemID.' LIMIT 1');
                    }
                }
            }
            if ($affected) {
                if ($task=='addItem'){
                    ActionsLog::getInstance()->save(ActionsLog::ACTION_CREATE, 'Создан логотип "'.$arPostData['title'].'"', SystemComponent::getAcceptLangs(), $arPostData['title'], $itemID, $arrPageData['module']);
                } else {
                    ActionsLog::getInstance()->save(ActionsLog::ACTION_EDIT, 'Отредактирован логотип "'.$arPostData['title'].'"', $lang, $arPostData['title'], $itemID, $arrPageData['module']);
                }  
            }                    
            //сохранение ассортимента
            if(!empty($arPostData['arAssortment'])) {
                $arAssortIDX = array();
                $arSettingsIDX = array();
                foreach($arPostData['arAssortment'] as $substrateID => $assort) {
                    $assortID = intval($assort['id']); 
                    $arAssortIDX[] = $assortID;
                    if($assort['arSettings']['active']) {
                        //если задан дефолтный цвет, то отключаем остальные дефолтные
                        if($assort['color_id'] && $assort['color_id'] != $defColorID) {
                            updateRecords(PRINTS_TABLE, 'file_id='.$itemID, 'WHERE id='.$printID);
                            updateRecords(PRINT_ASSORTMENT_TABLE, '`color_id`='.$assort['color_id'], 'WHERE `id`='.$assortID);
                            updateRecords(PRINT_ASSORTMENT_COLORS_TABLE, '`isdefault`=0', 'WHERE `assortment_id`='.$assortID.' AND `color_id`<>'.$assort['color_id'].' AND `isdefault`=1');
                        }
                        //сохранение настроек
                        $arSettings = $assort['arSettings'];
                        $arSettings['assortment_id'] = $assortID;
                        $arSettings['file_id'] = $itemID;
                        $settingsID = $DB->postToDB($arSettings, PRINT_ASSORTMENT_SETTINGS_TABLE, ($arSettings['id'] ? 'WHERE `id`='.$arSettings['id'] : ''), array('id'), ($arSettings['id'] ? 'update' : 'insert'));                        
                        if(!$arSettings['id'] && $settingsID && is_int($settingsID)) {
                            $arSettings['id'] = $settingsID;
                        }
                        $arSettingsIDX[$assortID] = $arSettings['id'];
                        $arColorsIDX = array();
                        if(!empty($assort['colors'])) {
                            foreach($assort['colors'] as $colorID) {
                                //проверяем есть ли уже такой цвет, если нету, то запендюриваем
                                $assortColor = getItemRow(PRINT_ASSORTMENT_COLORS_TABLE, '*', 'WHERE `assortment_id`='.$assortID.' AND `color_id`='.$colorID.' AND (`file_id`='.$itemID.' OR `active`=0)');
                                if(empty($assortColor)) {
                                    $assortColor = array(
                                        'id' => 0,
                                        'assortment_id' => $assortID,
                                        'color_id' => $colorID,
                                        'order' => getMaxPosition($assortID, 'order', 'assortment_id', PRINT_ASSORTMENT_COLORS_TABLE),                                                      
                                    );
                                }   
                                $assortColor['file_id'] = $itemID;                        
                                $assortColor['active'] = 1;                        
                                $assortColor['isdefault'] = ($assort['color_id']==$colorID ? 1 : 0);                        
                                $assortResult = $DB->postToDB($assortColor, PRINT_ASSORTMENT_COLORS_TABLE, ($assortColor['id'] ? 'WHERE `id`='.$assortColor['id'] : ''), array('id'), ($assortColor['id'] ? 'update' : 'insert'));
                                if(!$assortColor['id'] && $assortResult && is_int($assortResult)) {
                                    $assortColor['id'] = $assortResult;
                                }
                                $arColorsIDX[] = $assortColor['id'];
                            }                        
                        }
                        //снимаем с публикации цвета если они есть
                        updateRecords(PRINT_ASSORTMENT_COLORS_TABLE, '`active`=0', 'WHERE `assortment_id`='.$assortID.' AND `file_id`='.$itemID.($arColorsIDX ? ' AND `id` NOT IN('.implode(',', $arColorsIDX).')' : ''));
                    }                     
                }
                //снимаем с публикации цвета если они есть
                updateRecords(PRINT_ASSORTMENT_SETTINGS_TABLE, '`active`=0', 'WHERE `assortment_id` IN('.implode(',', $arAssortIDX).') AND `file_id`='.$itemID.($arSettingsIDX ? ' AND `id` NOT IN('.implode(',', $arSettingsIDX).')' : ''));
            }            
            setSessionMessage('Запись успешно сохранена!');
            Redirect($arrPageData['current_url'].'&task=editItem&itemID='.$itemID);
        } else {
            $arrPageData['errors'][] = 'Запись <font color="red">НЕ была сохранена</font>!';          
        }
    }
}
// \\\\\\\\\\\\\\\\\\\\\\\ END POST AND GET OPERATIONS /////////////////////////
# ##############################################################################


# ##############################################################################
// ///////////////////////// LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
if($printID == 0) {
    $arrPageData['errors'][] = 'Ошибка! Не все данные переданы!';
} else {    
    //готовим библиотеки для вывода данных
    //выбираем все активные сохраненные цвета для этого принта и разбиваем на активные и недоступные исходя из того к какому файлу они принадлежат
    $arColors = array('used' => array(), 'unavailable' => array());
    $query = 'SELECT pa.`substrate_id`, pc.`color_id`, pc.`active`, pc.`file_id` FROM '.PRINT_ASSORTMENT_TABLE.' pa '
            .'LEFT JOIN '.PRINT_ASSORTMENT_COLORS_TABLE.' pc ON pc.`assortment_id`=pa.`id` '
            .'WHERE pa.`print_id`='.$printID.' AND pc.`id` IS NOT NULL AND pc.`active`=1';
    $result = mysql_query($query);
    if($result && mysql_num_rows($result)) {
        while(($row = mysql_fetch_assoc($result))) {
            //если файл совпадает, значит цвет задействован в этом файле
            if($row['file_id'] == $itemID) {                
                if(!isset($arColors['used'][$row['substrate_id']])) {
                    $arColors['used'][$row['substrate_id']] = array();                    
                }
                $arColors['used'][$row['substrate_id']][] = $row['color_id'];
            //все уже зайдествованные цвета в других логотипе
            } else {
                if(!isset($arColors['unavailable'][$row['substrate_id']])) {
                    $arColors['unavailable'][$row['substrate_id']] = array();
                }
                $arColors['unavailable'][$row['substrate_id']][] = $row['color_id'];
            }
        }
    }
    //инитим массив данных доступных цветов и массив айдишников не доступных цветов
    $arrPageData['arEnabledColors'] = $arrPageData['arDisabledColors'] = array();
    //получаем данные стороны по настройкам принта
    $arSide = PrintProduct::getSides($arrPageData['arPrint']['placement']);
    //получаем все цвета для которых загружены подложки
    $arSubstratesColors = getRowItemsInKeyArray('substrate_id', SUBSTRATES_IMAGES_TABLE.' pi LEFT JOIN '.COLORS_TABLE.' c ON c.`id`=pi.`color_id`', 'pi.*, c.`hex`', 'WHERE pi.`img_front`<>"" OR pi.`img_rear`<>""', 'ORDER BY c.`order`');
    foreach($arrPageData['arAssortment'] as $substrateID => $assort) {
        //недоступные цвета для этой подложки
        $arrPageData['arDisabledColors'][$substrateID] = isset($arColors['unavailable'][$substrateID]) ? $arColors['unavailable'][$substrateID] : array();
        //инитим будущий массив всех достпунхы цветов этой подложки
        $arrPageData['arEnabledColors'][$substrateID] = array();
        //выбираем из всех подложек, подложки для текущей подложки
        $arSubstrates = array_key_exists($substrateID, $arSubstratesColors) ? $arSubstratesColors[$substrateID] : array();
        //проходим по подложкам и распределяем их по сторонам
        foreach($arSubstrates as $substrate) {  
            //запоняем цветами, если для них загружены подложки
            if(!in_array($substrate['color_id'], $arrPageData['arDisabledColors'][$substrateID]) && $substrate[$arSide['column']] && file_exists(WLCMS_ABS_ROOT.$arrPageData['substrate_path'].$substrate[$arSide['column']])) {   
                //определяем реальные размеры подложки
                $sizes = getArrImageSize($arrPageData['substrate_path'], $substrate[$arSide['column']]);
                //создаем цвет и определяем его данные
                $arrPageData['arEnabledColors'][$substrateID][$substrate['color_id']] = array(
                    'id' => $substrate['color_id'],
                    'hex' => $substrate['hex'],  
                    'filename' => $substrate[$arSide['column']],
                    'width' => $sizes['w'], 
                    'height' => $sizes['h'],                        
                );
            }
        }         
    }
    //получаем итем
    if($itemID) {
        $item = getSimpleItemRow($itemID, PRINTFILES_TABLE);        
        $item['title'] = unScreenData($item['title']);
        $item['createdDate'] = date('Y-m-d', strtotime($item['created']));
        $item['createdTime'] = date('H:i:s', strtotime($item['created']));
        $item['arLogoSizes'] = getArrImageSize($arrPageData['file_path'], $item['filename']);
        $item['filename'] = $item['filename'] ? $arrPageData['file_url'].$item['filename'] : $arrPageData['file_url'].'noimage.png';
    } else {
        $item = array_combine_multi($DB->getTableColumnsNames(PRINTFILES_TABLE), '');  
        $item['order']  = getMaxPosition(0, 'order', 'id', PRINTFILES_TABLE);
        $item['active'] = 1;
        $item['createdDate'] = date('Y-m-d');
        $item['createdTime'] = date('H:i:s');
        $item['arLogoSizes'] = false;
    }      
    //зполняем ассортименты
    $item['arAssortment'] = array();    
    foreach($arrPageData['arAssortment'] as $substrateID => $assort) {
        //если есть доступные цвета, то формируем данные асортимента
        if(!empty($arrPageData['arEnabledColors'][$substrateID])) {
            //получаем массив айдишников уже сохраненных цветов
            $assort['colors'] = isset($arColors['used'][$substrateID]) ? $arColors['used'][$substrateID] : array();
            //если не установлен дефолтный цвет, то устанавливаем первый
            if($assort['color_id'] == 0) {
                $color = reset($arrPageData['arEnabledColors'][$substrateID]);
                $assort['color_id'] = $color['id'];  
                //и добавляем его к выбранным
                $assort['colors'][] = $color['id'];
            }
            //если нету настроек, то заполняем их из подложки
            if($assort['settings_id'] == 0) {
                //расшифровка параметров - массив параметров для всех сторон по айди стороны
                $dimensions = PrintProduct::dimensionsFromDB(unScreenData($assort['dimensions']));
                //получаем дефолтные настройки логотипа
                $dimensions = isset($dimensions[$arSide['id']]) ? $dimensions[$arSide['id']] : PrintProduct::getDimension();
                $assort['width'] = $item['arLogoSizes'] && $item['arLogoSizes']['w']<$dimensions['width'] ? $item['arLogoSizes']['w'] : $dimensions['width'];
                $assort['offset'] = $dimensions['offset'];
                $assort['height'] = 0;
                $assort['active'] = 0;
            }     
            $assort['arSettings'] = array(
                'id'     => $assort['settings_id'],
                'width'  => $assort['width'],
                'height' => $assort['height'],
                'offset' => $assort['offset'],
                'active' => $assort['active'],
            );
            $item['arAssortment'][$substrateID] = $assort;
        }        
    }
        
    if(!empty($_POST)) $item = array_merge ($item, $_POST);
}

$smarty->assign('item',  $item);
// /////////////////////// END LOCAL PAGE OPERATIONS \\\\\\\\\\\\\\\\\\\\\\\\\\\
# ##############################################################################