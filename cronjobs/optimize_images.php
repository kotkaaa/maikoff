<?php

/* 
 * @descr Запускает команду на оптимизацию файлов находящихся в заданых директориях включая поддиректории за прошлый день начиная с 2017-07-20 10:00:00
 * @cmd $ php /var/www/maikoff/maikoff.com.ua/cronjobs/optimize_images.php --print=0 --limit=0 --test=0 --debug=0
 * @cmd $ php D:\OpenServer\domains\maikoff\trunk\cronjobs\optimize_images.php --print=0 --limit=0 --test=0 --debug=0
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
if(empty($_SERVER['DOCUMENT_ROOT'])) $_SERVER['DOCUMENT_ROOT'] = rtrim(getcwd(), '/\\');

# ##############################################################################
// /// INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\\\\\
require_once('include/functions/base.php');         // 1. Include base functions
require_once('include/classes/ImageOptimizer.php'); // 2. Include image optimizer class
$logfile = $_SERVER['DOCUMENT_ROOT'].'/temp/runtime/optimization.log';
$images_hashsum = $_SERVER['DOCUMENT_ROOT'].'/temp/runtime/images_hashsum.php';
$log_message = "Оптимизация изображений".PHP_EOL;
$mctime = microtime(true); 
$argv = array_merge(isset($argv[1]) ? getopt('', array('test::', 'print::', 'limit:', 'command:', 'debug')) : array(), array('argv'=>$argv));
$test = !empty($argv['test']);
$print = !empty($argv['print']);
$limit = (isset($argv['limit']) ? intval($argv['limit']) : ($test ? 5 : 0));
$command = (isset($argv['command']) ? trim($argv['command']) : '');
$debug = (isset($argv['debug']) ? (bool)$argv['debug'] : true);
/*
$argv = array_merge(isset($argv[1]) ? getopt_compatible('t::p::l:c:d', $argv, $usegetopt) : array(), array('argv'=>$argv)); // изза старой версии пришлось использовать только короткие нотации параметров
$test = !empty($argv['t']);
$print = !empty($argv['p']);
$limit = (isset($argv['l']) ? intval($argv['l']) : ($test ? 5 : 0));
$command = (isset($argv['c']) ? trim($argv['c']) : '');
$debug = (isset($argv['d']) ? (bool)$argv['d'] : false);
 */

// check hash array
$arCurrentHash = file_exists($images_hashsum) ? include_once $images_hashsum : array();
$startdate = strtotime('2018-08-16 10:00:00'); $checkdate = date('Y-m-d', strtotime("-1 day")); $exts = array('jpg','png','gif'); $idx = 0; $num = 0;
try {
    $optimizer = new ImageOptimizer(getenv('IS_DEV') ? array(
        ImageOptimizer::OPTIMIZER_OPTIPNG => 'D:\OpenServer\modules\optipng\optipng.exe',
        ImageOptimizer::OPTIMIZER_JPEGOPTIM => 'D:\OpenServer\modules\jpegoptim\jpegoptim.exe',
        ImageOptimizer::OPTIMIZER_GIFSICLE => 'D:\OpenServer\modules\gifsicle\gifsicle.exe',
    ) : array());
    $dirs = array(
        $_SERVER['DOCUMENT_ROOT'].'/images/smart',
        $_SERVER['DOCUMENT_ROOT'].'/images/site/smart',
        
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/attributes', 
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/banners',
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/brands',         
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/catalog',
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/gallery',
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/homeslider',
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/main',
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/media', 
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/news',
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/options',
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/print_types',
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/prints_spool',        
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/selections',
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/size_grids',
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/stocks',  
        $_SERVER['DOCUMENT_ROOT'].'/uploaded/users', 
    );
    $log_message .= 'на дату: '.$checkdate.'; в папках: '.implode(', ', $dirs).PHP_EOL;
    while(NULL !== ($dir = array_pop($dirs))) {
        if($dh = opendir($dir)) {
            $log_message .= "  ".$dir.PHP_EOL;
            while(false !== ($file = readdir($dh))) {
                if($file == '.' || $file == '..') continue;
                $path = $dir . '/' . $file;
                if(is_dir($path)) {
                    $dirs[] = $path;
                } else {
                    $idx++;
                    $ext = getFileExt($file);
                    if(in_array($ext, $exts)) {
                        $filtetime = filemtime($path);
                        if(date('Y-m-d', $filtetime) == $checkdate && $filtetime > $startdate) {
                            $key = md5($path);
                            $hash = md5_file($path);
                            $size = filesize($path);  
                            if(!isset($arCurrentHash[$key]) || ($arCurrentHash[$key]['hash'] != $hash || $arCurrentHash[$key]['size'] != $size)) {
                                $mess = "    ".$path.' -> ';
                                try {
                                    $mess .= trim($optimizer->optimize($path, null, $test));
                                    $arCurrentHash[$key] = array('hash'=>md5_file($path),'size'=>filesize($path));
                                } catch (Exception $e) {
                                    $mess .= trim($e->getMessage());
                                }   
                                $log_message .= $mess.PHP_EOL;
                                if($limit > 0 && $limit == $num) { break 2; }
                                $num++;
                            }
                        }
                    }
                }
            }
            closedir($dh);
        }
    }
} catch (Exception $e) {
    $log_message .= 'ERROR: '.$e->getMessage().PHP_EOL;
}
$log_message .= str_repeat('-', 50).PHP_EOL.'Работа завершена за ' . (microtime(true) - $mctime) .' s. '.PHP_EOL.'Всего обработано файлов '.$num.' из '.$idx.PHP_EOL;

//очистка темповой папки
$dir = $_SERVER['DOCUMENT_ROOT']."/uploaded/temp/";
if($dh = opendir($dir)) {
    $log_message .= PHP_EOL."  Очистка темповых изображений: ".PHP_EOL;
    while(false !== ($file = readdir($dh))) {
        $path = $dir.$file;
        if($file == '.' || $file == '..' || is_dir($path)) continue;        
        $filtetime = filemtime($path);
        if(date('Y-m-d', $filtetime) <= $checkdate) {
            @unlink($path);
            @unlink($dir.'thumbnail/'.$file);
            $log_message .= ' - удален темповый файл '.$path.PHP_EOL;
        } 
    }
    $log_message .= ' завершена '.PHP_EOL;
    closedir($dh);
}

// save hash array
if (($fp = @fopen($images_hashsum, 'w'))) {
    fwrite($fp, '<?php'.PHP_EOL.'return '.var_export($arCurrentHash, true).';'.PHP_EOL.'?>');
    fclose($fp);
}

//Write log file
if (($fp = @fopen($logfile, 'a'))) {
    fwrite($fp, $log_message);
    fclose($fp);
}

if($print || isset($_GET['print'])) {
    echo $log_message;
}