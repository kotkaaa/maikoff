<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/ 
define('WEBlife',    1); //Set flag that this is a parent file
define('WLCMS_EXEC', 1);//Set flag that this process by exec
define('WLCMS_ZONE', 'BACKEND'); //Set flag that this is a admin area

mb_internal_encoding('UTF-8');
mb_http_input('UTF-8'); 
mb_http_output('UTF-8'); 

ini_set('memory_limit', '999M');
set_time_limit(60*60);

// change to current work file dir  [fix for exec, that current work dir is user dir]
chdir(dirname(__FILE__));
// change to root WLCMS dir
chdir("..".DIRECTORY_SEPARATOR);

// Set DOCUMENT_ROOT in global $_SERVER var if empty
if(empty($_SERVER['DOCUMENT_ROOT'])) $_SERVER['DOCUMENT_ROOT'] = rtrim(getcwd(), '/\\');
// Set SERVER_NAME in global $_SERVER var if empty
if(empty($_SERVER['SERVER_NAME'])) $_SERVER['SERVER_NAME'] = (getenv('IS_DEV') ? 'maikoff.curr.loc' : 'maikoff.com.ua');
// Set $argv required var
isset($argv) or $argv = array();
    
require_once('include/functions/base.php');                 // 1. Include base functions

require_once('include/system/SystemComponent.php');         // 1. Include DB configuration file Must be included before other
require_once('include/system/DefaultLang.php');             // 2. Include Languages File
require_once('include/system/tables.php');                  // 3. Include DB tables File
require_once('include/classes/DbConnector.php');            // 4. Include DB class
require_once('include/classes/product/PrintProduct.php'); // 5. Include CatalogProduct class
require_once('include/helpers/PHPHelper.php');        // 6. Include PriceAggregator class
require_once('include/classes/PriceAggregator.php');        // 6. Include PriceAggregator class

$DB          = new ExternalDbConnector();
$logfile     = WLCMS_RUNTIME_DIR.DS.'cron_tasks.log';
$cnt         = 0;
$log_message = "";
$mctime      = microtime(true);

$AgregatorsManager = new AgregatorsManager($UrlWL);        
$AgregatorsManager->run(WLCMS_RUNTIME_DIR, UPLOAD_DIR.DS.'export');
 
$log_message = "\n\n".date("Y.m.d (l dS of F Y h:i:s A)")."\n".'Prices Updated Time: ' . (microtime(true) - $mctime) .' s' . PHP_EOL;
if (($fp = @fopen($logfile, 'a'))) {
    fwrite($fp, $log_message);
    fclose($fp);
}

if(isset($_GET['print'])) {
    echo nl2br($log_message);
}

if (isset($_GET['site'])) {
    header("Location: /admin.php?module=catalog");
} 