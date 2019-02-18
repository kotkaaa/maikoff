<?php

/* 
 * WebLife CMS
 * Created on 31.10.2018, 17:34:39
 * Developed by http://weblife.ua/
 */
define('WEBlife',    1); //Set flag that this is a parent file
define('WLCMS_EXEC', 1);//Set flag that this process by exec
define('WLCMS_ZONE', 'BACKEND'); //Set flag that this is a admin area

mb_internal_encoding('UTF-8');
mb_http_input('UTF-8');
mb_http_output('UTF-8');

// change to current work file dir  [fix for exec, that current work dir is user dir]
chdir(dirname(__FILE__));
// change to root WLCMS dir
chdir("..".DIRECTORY_SEPARATOR);

// Set DOCUMENT_ROOT in global $_SERVER var if empty
if (empty($_SERVER['DOCUMENT_ROOT'])) $_SERVER['DOCUMENT_ROOT'] = rtrim(getcwd(), '/\\');
// Set SERVER_NAME in global $_SERVER var if empty
if (empty($_SERVER['SERVER_NAME'])) $_SERVER['SERVER_NAME'] = (getenv('IS_DEV') ? 'maikoffnew.loc' : 'maikoff.com.ua');

# ##############################################################################
// /// INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\\\\\
require_once('include/functions/base.php');                    // 1. Include base functions
require_once('include/functions/menu.php');                    // 1. Include base functions
require_once('include/classes/Cookie.php');                    // 2. Include Cookie class file
$Cookie     = new CCookie();
require_once('include/system/SystemComponent.php');            // 3. Include DB configuration file Must be included before other
require_once('include/system/DefaultLang.php');                // 4. Include Languages File
require_once('include/system/tables.php');                     // 5. Include DB tables File
require_once('include/helpers/PHPHelper.php');                     // 5. Include DB tables File
require_once('include/classes/DbConnector.php');               // 6. Include DB class
require_once('include/classes/Filters.php');
require_once('include/classes/product/CatalogProduct.php');
require_once('include/classes/sitemap/Sitemap.php');           // 7. Include Sitemap class
$DB           = new DbConnector();
$mctime       = microtime(true);
$params       = getopt('t:l:p:d:', array('task:', 'logging:', 'print:', 'debug:'));
$logfile      = WLCMS_RUNTIME_DIR.'/sitemap.log';
$message      = date("Y.m.d (l dS of F Y h:i:s A)") . PHP_EOL;
$baseUrl      = WLCMS_HTTP_HOST.$_SERVER['SERVER_NAME']."/";
$maps         = [];

// init from command line args and set default if absent and possible
$task    = isset($params['t']) ? (string)$params['t'] : (isset($params['task'])    ? (string)$params['task']  : "index");
$logging = isset($params['l']) ? (bool)$params['l']   : (isset($params['logging']) ? (bool)$params['logging'] : false);
$print   = isset($params['p']) ? (bool)$params['p']   : (isset($params['print'])   ? (bool)$params['print']   : false);
$debug   = isset($params['d']) ? (bool)$params['d']   : (isset($params['debug'])   ? (bool)$params['debug']   : false);

// write categories sitemap
$filename = "sitemap_categories.xml";
$Sitemap  = \Sitemap\SitemapFactory::getInstance();
$Sitemap->xml()->setFilename($filename);
$Sitemap->xml()->setBase(rtrim($baseUrl, "/"));
$Sitemap->export(new \Sitemap\CategoriesDataProvider($DB, $UrlWL));
$maps[] = $baseUrl.$filename;

// write catalog sitemap
$filename = "sitemap_catalog.xml";
$Sitemap->xml()->setFilename($filename);
$Sitemap->export(new \Sitemap\CatalogDataProvider($DB, $UrlWL));
$maps[] = $baseUrl.$filename;

// write prints sitemap
$filename = "sitemap_prints.xml";
$Sitemap->xml()->setFilename($filename);
$Sitemap->export(new \Sitemap\PrintsDataProvider($DB, $UrlWL));
$maps[] = $baseUrl.$filename;

// write catalog filters sitemap
$filename = "sitemap_filters_catalog.xml";
$Sitemap->xml()->setFilename($filename);
$Sitemap->export(new \Sitemap\FiltersDataProvider($DB, $UrlWL));
$maps[] = $baseUrl.$filename;

// write prints filters sitemap
$filename = "sitemap_filters_prints.xml";
$Sitemap->xml()->setFilename($filename);
$Sitemap->export(new \Sitemap\PrintsFiltersDataProvider($DB, $UrlWL));
$maps[] = $baseUrl.$filename;

// write sitemap index
$Sitemap->xml()->setFilename("sitemap.xml");
$Sitemap->index($maps);

$message .= 'Sitemap generated! ' . PHP_EOL
            .'Execution time: ' . (microtime(true) - $mctime) .' s ' . PHP_EOL
            .'Memory peak usage: ' . memory_get_peak_usage(true) . ' bytes. ' . str_repeat(PHP_EOL, 2);

// Write log file
if ($logging) {
    try {
        $fp = @fopen($logfile, 'a+');
        fwrite($fp, $message);
        fclose($fp);
    } catch (Exception $ex) {
        print $ex->getMessage();
    }
}
// Print message to console output
if ($print) {
    print $message;
}