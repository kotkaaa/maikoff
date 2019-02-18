<?php

/* 
 * WebLife CMS
 * Created on 24.09.2018, 9:50:24
 * Developed by http://weblife.ua/
 *
 * php cronjobs\np.php --print=1 --debud=1 --logging=1 --task=import
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
if (empty($_SERVER['SERVER_NAME'])) $_SERVER['SERVER_NAME'] = (getenv('IS_DEV') ? 'maikoff.crm.loc' : 'maikoff.com.ua');
# ##############################################################################
// /// INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\\\\\
require_once('include/functions/base.php');                    // 1. Include base functions
require_once('include/classes/Cookie.php');                    // 2. Include Cookie class file
$Cookie     = new CCookie();
require_once('include/system/SystemComponent.php');            // 3. Include DB configuration file Must be included before other
require_once('include/system/DefaultLang.php');                // 4. Include Languages File
require_once('include/system/tables.php');                     // 5. Include DB tables File
require_once('include/classes/DbConnector.php');               // 6. Include DB class
require_once('include/classes/NovaPoshta.php');                // 7. Include DB class
$DB           = new DbConnector();
$mctime       = microtime(true);
$params       = getopt('t:p:d:l:', array('task:', 'print:', 'debug:', 'logging:'));
$logfile      = WLCMS_RUNTIME_DIR.'/np.log';
$logtext      = date("Y.m.d (l dS of F Y h:i:s A)") . PHP_EOL;
$message      = $logtext;
// init from task line args and set default if absent and possible
$task   = isset($params['t'])   ? $params['t']       : (isset($params['task'])    ? $params['task'] : null);
$print  = isset($params['p'])   ? (bool)$params['p'] : (isset($params['print'])   ? (bool)$params['print'] : false);
$debug  = isset($params['d'])   ? (bool)$params['d'] : (isset($params['debug'])   ? (bool)$params['debug'] : false);
$logging  = isset($params['l']) ? (bool)$params['l'] : (isset($params['logging']) ? (bool)$params['logging'] : false);

// run
if ($print) print $logtext;

use \NovaPoshta\Api as Api;

$cities     = Api::getCities(null, true);
$warehouses = Api::getWarehouses(null, null, true);

// Update cities
if (!empty($cities)) {
    $query  = 'INSERT INTO `'.NP_CITY_TABLE.'` (`CityID`, `Description`, `DescriptionRu`, `Ref`, `SettlementType`, `SettlementTypeDescription`, `SettlementTypeDescriptionRu`) VALUES'.PHP_EOL;
    foreach ($cities as $i=>$city) {
        $query .= '("'.$city->CityID.'", "'.mysql_real_escape_string($city->Description).'", "'.mysql_real_escape_string($city->DescriptionRu).'", "'.mysql_real_escape_string($city->Ref).'", "'.mysql_real_escape_string($city->SettlementType).'", "'.mysql_real_escape_string($city->SettlementTypeDescription).'", "'.mysql_real_escape_string($city->SettlementTypeDescriptionRu).'")';
        unset($cities[$i]);
        $query .= (!empty($cities) ? "," : "").PHP_EOL;
    }
    $query .= 'ON DUPLICATE KEY UPDATE'.PHP_EOL
            . '`Description` = VALUES(`Description`), '
            . '`DescriptionRu` = VALUES(`DescriptionRu`), '
            . '`Ref` = VALUES(`Ref`), '
            . '`SettlementType` = VALUES(`SettlementType`), '
            . '`SettlementTypeDescription` = VALUES(`SettlementTypeDescription`), '
            . '`SettlementTypeDescriptionRu` = VALUES(`SettlementTypeDescriptionRu`);';
    //saveStrToFile($query, WLCMS_RUNTIME_DIR."/qry.log");
    $result = mysql_query($query);
    $message .= "Table ".NP_CITY_TABLE.": ".mysql_affected_rows()." rows updated" . PHP_EOL;
}
// Update warehouses
if (!empty($warehouses)) {
    $query  = "INSERT INTO `".NP_WAREHOUSE_TABLE."` (`SiteKey`, `Ref`, `Description`, `DescriptionRu`, `Number`, `CityRef`, `CityDescription`, `CityDescriptionRu`) VALUES".PHP_EOL;
    foreach ($warehouses as $i=>$warehouse) {
        $query .= '("'.$warehouse->SiteKey.'", "'.mysql_real_escape_string($warehouse->Ref).'", "'.mysql_real_escape_string($warehouse->Description).'", "'.mysql_real_escape_string($warehouse->DescriptionRu).'", "'.$warehouse->Number.'", "'.mysql_real_escape_string($warehouse->CityRef).'", "'.mysql_real_escape_string($warehouse->CityDescription).'", "'.mysql_real_escape_string($warehouse->CityDescriptionRu).'")';
        unset($warehouses[$i]);
        $query .= (!empty($warehouses) ? ',' : '').PHP_EOL;
    }
    $query .= 'ON DUPLICATE KEY UPDATE'.PHP_EOL
            . '`Ref` = VALUES(`Ref`), '
            . '`Description` = VALUES(`Description`), '
            . '`DescriptionRu` = VALUES(`DescriptionRu`), '
            . '`Number` = VALUES(`Number`), '
            . '`CityRef` = VALUES(`CityRef`), '
            . '`CityDescription` = VALUES(`CityDescription`), '
            . '`CityDescriptionRu` = VALUES(`CityDescriptionRu`);';
    $result = mysql_query($query);
    $message .= "Table ".NP_WAREHOUSE_TABLE.": ".mysql_affected_rows()." rows updated" . PHP_EOL;
}

// finally
$message .= 'Execution time: ' . (microtime(true) - $mctime) .' s' . PHP_EOL;
$logtext .= $message;
if($print) print $message;

// Write message to log file
if ($logging) saveStrToFile(($debug ? $logtext : str_replace(PHP_EOL, "\t", $logtext)).PHP_EOL, $logfile);

exit();