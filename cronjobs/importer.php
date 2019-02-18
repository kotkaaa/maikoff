<?php
/*
 * @description Import data from old site
 * local
 * @cmd cd domains\maikoff\design2017
 * @cmd php cronjobs\importer.php --print=1 --debug=1 --task=colors
 * @cmd php cronjobs\importer.php --print=1 --debug=1 --offset=0 --limit=10 --task=colors
 * host
 * @cmd php /var/www/maikoffO/maikoff.ua/cronjobs/importer.php --debug=1 --print=1 --task=colors
 * @cmd php /var/www/maikoffO/maikoff.ua/cronjobs/importer.php --debug=1 --print=1 --offset=0 --limit=10 --task=colors
 * @cmd cd maikoff.ua && php cronjobs/importer.php --print=1 --debug=1 --task=colors
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
require_once('include/classes/Cookie.php');                    // 2. Include Cookie class file
$Cookie     = new CCookie();
require_once('include/system/SystemComponent.php');            // 3. Include DB configuration file Must be included before other
require_once('include/system/DefaultLang.php');                // 4. Include Languages File
require_once('include/system/tables.php');                     // 5. Include DB tables File
require_once('include/classes/DbConnector.php');               // 6. Include DB class
require_once('include/classes/wideimage/WideImage.php');       // 7. Include WideImage class
require_once('include/classes/Importer.php');                  // 8. Include Importer class
$DB           = new DbConnector();
$mctime       = microtime(true);
$params       = getopt('t:p:d:o:l:', array('task:', 'print:', 'debug:', 'offset:', 'limit:'));
$logfile      = WLCMS_RUNTIME_DIR.'/importer.log';
$logtext      = date("Y.m.d (l dS of F Y h:i:s A)") . PHP_EOL;

// init from task line args and set default if absent and possible
$task   = isset($params['t']) ? $params['t'] : (isset($params['task']) ? $params['task'] : null);
$print  = isset($params['p']) ? (bool)$params['p'] : (isset($params['print']) ? (bool)$params['print'] : false);
$debug  = isset($params['d']) ? (bool)$params['d'] : (isset($params['debug']) ? (bool)$params['debug'] : false);
$offset = isset($params['o']) ? (bool)$params['o'] : (isset($params['offset']) ? intval($params['offset']) : 0);
$limit  = isset($params['l']) ? (bool)$params['l'] : (isset($params['limit']) ? intval($params['limit']) : 0);
// set db settings
$db_settings = $DB::getDBSettings();
$db_source = getenv("IS_DEV") ? array(
    "dbname"     => "maikoff2p",
    "dbhost"     => "en116db.mirohost.net",
    "dbusername" => "u_maikoffO",
    "dbpassword" => "16vCdnTd2f82",
) : array(
    "dbname"     => "maikoff2p",
    "dbhost"     => "en116db.mirohost.net",
    "dbusername" => "u_maikoffS",
    "dbpassword" => "1F10KNdo9zUX",
);
// определяем класс импорта
// $offset, $limit - не поддерживается в текущей редакции. заложено на перед
$Importer = new Importer($DB, $UrlWL, $debug, $print);

// run
if($print) print $logtext;
try {
    // Set Reader && Writer && run task
    $Importer
        ->setReader(new DataReader('https://maikoff.com.ua', $db_source["dbname"], $db_source["dbhost"], $db_source["dbusername"], $db_source["dbpassword"]))
        ->setWriter(new DataWriter('http://maikoff.new.loc', $db_settings["dbname"], $db_settings["dbhost"], $db_settings["dbusername"], $db_settings["dbpassword"], WLCMS_DB_ENCODING))
        ->run($task, $offset, $limit);
    $logtext.= $Importer->getLogs(PHP_EOL);
} catch (Exception $exc) {
    $message = $exc->getMessage() . PHP_EOL;
    $logtext.= $Importer->getLogs(PHP_EOL) . $message;
    if($print) print $message;
}

// finally
$message = 'Execution time: ' . (microtime(true) - $mctime) .' s' . PHP_EOL;
$logtext .= $message;
if($print) print $message;

// Write message to log file
saveStrToFile(($debug ? $logtext : str_replace(PHP_EOL, "\t", $logtext)).PHP_EOL, $logfile);

exit();