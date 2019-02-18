<?php
/*
 * @description Full and Stack reindexing should be separated by time
 *              For example Full at 2:05 and Stack every 10 min for every hour
 * local
 * @cmd cd domains\maikoff\design2017
 * @cmd php cronjobs\indexer.php --debug=1 --command=init
 * @cmd php cronjobs\indexer.php --print=1 --logging=1 --command=full
 * host
 * @cmd php /var/www/maikoffO/maikoff.ua/cronjobs/indexer.php --debug=1 --command=init
 * @cmd php /var/www/maikoffO/maikoff.ua/cronjobs/indexer.php --print=1 --logging=1 --command=full
 * @cmd cd maikoff.ua && php cronjobs/indexer.php --print=1 --logging=1 --command=init && php cronjobs/indexer.php --print=1 --logging=1 --command=full
 */
define('WEBlife',    1); //Set flag that this is a parent file
define('WLCMS_EXEC', 1);//Set flag that this process by exec
define('WLCMS_ZONE', 'BACKEND'); //Set flag that this is a admin area

//putenv('IS_DEV=1');

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
require_once('include/classes/Indexer.php');                   // 7. Include Indexer class
$DB           = new DbConnector();
$mctime       = microtime(true);
$params       = getopt('c:l:p:d:', array('command:', 'logging:', 'print:', 'debug:'));
$logfile      = WLCMS_RUNTIME_DIR.'/indexer.log';
$message      = date("Y.m.d (l dS of F Y h:i:s A)") . PHP_EOL;

// init from command line args and set default if absent and possible
$command = isset($params['c']) ? $params['c'] : (isset($params['command']) ? $params['command'] : null);
$logging   = isset($params['l']) ? (bool)$params['l'] : (isset($params['logging']) ? (bool)$params['logging'] : true);
$print   = isset($params['p']) ? (bool)$params['p'] : (isset($params['print']) ? (bool)$params['print'] : false);
$debug   = isset($params['d']) ? (bool)$params['d'] : (isset($params['debug']) ? (bool)$params['debug'] : false);

switch ($command) {
    case 'init':
        Indexer::init();
        $message .= 'Initialization of the necessary structures has been completed successfully!' . PHP_EOL;
        break;
    
    case 'full':
        Indexer::$settings = getSettings(); //settings info object
        Indexer::update();
        $message .= 'Full reindexation has been completed successfully! Inserted ' . Indexer::count() . ' rows' . PHP_EOL;
        break;
    
    case 'stack':
        Indexer::$settings = getSettings(); //settings info object
        $affected = Indexer::update(IndexItem::getInstance());
        $message .= 'Update from stack table has been completed successfully! Processed '.$affected.' tasks.' . PHP_EOL;
        break;
    
    case 'drop':
        Indexer::drop();
        $message .= 'Drop of the necessary structures has been completed successfully!' . PHP_EOL;
        break;
    
    default:
        print '!!!    ERROR    !!!'.PHP_EOL;
        $message .= 'Error: Command not recognized. Check command line args!' . PHP_EOL;
        break;
}

$message .= 'Execution time: ' . (microtime(true) - $mctime) .' s' . PHP_EOL;

// Write message to log file
if ($logging || $debug) {
    saveStrToFile($message.PHP_EOL, $logfile);
}
// print message
if($print || $debug) {
    print $message;
}

exit();