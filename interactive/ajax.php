<?php
 /*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
define('WEBlife', 1);  //Set flag that this is a parent file
mb_internal_encoding('UTF-8');
mb_http_input('UTF-8'); 
mb_http_output('UTF-8');
// link = /interactive/ajax.php?zone=[site|admin]&action=[|]
$site_zone =  (isset($_GET['zone']) && !empty($_GET['zone'])) ? addslashes($_GET['zone']) : false;
$action    =  (isset($_GET['action']) && !empty($_GET['action'])) ? addslashes($_GET['action']) : false;

if($site_zone){

    // Define WLCMS_ZONE from $site_zone var 
    switch($site_zone){
        case 'admin': define('WLCMS_ZONE', 'BACKEND');  break;//Set flag that this is a admin area
        case 'site' : define('WLCMS_ZONE', 'FRONTEND'); break;//Set flag that this is a site area
        default:  exit(); break;
    }

    // change to root dir
    chdir("..".DIRECTORY_SEPARATOR);


# ##############################################################################
// /// INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\\\\\
    require_once('include/functions/base.php');         // 1. Include base functions
    require_once('include/functions/image.php');        // 2. Include image functions

    require_once('include/classes/Cookie.php');         // 1. Include Cookie class file
    $Cookie     = new CCookie();
    require_once('include/system/SystemComponent.php'); // 2. Include DB configuration file Must be included before other
    require_once('include/system/DefaultLang.php');     // 3. Include Languages File
    require_once('include/system/tables.php');          // 4. Include DB tables File
    require_once('include/classes/DbConnector.php');    // 5. Include DB class
    require_once('include/helpers/PHPHelper.php');
    require_once('include/helpers/HTMLHelper.php');
    require_once('include/classes/wideimage/WideImage.php');
    require_once('include/classes/Validator.php');      // 8. Include Text Validator class
    require_once('include/classes/ActionsLog.php');     // 9. 
    require_once('include/classes/UserAccess.php');     // 10. Include User Access class 
    $DB         = new DbConnector();
    $PHPHelper  = new PHPHelper();  
    $HTMLHelper = new HTMLHelper();
    $Validator  = new Validator();
    $UserAccess = new UserAccess();
// /// END INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\
# ##############################################################################


################################################################################
// /////////////////// IMPORTANT GLOBAL VARIABLES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    $objUserInfo     = getUserFromSession($DB->getTableColumnsNames(USERS_TABLE));
    $objSettingsInfo = getSettings();
    //init user access
    if(isset($objUserInfo->id) && isset($objUserInfo->type))
        $UserAccess->init($objUserInfo->id, $objUserInfo->type);
    $arrModules      = getModules();
    
    $arrPageData     = array( //Page data array
        'itemID'        => 0,    // Item ID
        'backurl'       => '',
        'files_url'     => UPLOAD_URL_DIR,
        'files_path'    => UPLOAD_DIR,
        'def_img_param' => array('w'=>100, 'h'=>100),
        'images_params' => array(),
        'arrOrderLinks' => array(),
        'arrBreadCrumb' => array(),
        'items_on_page' => 10,
        'total_items'   => 0,
        'total_pages'   => 1,
        'seo_separator' => ' - ',
        'css_dir'       => '/css/'.TPL_FRONTEND_NAME.'/',
        'images_dir'    => '/images/site/'.TPL_FRONTEND_NAME.'/',
        'headTitle'     => '',
        'headCss'       => array(),
        'headScripts'   => array(),
        'messages'      => getSessionMessages(),
        'errors'        => getSessionErrors(),
        'success'       => false,
    );

    // Global json array for json outputs
    $json = array();  

// \\\\\\\\\\\\\\\\\ END IMPORTANT GLOBAL VARIABLES ////////////////////////////
################################################################################
//    saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_FILES'=>$_FILES, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), 'log_ajax.txt');
        
    if ($action) {
        
        //header('Content-type: text/html; charset=utf-8');
        
        switch($action){

            // ACTION WITH ajaxUserSessionTimeUpdate -----------------------------------------------------------
            case 'ajaxCacheFlush':
                if($objUserInfo->logined){
                    $cacheKey  = isset($_GET['cacheKey'])  ? trim($_GET['cacheKey'])  : null;
                    if ($cacheKey === null) {
                        PHPHelper::getMemCache()->flush();
                    } elseif ($cacheKey) {
                        $value = PHPHelper::getMemCache()->get($cacheKey);
                        if ($value) {
                            PHPHelper::getMemCache()->delete($cacheKey);
                        } else {
                            if ($value) PHPHelper::getMemCache()->delete($cacheKey);
                        }
                    }
                } echo '1';// Required to trigger onComplete function on Mac OSX
//                saveLogDebugFile(array('$_GET'=>$_GET, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                break;
            
            case "ajaxFileUpload":
                require_once('js/libs/blueimp-file-upload/server/php/UploadHandler.php');
                $files_url  = UPLOAD_URL_DIR."temp/";
                $files_path = prepareDirPath($files_url, true);
                $UploadHandler = new UploadHandler(array(
                    "upload_dir"    => $files_path,
                    "upload_url"    => $files_url,
                    "user_dirs"     => false,
                    "mkdir_mode"    => 0777,
                    "image_library" => 2,
                    'print_response' => false,
                    //'max_number_of_files' => OrderHelper::MAX_FILES_COUNT,
                    'accept_file_types' => OrderHelper::UPLOAD_ALLOW_EXTENSION,
                    'max_file_size' => OrderHelper::UPLOAD_MAX_FILESIZE,
                ));
                $response = $UploadHandler->get_response();
                echo json_encode($response);
                break;
            
            case "getPrintProduct":
                include 'include/classes/product/PrintProduct.php'; 
                
                $itemID = (!empty($_GET['itemID'])) ? intval($_GET['itemID']): 0;
                $substrateID = (!empty($_GET['substrateID'])) ? intval($_GET['substrateID']): 0;                
                $searchStr = !empty($_GET['searchStr']) ? PHPHelper::prepareSearchText($_GET['searchStr']) : '';
                
                $json['items'] = array();
                $where = $searchStr ? '(p.`title` LIKE "%'.$searchStr.'%" OR p.`pcode` LIKE "%'.$searchStr.'%") AND pa.`id` IS NOT NULL' : 'p.`id`='.$itemID;
                $query = 'SELECT p.`id`, p.`id` `print_id`, p.`title`, p.`category_id`, p.`placement`, 
                                 m.`title` ctitle, pa.`substrate_id` `default_substrate_id`, pa.`color_id`, pa.`id` `assortment_id`  
                          FROM `'.PRINTS_TABLE.'` p 
                          LEFT JOIN `'.PRINT_ASSORTMENT_TABLE.'` pa ON pa.`print_id` = p.`id` AND '.($substrateID ? 'pa.`substrate_id`='.$substrateID : 'pa.`isdefault`=1').'  
                          LEFT JOIN `'.MAIN_TABLE.'` m ON m.`id` = p.`category_id` 
                          WHERE '.$where.' GROUP BY p.`id` ORDER BY p.`order`';
                $result = mysql_query($query);
                if($result && mysql_num_rows($result)) {
                    while (($row = mysql_fetch_assoc($result))) {
                        $row['title'] = unScreenData($row['title']);
                        $row = PrintProduct::getSimpleItem($row);
                        $json['items'][] = $row;
                    }
                }    
                echo json_encode($json);
                break;
            
            case 'updateShortcuts': 
                    $json['items'] = array(); 
                    $stext  = (!empty($_GET['stext'])) ? PHPHelper::prepareSearchText($_GET['stext'], true): '';
                    $cid    = (!empty($_GET['cid']))   ? intval($_GET['cid']): 0;
                    $pid    = (!empty($_GET['pid']))   ? intval($_GET['pid']): 0;
                    $module = (!empty($_GET['object_module']))  ? trim($_GET['object_module']): '';
                    // products search
                    if ($module == 'catalog') {
                        $arIdx = array();
                        $squery = 'SELECT `pid` as `id` FROM '.SHORTCUTS_TABLE.' 
                                   WHERE `module`="'.$module.'" AND `cid`='.$cid.
                                   ($pid ? ' OR (`cid`='.$cid.' AND `pid`='.$pid.')' : '');
                        
                        $cquery = 'SELECT `id` FROM '.CATALOG_TABLE.' WHERE `cid`='.$cid;
                        $result = mysql_query('('.$squery.') UNION ALL ('.$cquery.')');
                        if(mysql_num_rows($result) > 0) {
                            while ($row = mysql_fetch_assoc($result)) {
                                $arIdx[] = $row['id'];
                            }
                        }
                        $json['enable'] = ($pid && !empty($arIdx) && in_array($pid, $arIdx)) ? false : true;
                        if ($stext) {
                            $select = 'SELECT c.*, m.`title` AS `ctitle` FROM `'.CATALOG_TABLE.'` c ';
                            $join   = 'LEFT JOIN `'.MAIN_TABLE.'` m ON(m.`id` = c.`cid`)  ';
                            $where  = 'WHERE (c.`title` LIKE "%'.$stext.'%" OR c.`pcode` LIKE "%'.$stext.'%")  ';
                            $where .= (!empty($arIdx) ? ' AND c.`id` NOT IN ('.implode(',', $arIdx).')' : '');
                            $order  = 'GROUP BY c.`id` ORDER BY c.`title`, m.`title`';
                            $query  = $select.$join.$where.$order;
                            $result = mysql_query($query);
                            if(mysql_num_rows($result) > 0) {
                                while ($row = mysql_fetch_assoc($result)) {
                                    $row['title'] = unScreenData($row['title']);
                                    $row['ctitle'] = unScreenData($row['ctitle']);
                                    $json['items'][] = $row;
                                }
                            }
                        }
                    } echo json_encode($json);
                break;

            case 'getImgSettings':
                $module = (!empty($_GET['module'])) ? trim($_GET['module'])   : '';
                $index  = (!empty($_GET['index']))  ? intval($_GET['index'])   : 0;
                $json   = array();
                if ($module) {
                    require_once('include/classes/SmartyEnvironment.php');
                    $smarty = new SmartyEnvironment(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CHECK_COMPILE, TPL_BACKEND_CACHING);
                    $smarty->assign('module', $module);
                    $smarty->assign('index', $index);
                    $smarty->assign('aliases', SystemComponent::getArImgAliases());
                    $smarty->assign('arImages', array());
                    $json['image'] = $smarty->fetch('common/module_images_settings.tpl');
                }
                echo json_encode($json);
                break;
                
            case 'getAccessSettings':
                $option  = (!empty($_GET['option']))  ? $_GET['option']  : '';
                $modules = (!empty($_GET['modules'])) ? $_GET['modules'] : '';
                $gid  = (!empty($_GET['gid'])) ? intval($_GET['gid'])    : 0;
                $uid  = (!empty($_GET['uid'])) ? intval($_GET['uid'])    : 0;
                $json = array();
                switch ($option) {
                    case 'reset':
                        deleteRecords(USERS_ACCESS_TABLE, 'WHERE `uid`='.$uid.' AND `gid`='.$gid);
                        $arModulesParams = getRowItems(MODULES_PARAMS_TABLE, '*', '`access`=1');
                        uasort($arModulesParams, 'mySort');
                        $settings = getItemRow(USERS_ACCESS_TABLE, 'modules', 'WHERE `gid`='.$gid);
                        require_once('include/classes/SmartyEnvironment.php');
                        $smarty = new SmartyEnvironment(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CHECK_COMPILE, TPL_BACKEND_CACHING);
                        $smarty->assign('arModules', $arModulesParams);
                        $smarty->assign('gid', $gid);
                        $smarty->assign('uid', $uid);
                        $smarty->assign('availableModules', !empty($settings) ? explode(',', $settings['modules']) : array());
                        $json['messages'] = 'Значения восстановлены до значений группы';
                        $json['settings'] = $smarty->fetch('ajax/access_settings.tpl'); 
                        break;
                    case 'update':
                        if(!empty($gid)){
                            $arModulesParams = getRowItems(MODULES_PARAMS_TABLE, '*', '`access`=1');
                            uasort($arModulesParams, 'mySort');
                            $settings = getItemRow(USERS_ACCESS_TABLE, '*', 'WHERE `gid`='.$gid.' AND (`uid`='.$uid.' OR `uid`=0) ORDER BY `uid` DESC');
                            require_once('include/classes/SmartyEnvironment.php');
                            $smarty = new SmartyEnvironment(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CHECK_COMPILE, TPL_BACKEND_CACHING);
                            $smarty->assign('arModules', $arModulesParams);
                            $smarty->assign('gid', $gid);
                            $smarty->assign('uid', $uid);
                            $smarty->assign('availableModules', !empty($settings) ? explode(',', $settings['modules']) : array());
                            $json['settings'] = $smarty->fetch('ajax/access_settings.tpl'); 
                        } else $json['errors'] = 'Ошибка сохранения';
                        break;
                    case 'save':
                        if(!empty($gid)){
                            deleteRecords(USERS_ACCESS_TABLE, 'WHERE `uid`='.$uid.' AND `gid`='.$gid);
                            $result = $DB->postToDB(array('uid'=>$uid, 'gid'=>$gid, 'modules'=>$modules), USERS_ACCESS_TABLE);
                            if($result) $json['messages'] = 'Данные успешно сохранены';
                            else $json['errors'] = 'Ошибка сохранения';
                        } else $json['errors'] = 'Ошибка сохранения';
                        break;
                }
                echo json_encode($json);
                break;
            
            case 'filterActionsLog':
                $filters  = !empty($_GET['filters']) ? $_GET['filters']   : array('time'=>1);
                $key      = !empty($_GET['key'])     ? trim($_GET['key']) : '';
                $showMore = !empty($_GET['type']);
                $json = array();
                $arActionsLog['arHistory'] = ActionsLog::getAuthInstance($objUserInfo->id, getRealIp())->getHistory($filters);
                $arActionsLog['arFilters'] = ActionsLog::getAuthInstance($objUserInfo->id, getRealIp())->getFilters($filters, $key);
                require_once('include/classes/SmartyEnvironment.php');
                $smarty = new SmartyEnvironment(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CHECK_COMPILE, TPL_BACKEND_CACHING);
                $smarty->assign('arHistoryData', $arActionsLog['arHistory']);
                $smarty->assign('arFilters', $arActionsLog['arFilters']);
                $smarty->assign('selectedFilters', $filters);
                $json['history'] = $showMore ? $smarty->fetch('ajax/object_actions_log_body.tpl') : $smarty->fetch('ajax/actions_log.tpl');  
                $json['filters'] = $smarty->fetch('ajax/actions_log_filters.tpl');
                $json['url'] = $arActionsLog['arHistory']['filtersUrl'];
                $json['page'] = $arActionsLog['arHistory']['page']+1 <= $arActionsLog['arHistory']['total_pages'] ? $arActionsLog['arHistory']['page']+1 : '';
                echo json_encode($json);
                break;
                        
            case 'checkUniqueEmail':
                $email = (!empty($_GET['email']))? trim($_GET['email']): '';
                if(!empty($email)) {
                    $select = 'SELECT u.id FROM `'.USERS_TABLE.'` u ';
                    $where  = 'WHERE u.`email`="'.$email.'" OR u.`login`="'.$email.'"';
                    $query  = $select.$where;
                    $result = mysql_query($query);
                    if(mysql_num_rows($result) > 0) {
                        $json['result'] = 'error';
                    } else {
                        $json['result'] = 'success';
                    }
                    echo json_encode($json);
                }
                break;

            // ACTION WITH getAttributeValue ---------------------------------------------
            case 'getAttributeValue':
                $json = array();
                $aid = !empty($_GET['aid']) ? intval($_GET['aid']) : 0;
                $vidx = !empty($_GET['vidx']) ? explode(",", PHPHelper::prepareSearchText($_GET['vidx'])) : array();
                $searchStr = !empty($_GET['searchStr'])? PHPHelper::prepareSearchText($_GET['searchStr'], true): '';
                if (mb_strlen($searchStr) > 0) {
                    $json['items'] = array();
                    $arWhere = array();
                    $arLike = explode(" ", $searchStr);
                    foreach ($arLike as $like) {
                        $arWhere[] = "LOWER(`title`) LIKE '%{$like}%'";
                    }
                    $where  = "WHERE `aid`={$aid} AND ".implode(" OR ", $arWhere);
                    $query  = "SELECT DISTINCT `title` AS `id`, `title` AS `text` FROM `".ATTRIBUTES_VALUES_TABLE."` av $where ORDER BY `title`";
                    $result = mysql_query($query);
                    if (mysql_num_rows($result) > 0) {
                        while ($row = mysql_fetch_object($result)) {
                            $json['items'][] = $row;
                        }
                    }
                }
                echo json_encode($json);
                break;

            // ACTION WITH liveSearch ---------------------------------------------
            case 'liveSearch':
                $json = array();
                $searchStr = !empty($_GET['searchStr']) ? PHPHelper::prepareSearchText($_GET['searchStr']) : '';
                switch ($site_zone) {
                    // admin zone search
                    case 'admin':
                        $module = !empty($_GET['module']) ? trim($_GET['module']) : die('Select module param');
                        switch ($module) {
                            // catalog search
                            case 'catalog':
                                $json['items'] = array();
                                $modelID = isset($_GET['modelID']) ? intval($_GET['modelID']) : 0;
                                $query = 'SELECT DISTINCT `title` FROM `'.CATALOG_TABLE.'` 
                                          WHERE '.($modelID ? '`model_id`='.$modelID.' AND ' : '').
                                                '(`title` LIKE "%'.$searchStr.'%" OR `pcode` LIKE "%'.$searchStr.'%") 
                                          GROUP BY `id` ORDER BY `order`';
                                $result = mysql_query($query);
                                if($result && mysql_num_rows($result)) {
                                    while (($row = mysql_fetch_assoc($result))) {
                                        $row['title'] = unScreenData($row['title']);
                                        $json['items'][] = $row;
                                    }
                                }
                                break;
                            // print search
                            case 'prints':
                                $json['items'] = array();
                                    $query = 'SELECT p.`title`, p.`id`, m.`title` ctitle FROM `'.PRINTS_TABLE.'` p LEFT JOIN `'.MAIN_TABLE.'` m ON m.`id`=p.`category_id` 
                                              WHERE (p.`title` LIKE "%'.$searchStr.'%" OR p.`pcode` LIKE "%'.$searchStr.'%") 
                                              GROUP BY p.`id` ORDER BY p.`order`';
                                    $result = mysql_query($query);
                                    if($result && mysql_num_rows($result)) {
                                        while (($row = mysql_fetch_assoc($result))) {
                                            $row['title'] = unScreenData($row['title']);
                                            $row['ctitle'] = unScreenData($row['ctitle']);
                                            $json['items'][] = $row;
                                        }
                                    }
                                break;
                            // model search
                            case 'models':
                                $json['items'] = array();
                                $query = 'SELECT DISTINCT `title` FROM `'.MODELS_TABLE.'` 
                                          WHERE (`title` LIKE "%'.$searchStr.'%" OR `pcode` LIKE "%'.$searchStr.'%") 
                                          GROUP BY `id` ORDER BY `order`';
                                $result = mysql_query($query);
                                if($result && mysql_num_rows($result)) {
                                    while (($row = mysql_fetch_assoc($result))) {
                                        $row['title'] = unScreenData($row['title']);                                        
                                        $json['items'][] = $row;
                                    }
                                }
                                break;
                            // model search
                            case 'customers':
                                $json['items'] = array();
                                $query = 'SELECT `id`, `phone`, IFNULL(`email`, "") `email`, IFNULL(`city`, "") `city`, 
                                                 IFNULL(`address`, "") `address`, IFNULL(`descr`, "") `descr`, 
                                                 CONCAT_WS(" ", `firstname`, `surname`, `surname`, `phone`) `title`, 
                                                 CONCAT_WS(" ", `firstname`, `surname`, `surname`) `name`
                                          FROM `'.USERS_TABLE.'` 
                                          WHERE `type`="'.USER_TYPE_USER.'" 
                                            AND (`firstname` LIKE "%'.$searchStr.'%" OR `surname` LIKE "%'.$searchStr.'%" OR `phone` LIKE "%'.$searchStr.'%" OR `email` LIKE "%'.$searchStr.'%") 
                                          GROUP BY `id` ORDER BY `id` DESC';
                                $result = mysql_query($query);
                                if($result && mysql_num_rows($result)) {
                                    while (($row = mysql_fetch_assoc($result))) {
                                        $row['title'] = unScreenData($row['title']);                                        
                                        $json['items'][] = $row;
                                    }
                                }
                                break;
                            // model orders
                            case 'orders':
                                $json['items'] = array();
                                $query = 'SELECT CONCAT_WS(" ", `name`, `phone`) `title`, `phone` `value` FROM `'.ORDERS_TABLE.'` 
                                          WHERE (`id` LIKE "%'.$searchStr.'%" OR `name` LIKE "%'.$searchStr.'%" OR `phone` LIKE "%'.$searchStr.'%" OR `email` LIKE "%'.$searchStr.'%") 
                                          ORDER BY `id` DESC';
                                $result = mysql_query($query);
                                if($result && mysql_num_rows($result)) {
                                    while (($row = mysql_fetch_assoc($result))) {                                   
                                        $json['items'][] = $row;
                                    }
                                }
                                break;
                        }
                        break;
                    // site zone search
                    case 'site':
                        $json = array();
                        $items = array();
                        $searchtext  = !empty($_GET['stext'])  ? PHPHelper::prepareSearchText($_GET['stext'], true): '';
                        $searchwhere = !empty($_GET['swhere']) ? PHPHelper::prepareSearchText($_GET['swhere'], true) : false;
                        require_once('include/classes/SmartyEnvironment.php');
                        $smarty     = new SmartyEnvironment(TPL_FRONTEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_FRONTEND_FORSE_COMPILE, TPL_FRONTEND_CHECK_COMPILE, TPL_FRONTEND_CACHING);
                        switch ($searchwhere) {
                            // catalog search
                            case 'catalog':
                                $files_url  = UPLOAD_URL_DIR.'catalog/';
                                $files_path = prepareDirPath($files_url);
                                $arrFields = array('pcode', 'title', 'descr', 'fulldescr', 'meta_descr', 'meta_key', 'seo_title');
                                $arrText = explode(' ', $searchtext);
                                $serachByValue =  $serachByCTitle = $serachByTitle = '';
                                foreach($arrText as $text) {
                                    $serachByValue .= ($serachByValue ? ' AND ' : ''). " (LOWER(a.`value`) like '%".$text."%' OR LOWER(a.`value`) like '".$text."%') ";
                                    $serachByCTitle .= ($serachByCTitle ? ' AND ' : ''). " (LOWER(ct.`title`) like '%".$text."%' OR LOWER(ct.`title`) like '".$text."%') ";
                                    $serachByTitle .= ($serachByTitle ? ' AND ' : ''). " (LOWER(bt.`title`) like '%".$text."%' OR LOWER(bt.`title`) like '".$text."%')  ";
                                }
                                $query = "SELECT t.*, cf.`filename` AS `image`, IF (t.`isdiscount`=0 OR t.`discount`=0, 0, 1) AS `dis`, (SELECT COUNT(*) FROM `comments` WHERE `module`='catalog' AND `pid`=t.`id`) AS `com` FROM ((SELECT ct.* FROM " . CATALOG_TABLE . " ct LEFT JOIN ". MAIN_TABLE ." mt ON(ct.`cid` = mt.`id`) ".
                                        " WHERE ( LOWER(".getSqlStrCondition(getSqlListFilter($arrFields, $searchtext, "LIKE", 'ct.'), 'OR').") OR (".$serachByCTitle.") ) AND ct.`active` = 1)".
                                        " UNION (SELECT ca.* FROM " . CATALOG_TABLE . " ca LEFT JOIN ".MODEL_ATTRIBUTES_TABLE." a ON ca.`model_id`=a.`mid` ".
                                        " WHERE (".$serachByValue.") AND ca.`active` = 1)".
                                        " UNION (SELECT ca.* FROM " . CATALOG_TABLE . " ca LEFT JOIN ".BRANDS_TABLE." bt ON bt.`id`=ca.`bid` ".
                                        " WHERE (".$serachByTitle." ) AND bt.`active` = 1)".
                                        " ) t LEFT JOIN ". MAIN_TABLE ." m ON(t.`cid` = m.`id`) LEFT JOIN `".CATALOGFILES_TABLE."` cf ON(cf.`pid`=t.`id`) AND cf.`isdefault`=1 WHERE t.`active`=1 AND m.`active`=1 ORDER BY (`dis` + `com` + t.`viewed`), t.`price` DESC, t.`order`";
                                $result = mysql_query($query);     
                                if ($result and mysql_num_rows($result)){
                                    while ($row = mysql_fetch_assoc($result)) {
                                        $row['arCategory'] = $UrlWL->getCategoryById($row['cid']);
                                        $row['title'] = unScreenData($row['title']);
                                        $row['small_image'] = (!empty($row['image']) && file_exists($files_path.$row['id'].DS.$row['image']))? $files_url.$row['id'].'/small_'.$row['image']: $files_url.'small_noimage.jpg';
                                        $row['new_price'] = ($row['isdiscount'] && $row['discount']) ? $row['price'] - ($row['price']*$row['discount']/100) : 0;
                                        $items[] = $row;
                                    }
                                }
                                $smarty->assign('items',        $items);
                                $smarty->assign('UrlWL',        $UrlWL);
                                $smarty->assign('arrModules',   $arrModules);
                                $smarty->assign('searchtext',  $searchtext);
                                $json['output'] = $smarty->fetch('ajax/live_search.tpl');
                                break;
                            // all site search
                            case 'site':
                                $json['items'] = array();
                                $arrFields = array('title', 'text', 'descr', 'meta_descr', 'meta_key', 'seo_title');
                                $query = "SELECT `title` FROM " . MAIN_TABLE . " WHERE LOWER(".getSqlStrCondition(getSqlListFilter($arrFields, $searchtext, 'LIKE'), 'OR').") AND `pid`>0 AND `active` = 1 ORDER BY `order` ";
                                $result = mysql_query($query);
                                if($result && mysql_num_rows($result)){
                                    while ($row = mysql_fetch_assoc($result)) {
                                        $row['title'] = unScreenData($row['title']);
                                        $json['items'][] = $row;
                                    }
                                }

                                $arrSearchModules = array(
                                    array('module'=>'news',    'table'=>NEWS_TABLE,    'title'=>NEWS,      'arFields'=>array('title', 'descr', 'fulldescr', 'meta_descr', 'meta_key', 'seo_title')), 
                                    array('module'=>'gallery', 'table'=>GALLERY_TABLE, 'title'=>GALLERIES, 'arFields'=>array('title', 'descr', 'meta_descr', 'meta_key', 'seo_title')),
                                    array('module'=>'video',   'table'=>VIDEOS_TABLE,  'title'=>VIDEOS,    'arFields'=>array('title', 'descr', 'fulldescr', 'meta_descr', 'meta_key', 'seo_title'))
                                );

                                $select = 'title';
                                $order = " `created` DESC, `cid`, `order`";

                                foreach($arrSearchModules as $module){
                                    $where = " WHERE (".getSqlStrCondition(getSqlListFilter($module['arFields'], $searchtext, 'LIKE'), 'OR').") AND `active` = 1";
                                    $result = getComplexRowItems($module['table'], $select, $where, $order);
                                    if(!empty($result)){
                                        foreach($result as $row) {
                                            $row['title'] = unScreenData($row['title']);
                                            $json['items'][] = $row;
                                        }
                                    }
                                }
                                break;
                        }
                        break;
                } echo json_encode($json);
                
                break;
                
            // ACTION WITH Basket ---------------------------------------------
            case 'basket':
                $json = array();
                require_once('include/classes/Basket.php');         
                $Basket     = new Basket();
                $Basket->setupKitParams(PRODUCT_KIT_PREFIX);
                require_once('include/classes/SmartyEnvironment.php');
                $smarty     = new SmartyEnvironment(TPL_FRONTEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_FRONTEND_FORSE_COMPILE, TPL_FRONTEND_CHECK_COMPILE, TPL_FRONTEND_CACHING);
                $smarty->assign('Basket',       $Basket);
                $smarty->assign('UrlWL',        $UrlWL);
                $smarty->assign('HTMLHelper',   $HTMLHelper);
                $smarty->assign('arrModules',   $arrModules);
                $smarty->assign('arrPageData',  $arrPageData);
                
                $option  = !empty($_GET['option'])? trim($_GET['option']): die('Choose the basket option!');
                $itemID  = (!empty($_GET['itemID']))? trim($_GET['itemID']): false;
                $qty     = (!empty($_GET['qty']) && intval($_GET['qty']))? intval($_GET['qty']): 1;
                $recalc  = (isset($_GET['recalc'])) ? $_GET['recalc'] : 0;
                $list    = (isset($_GET['list'])) ? (bool)$_GET['list'] : false;
                $initial = (isset($_GET['initial'])) ? (bool)$_GET['initial'] : false;
                $smarty->assign('list',  $list);
                // Basket operations
                switch ($option){
                    // Add To Basket
                    case 'add':
                        if($itemID) {
                            $Basket->add($itemID, $qty, $recalc);
                        }
                        break;
                    // Remove From Basket
                    case 'remove':
                        if($itemID) {
                            $Basket->remove($itemID, $qty);
                        }
                        $json['output'] = array(
                            'isEmpty'  => $Basket->isEmptyBasket()
                        );
                        break;
                    // Update Basket
                    case 'update':
                        $json["items"]  = $Basket->get();
                        $json["files"]  = $Basket->getFiles();
                        $json["price"]  = $Basket->getTotalPrice();
                        $json["shipping_price"] = $Basket->getShippingPrice();
                        $json["total_price"] = $Basket->getTotalPrice(1);
                        $json["amount"] = $Basket->getTotalAmount();
                        $json["empty"]  = $Basket->isEmptyBasket();
                        $json["shippingID"] = $Basket->getShippingID();
                        if (!$initial) {
                            $json['output'] = array(
                                'layout'    => $smarty->fetch('ajax/basket.tpl'),
                                'checkout'  => $smarty->fetch('ajax/basket-inline.tpl')
                            );
                        }
                        break;
                    // Clear Basket
                    case 'clear':
                        $Basket->dropBasket();
                        break;
                    // Upload files
                    case "ajaxFileUpload":
                        require_once('js/libs/blueimp-file-upload/server/php/UploadHandler.php');
                        $json["files"] = array();
                        $files_url  = $Basket->getFilesUrl();
                        $files_path = $Basket->getFilesPath();
                        $UploadHandler = new UploadHandler(array(
                            "upload_dir"    => $files_path,
                            "upload_url"    => $files_url,
                            "user_dirs"     => false,
                            "mkdir_mode"    => 0777,
                            "image_library" => 2,
                            'print_response' => false,
                            //'max_number_of_files' => OrderHelper::MAX_FILES_COUNT,
                            'accept_file_types' => OrderHelper::UPLOAD_ALLOW_EXTENSION,
                            'max_file_size' => OrderHelper::UPLOAD_MAX_FILESIZE,
                        ));
                        $response = $UploadHandler->get_response();
                        if ($response and !empty($response["files"])) {
                            foreach ($response["files"] as $file) {
                                $Basket->addFile($file->name);
                            }
                        }
                        break;
                    // Attach files
                    case "attachFiles":
                        if (!empty($_GET["files"])) {
                            foreach ($_GET["files"] as $file) {
                                $Basket->addFile($file["name"]);
                            }
                        }
                        break;
                    // Delete uploaded file
                    case "deleteFile":
                        $Basket->deleteFile($itemID);
                        break;
                    // Set shipping type
                    case "setShipping":
                        $Basket->setShippingID($itemID);
                        break;
                    // Set payment type
                    case "setPayment":
                        $Basket->setPaymentID($itemID);
                        break;
                } die (json_encode($json));
                break;
            // ACTION WITH getKitItems ---------------------------------------------
            case 'getKitItems':
                $json = array();
                $cid = (!empty($_GET['cid']) && intval($_GET['cid']))? intval($_GET['cid']): 0;
                $exItems = !empty($_GET['exclude'])? $_GET['exclude']: array(0);
                if($cid) {
                    $json['items'] = array();
                    $select = 'SELECT c.* FROM `'.CATALOG_TABLE.'` c ';
                    $where = 'WHERE c.`cprice`>0 AND c.`cid`='.$cid.' AND c.`id` NOT IN('.implode(',', $exItems).') ';
                    $order = 'ORDER BY c.`order`';
                    $query = $select.$where.$order;
                    $result = mysql_query($query);
                    if(mysql_num_rows($result) > 0) {
                        while ($row = mysql_fetch_assoc($result)) {
                            $json['items'][$row['id']] = $row['title'];
                        }
                    }
                }
                echo json_encode($json);
                break;
            // ACTION WITH getRelatedItems ---------------------------------------------
            case 'getRelatedItems':
                $json = array();
                $module = !empty($_GET['module']) ? trim($_GET['module']) : die('Select module param');
                $exItems = !empty($_GET['exclude']) ? $_GET['exclude'] : array();
                $searchStr = !empty($_GET['searchStr']) ? trim($_GET['searchStr']) : '';
                $cid = (!empty($_GET['cid']) and intval($_GET['cid'])) ? intval($_GET['cid']) : false;
                switch ($module) {
                    case 'catalog':
                        $json['items'] = array();
                        $query  = 'SELECT c.*, m.`title` AS `ctitle` FROM `'.CATALOG_TABLE.'` c ';
                        $query .= 'LEFT JOIN `'.MAIN_TABLE.'` m ON(m.`id` = c.`cid`) ';
                        $query .= 'WHERE c.`title` LIKE "%'.$searchStr.'%" '.((!empty($exItems))? 'AND c.`id` NOT IN('.implode(',', $exItems).') ': ' ').(($cid)? 'AND c.`cid`='.$cid.' ': ' ');
                        $query .= 'GROUP BY c.`id` ORDER BY c.`order`';
                        $result = mysql_query($query);
                        if (mysql_num_rows($result) > 0) {
                            while ($row = mysql_fetch_assoc($result)) {
                                $json['items'][] = $row;
                            }
                        }
                        break;
                    case "stocks":
                        $json['items'] = array();
                        $query  = 'SELECT c.*, m.`title` AS `ctitle` FROM `'.CATALOG_TABLE.'` c ';
                        $query .= 'LEFT JOIN `'.MAIN_TABLE.'` m ON(m.`id` = c.`cid`) ';
                        $query .= 'WHERE c.`title` LIKE "%'.$searchStr.'%" '.((!empty($exItems))? 'AND c.`id` NOT IN('.implode(',', $exItems).') ': ' ').(($cid)? 'AND c.`cid`='.$cid.' ': ' ');
                        $query .= 'GROUP BY c.`id` ORDER BY c.`order`';
                        $result = mysql_query($query);
                        if(mysql_num_rows($result) > 0) {
                            while ($row = mysql_fetch_assoc($result)) {
                                $json['items'][] = $row;
                            }
                        }
                        break;
                }
                echo json_encode($json);
                break;
            // ACTION WITH sortable lists ---------------------------------------------
            case 'updateSortableList':
                $json = array();
                $item = array();
                $type = !empty($_GET['listType'])? trim($_GET['listType']): FALSE;
                
                if($type) {
                    
                    $item['type'] = $type;
                    
                    switch ($type) {
                        case 'attributes':
                            $gid = !empty($_GET['gid'])? intval($_GET['gid']): 0;
                            if($gid && $gid>0) {
                                $item['attributes'] = array();
                                $query = 'SELECT DISTINCT a.*, ag.`title` AS `gtitle` FROM `'.ATTRIBUTES_TABLE.'` a LEFT JOIN `'.ATTRIBUTE_GROUPS_TABLE.'` ag ON(ag.`id` = a.`gid`) ';
                                $where = 'WHERE a.`gid`='.$gid.' ';
                                $order = 'ORDER BY a.`order`';
                                $result = mysql_query($query.$where.$order);
                                if(mysql_num_rows($result) > 0) {
                                    while ($row = mysql_fetch_assoc($result)) {
                                        $item['attributes'][] = $row;
                                    }
                                }
                            }
                            break;
                            
                        case 'filters':
                            $fid = !empty($_GET['fid'])? intval($_GET['fid']): 0;
                            $filterType = !empty($_GET['filterType'])? trim($_GET['filterType']): FALSE;
                            if(($fid && $fid>0) && $filterType) {
                                $item['filters'] = array();
                                $query = 'SELECT f.*, CONCAT("{filter_", f.`id`, "}") AS `alias` FROM `'.FILTERS_TABLE.'` f ';
                                $where = 'WHERE f.`id`='.$fid;
                                $result = mysql_query($query.$where);
                                if(mysql_num_rows($result) > 0) {
                                    while ($row = mysql_fetch_assoc($result)) {
                                        $row['type'] = $filterType;
                                        $item['filters'][] = $row;
                                    }
                                }
                            }
                            break;
                    }
                    
                    require_once('include/classes/SmartyEnvironment.php');
                    $smarty = new SmartyEnvironment(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CHECK_COMPILE, TPL_BACKEND_CACHING);
                    $smarty->assign('item', $item);
                    $json['output'] = $smarty->fetch('ajax/sortable_list.tpl');
                    
                    echo json_encode($json);
                }
                break;
            // ACTION WITH addAttributeRow ---------------------------------------------
            case 'addAttributeRow':
                $json = array();
                $itemID = !empty($_GET['itemID'])? intval($_GET['itemID']): 0;
                $groupID = !empty($_GET['groupID'])? intval($_GET['groupID']): 0;
                $arGroups = !empty($_GET['arGroups'])? $_GET['arGroups']: array();
                if($groupID > 0) {
                    $attrGroup = getItemRow(ATTRIBUTE_GROUPS_TABLE, '*', 'WHERE `active`=1 AND `id`='.$groupID);
                    if(!empty($attrGroup)) {
                        $attrGroup['attributes'] = getComplexRowItems(ATTRIBUTES_TABLE, '*', 'WHERE `gid`='.$groupID, '`order`');
                        foreach ($attrGroup['attributes']  as $k => $v){
                            $attrGroup['attributes'][$k]['values'] = getComplexRowItems(ATTRIBUTES_VALUES_TABLE.' av', 'av.*', 'WHERE av.`aid`='.$v['id'], 'av.`order`');
                        }
                        require_once('include/classes/SmartyEnvironment.php');
                        $smarty = new SmartyEnvironment(TPL_BACKEND_NAME, WLCMS_DEBUG, WLCMS_SMARTY_ERROR_REPORTING, TPL_BACKEND_FORSE_COMPILE, TPL_BACKEND_CHECK_COMPILE, TPL_BACKEND_CACHING);
                        $smarty->assign('attrGroup', $attrGroup);
                        $json['tpl'] = $smarty->fetch('ajax/attributes_form.tpl');
                    }
                    $json['select'] = array();
                    $where = 'WHERE ag.`active`=1 AND ag.`id`!='.$groupID;
                    if (!empty($arGroups)) {
                        $where .= ' AND ag.`id` NOT IN('.implode(',', $arGroups).')';
                    }
                    $arAcceptedGroups = getComplexRowItems(ATTRIBUTE_GROUPS_TABLE.' ag', 'ag.*', $where, 'ag.`order`');
                    if (!empty($arAcceptedGroups)) {
                        foreach ($arAcceptedGroups as $group) {
                            $json['select'][$group['id']] = $group['title'].($group['descr'] ? '( '.$group['descr'].')' : '');
                        }
                    }
                }
                echo json_encode($json);
                break;
            // ACTION WITH generateSeoPath ---------------------------------------------
            case 'generateSeoPath':
                $path   = !empty($_GET['path'])   ? trim(urldecode($_GET['path']))   : '';
                $prefix = !empty($_GET['prefix']) ? trim(urldecode($_GET['prefix'])) : '';                
                if ($path) {
                    $path = preg_replace('/[^\s\w\d]/u', '', $path);
                    $path = preg_replace('/\s+/u', ' ', $path);
                    $path = trim($UrlWL->strToUniqueUrl($DB, $path, $prefix));
                }
//                saveLogDebugFile(array('$path'=>$path, '$_GET'=>$_GET, '$_POST'=>$_POST, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                echo json_encode( array( 'seo_path'=>$path ) );
                break;
            // ACTION WITH generatePassword --------------------------------------------------------------------
            case 'generatePassword': // Password
                $length  = !empty($_GET['length']) ? intval($_GET['length']): 12;
                echo json_encode( array( "code"=>randString($length)) );
//                saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                break;
            // ACTION WITH incrementBannerClick --------------------------------------------------------------------
            case 'incrementBannerClick': // Currency
                $categoryID = !empty($_GET['categoryID']) ? intval($_GET['categoryID'])               : 0;
                $bannerID   = !empty($_GET['bannerID'])   ? intval($_GET['bannerID'])                 : 0;
                $bannerURL  = !empty($_GET['bannerURL'])  ? urldecode(addslashes($_GET['bannerURL'])) : '/';
                require_once('include/classes/Banners.php');
                Banners::incrementClick($bannerID);
//                saveLogDebugFile(array('$_GET'=>$_GET, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                Redirect($bannerURL);
                break;
            // ACTION WITH ajaxChangeCurrency --------------------------------------------------------------------
            case 'ajaxChangeCurrency': // Currency
                $cid = !empty($_GET['cid']) ? intval($_GET['cid']) : 0;
                require_once('include/classes/Currencies.php');        // 10.Include Currencies class
                $Currencies = new Currencies();  //Initialize Currencies class
//                saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                echo json_encode( array( "result"=>(int)$Currencies->setCurrent($cid) ) );
                break;
            // ACTION WITH ajaxCapchaCheck --------------------------------------------------------------------
            case 'ajaxCapchaCheck': // Captcha
                $captchaSID  = !empty($_GET['sid'])  ? trim(addslashes($_GET['sid']))  : '';
                $captchaCode = !empty($_GET['code']) ? trim(addslashes($_GET['code'])) : '';
                require_once('include/classes/Captcha.php');        // 8. Include Captcha class
                $Captcha = new Captcha(getIValidatorPefix(), CAPTCHA_TABLE, false);  //Initialize Captcha class
//                saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                echo json_encode( array("result"=>(int)$Captcha->CheckCode($captchaCode, $captchaSID, true, false)) );
                break;
            // ACTION WITH ajaxCapchaUpdate --------------------------------------------------------------------
            case 'ajaxCapchaUpdate': // Captcha
                require_once('include/classes/Captcha.php');        // 8. Include Captcha class
                $Captcha    = new Captcha(getIValidatorPefix(), CAPTCHA_TABLE, false);  //Initialize Captcha class
                $Captcha->SetCode();
//                saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                echo json_encode(array("sid"=>$Captcha->GetSID(),"code"=>$Captcha->GetGeneratedCode(),"length"=>$Captcha->GetCodeLength()));
                break;
            // ACTION WITH ajaxUserSessionTimeUpdate -----------------------------------------------------------
            case 'ajaxUserSessionTimeUpdate':
                if($objUserInfo->logined) $_SESSION[(WLCMS_ZONE=='BACKEND' ? 'a' : 's').'user_timeout'] = time();
                echo '1';// Required to trigger onComplete function on Mac OSX
//                saveLogDebugFile(array('$_GET'=>$_GET, '$_SESSION'=>$_SESSION, '$real__FILE__'=>realpath(__FILE__)), "log_{$action}.txt");
                break;
            // ACTION WITH ajaxFilesUpload ---------------------------------------------
            case 'ajaxUserLogOut':
                $success = 0;
                if($objUserInfo->logined)
                    $success = unsetUserFromSession();
                echo json_encode($success);
                break;
            // ACTION WITH ajaxUserFilesUpload ---------------------------------
            case 'ajaxUserFilesUpload':
                $userID        = (isset($_GET['UID']) && intval($_GET['UID'])) ? intval($_GET['UID']) : 0;
                $filePrefix    = !empty($_GET['file_prefix']) ? addslashes($_GET['file_prefix']) : "u{$userID}_";
                $targetFolder  = !empty($_POST['folder']) ? prepareDirPath(UPLOAD_DIR.DS.$_POST['folder']) : '';
                //saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_FILES'=>$_FILES, '$_COOKIE'=>$_COOKIE, '$real__FILE__'=>realpath(__FILE__)), 'log_ajaxUserFilesUpload.txt');
                if(isset($_GET['uploadifyData']) && isset($_POST['Upload']) && !empty($_FILES) && $userID){
                    $files_params  = !empty($_GET['files_params']) ? unserialize(base64_decode(urldecode($_GET['files_params']))) : array();
                    $ext           = getFileExt($_FILES['Filedata']['name']);
                    $targetFName   = $filePrefix.setFilePathFormat($_FILES['Filedata']['name'], true);
                    if($targetFolder && in_array($ext, explode(';', str_replace('*.', '', $_POST['fileext'])))){
                        $fileExists = file_exists($targetFolder.$targetFName);
                        $moved      = move_uploaded_file($_FILES['Filedata']['tmp_name'], $targetFolder.$targetFName);
                        while($files_params && (list(, list($partiname, $piw, $pih)) = each($files_params))){
                            createThumb($targetFolder.$targetFName, $piw, $pih, $targetFolder.$partiname.$targetFName);
                        }
                        if($moved && !$fileExists) $DB->postToDB(array('uid'=>$userID, 'filename'=>$targetFName), USERFILES_TABLE);
                    }  echo '1';// Required to trigger onComplete function on Mac OSX

                } else if(isset($_GET['uploadifyCheck'])){
                    $fileArray = array();
                    if($targetFolder){
                        foreach ($_POST as $key => $value) {
                            if ($key == 'folder') continue;
                            $value = strtolower(setFilePathFormat($value));
                            if (file_exists($targetFolder.$filePrefix.$value)) $fileArray[$key] = $value;
                        }
                    } echo json_encode($fileArray);
                } else echo '0';
                break;

            // ACTION WITH ajaxCatalogFilesUpload ---------------------------------
            case 'ajaxCatalogFilesUpload':
                $itemID        = (isset($_GET['PID']) && intval($_GET['PID'])) ? intval($_GET['PID']) : 0;
                $filePrefix    = !empty($_GET['file_prefix']) ? addslashes($_GET['file_prefix']) : "p{$itemID}_";
                $targetFolder  = !empty($_POST['folder']) ? prepareDirPath(UPLOAD_DIR.DS.$_POST['folder']).DS.$itemID.DS : '';
                //saveLogDebugFile(array('$_GET'=>$_GET, '$_POST'=>$_POST, '$_FILES'=>$_FILES, '$_COOKIE'=>$_COOKIE, '$real__FILE__'=>realpath(__FILE__)), 'log_ajaxUserFilesUpload.txt');
                if(isset($_GET['uploadifyData']) && isset($_POST['Upload']) && !empty($_FILES) && $itemID){
                    $files_params  = !empty($_GET['files_params']) ? unserialize(base64_decode(urldecode($_GET['files_params']))) : array();
                    $ext           = getFileExt($_FILES['Filedata']['name']);
                    $targetFName   = $filePrefix.setFilePathFormat($_FILES['Filedata']['name'], true);
                    if($targetFolder && in_array($ext, explode(';', str_replace('*.', '', $_POST['fileext'])))){
                        $fileExists = file_exists($targetFolder.$targetFName);
                        $moved      = move_uploaded_file($_FILES['Filedata']['tmp_name'], $targetFolder.$targetFName);
                        while($files_params && (list(, list($partiname, $piw, $pih)) = each($files_params))){
                            createThumb($targetFolder.$targetFName, $piw, $pih, $targetFolder.$partiname.$targetFName);
                        }
                        if($moved && !$fileExists) {
                            $DB->postToDB(array('pid'=>$itemID, 'filename'=>$targetFName), CATALOGFILES_TABLE, '', array(), 'insert', true);
                        }
                        $default = getItemRow(CATALOGFILES_TABLE, 'id', 'WHERE `isdefault`=1 AND `pid`='.$itemID);
                        if(empty($default)) updateRecords(CATALOGFILES_TABLE, '`isdefault`=1', 'WHERE `pid`='.$itemID. ' ORDER BY `fileorder` LIMIT 1');
                    }  echo '1';// Required to trigger onComplete function on Mac OSX
                } else if(isset($_GET['uploadifyCheck'])){
                    $fileArray = array();
                    if($targetFolder){
                        foreach ($_POST as $key => $value) {
                            if ($key == 'folder') continue;
                            $value = strtolower(setFilePathFormat($value));
                            if (file_exists($targetFolder.$filePrefix.$value)) $fileArray[$key] = $value;
                        }
                    } echo json_encode($fileArray);
                } else echo '0';
                break;
            // ACTION WITH fileDownload FROM private AREA ----------------------
            case 'dbFileBackUpDownload':
                // Cookie dbFileBackUpDownload names
                define('DCOOKIE',      'sxd');
                //variables from GET Array
                $uid = (isset($_GET['uid']) && intval($_GET['uid']) > 0) ? intval($_GET['uid']) : false;
                $file = (isset($_GET['file']) && strlen($_GET['file'])>0) ? trim(urldecode($_GET['file'])) : false;
                if($uid==$objUserInfo->id)
                   $uid=true;
                elseif($Cookie->getCookie(DCOOKIE)!=''){
                    $cUser = explode(":", base64_decode($dCookie->getCookie(DCOOKIE)));
                    $dbUser = $DB->getDBSettings();
                    if ($DB->getDBUser()==$cUser[1] && $DB->getDBPassword()==$cUser[2]) {
                        $uid=true;
                    }
                }
                if( $uid==true && $file && strpos($file, "\0") === FALSE/*Nullbyte hack fix*/){
                    // Make sure program execution doesn't time out
                    // Set maximum script execution time in seconds (0 means no limit)
                    @set_time_limit(0);
                    // Make sure that header not sent by error
                    // Sets which PHP errors are reported
                    @error_reporting(0);
                    // Allow direct file download (hotlinking)?  Empty - allow hotlinking
                    // If set to nonempty value (Example: example.com) will only allow downloads when referrer contains this text
                    $allowed_referrer = $_SERVER['SERVER_NAME'];
                    // Allowed extensions list in format 'extension' => 'mime type'
                    // If myme type is set to empty string then script will try to detect mime type
                    // itself, which would only work if you have Mimetype or Fileinfo extensions
                    // installed on server.
                    $allowed_ext = array(
                        'sql'   => 'text/x-sql', 
                        'bz'    => 'application/x-bzip', 
                        'bz2'   => 'application/x-bzip2', 
                        'boz'   => 'application/x-bzip2', 
                        'gz'    => 'application/x-gzip', 
                        'tgz'   => 'application/x-gzip', 
                        'tar'   => 'application/x-tar', 
                        'tgz'   => 'application/x-tar', 
                        'zip'   => 'application/zip'
                    );
                    // Download base folder, i.e. folder where you keep all user dirs with files for download.
                    $baseFolder = prepareDirPath('backup/');
                    // log file name
                    $log_file = $baseFolder.'downloads.log';
                    // If hotlinking not allowed then make hackers think there are some server problems
                    if ( !empty($allowed_referrer) &&
                         (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']), strtoupper($allowed_referrer)) === false)
                    ) die("Internal server error. Please contact system administrator.");
                    // Get real file name.
                    // Remove any path info to avoid hacking by adding relative path, etc.
                    $fname = basename($file);
                    // file extension
                    $fext = getFileExt($fname);
                    // check if allowed extension
                    if (!array_key_exists($fext, $allowed_ext)) {
                      die("Not allowed file type.");
                    }
                    $file = $baseFolder.$fname;
                    // if don't exist and isn't file and  can't read them - die
                    if (!file_exists($file) && !is_file($file) && !is_readable($file)) {
                      header ("HTTP/1.0 404 Not Found");
                      exit();
                    }
                    // if Time file last modified mor then SESSION_INACTIVE - die
                    if ((time() - filectime($file)) > SESSION_INACTIVE){
                      $Cookie->del(DCOOKIE);
                      $Cookie->process();
                      header ("HTTP/1.0 404 Not Found");
                      die("Not allowed to download this file more.");
                    }
                    // file size in bytes
                    $fsize = filesize($file);
                    // get mime type
                    if (empty($allowed_ext[$fext])) {
                        $mtype = '';
                        // mime type is not set, get from server settings
                        if (function_exists('mime_content_type')) {
                            $mtype = mime_content_type($file);
                        } else if (function_exists('finfo_file')) {
                            $finfo = finfo_open(FILEINFO_MIME); // return mime type
                            $mtype = finfo_file($finfo, $file);
                            finfo_close($finfo);
                        }
                        if ($mtype == '') {
                            $mtype = "application/force-download";
                        }
                    } else  $mtype = $allowed_ext[$fext]; // get mime type defined by admin
                    // Browser will try to save file with this filename, regardless original filename.
                    // You can override it if needed.
                    // remove some bad chars
                    $asfname = str_replace(array('"',"'",'\\','/'), '', $fname);
                    if ($asfname === '') $asfname = 'NoName'.'.'.$fext;
                    // set headers
                    header("Pragma: public");
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: public");
                    header("Content-Description: File Transfer");
                    header("Content-Type: $mtype");
                    header("Content-Disposition: attachment; filename=\"$asfname\"");
                    header("Content-Transfer-Encoding: binary");
                    header("Content-Length: " . $fsize);
                    // download
                    // @readfile($file);
                    $file = @fopen($file, "rb");
                    if($file) {
                        while(!feof($file)) {
                            print(fread($file, 1024 * 8));
                            flush();
                            if( connection_status()!=0 ) {
                                @fclose($file);
                                die();
                            }
                        } @fclose($file);
                    }
                    // log downloads
                    if (!empty($log_file)){
                        $f = @fopen($log_file, 'a+');
                        if ($f) {
                          @fputs($f, date("m.d.Y g:ia")."  ".$_SERVER['REMOTE_ADDR']."  ".$folder."  ".$fname."\n");
                          @fclose($f);
                          @chmod($log_file, 0775);
                        }
                    }
                } else { die(); }
                break;

            default:
                exit();
                break;
        }
    }
}

function mySort($a, $b) {  
    if($a['order'] != $b['order']) 
        return ($a['order'] < $b['order']) ? -1 : 1;  
    return 0;
}