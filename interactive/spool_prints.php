<?php

/* 
 * WebLife CMS
 * Created on 07.06.2018, 16:56:21
 * Developed by http://weblife.ua/
 */
define('WEBlife', 1); //Set flag that this is a parent file
define('WLCMS_ZONE', 'FRONTEND'); //Set flag that this is a site area

// change to root dir
chdir("..".DIRECTORY_SEPARATOR);


# ##############################################################################
// /// INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\\\\\
require_once('include/functions/base.php');         // 1. Include base functions
require_once('include/classes/Cookie.php');         // 1. Include Cookie class file
require_once('include/system/SystemComponent.php'); // 2. Include DB configuration file Must be included before other
require_once('include/classes/wideimage/WideImage.php');
require_once('include/classes/product/PrintProduct.php');

$show404 = false; 
$mctime  = microtime(true); 
$logfile = WLCMS_RUNTIME_DIR . DS . 'spool.log';
$logtext = date("Y.m.d (l dS of F Y h:i:s A)") . " Spool print image generate".PHP_EOL;
// /// END INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\
# ##############################################################################


try {
    // preparation
    if(!isset($_GET['spoolled'])) { 
        throw new Exception('Absent require get param');
    }
    $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if(!$url_path || !($params = PrintProduct::parseSpoolFileName(basename($url_path)))){
        throw new Exception('ParseSpoolFileName returned false result');
    }
    
    // get substrate file
    $substrateFile = PrintProduct::getTypeColorImage($params['substrateID'], $params['colorID'], $params['placement']);
    if(!$substrateFile || !($substrate = WideImage::load($substrateFile)) || !$substrate->isValid()) {
        throw new SpoolException('Substrate file is not valid');
    }
    // get logo file
    $logoFile = PrintProduct::getLogoImage($params['logoID']);
    if(!$logoFile || !($logo = WideImage::load($logoFile)) || !$logo->isValid()) {
        throw new SpoolException('Logo file is not valid');
    }
    // get spool sizes
    $spoolSize = PrintProduct::getSpooledImageSize($params['alias']);
    if(!$spoolSize) {
        throw new Exception('Spool sizes is not valid. Check Spooled Images Params file exist & correct');
    }
    // get spool result file
    $spoolFile = PrintProduct::getSpoolPath($params['printID'], $params['fileName']);
    // create subfolder if need
    $spoolSubDir = dirname($spoolFile);
    if (!is_dir($spoolSubDir) && !mkdir($spoolSubDir, 0775)) {
        throw new Exception('mkdir returned false result');
    } else if(basename($spoolSubDir) != basename(dirname($url_path))){
        throw new Exception('Spool subfolder is incorrect');
    }
    
    // create
    // create convas
    $convas = WideImage::createTrueColorImage($substrate->getWidth(), $substrate->getHeight());
    // fill convas by RGB color 
    $convas->fill(0, 0, $convas->allocateColor(255, 255, 255));
    // merge substrate & logo to convas file
    $convas = $convas->merge($substrate->merge($logo->resizeDown($params['width']), 'center', 'top + ' . $params['offset'], 100));
    // watermark load & place to convas file
    $watermarkFile = 'images/site/smart/watermark.png';
    if($watermarkFile && ($watermark = WideImage::load($watermarkFile)) && $watermark->isValid()) {
        $convas = $convas->merge($watermark->resizeDown($substrate->getWidth()), 'center', 'bottom - 10 ', 100);
    }
    // resize to out file
    $out = $convas->resizeDown($spoolSize['width'], $spoolSize['height'], 'inside');
    // output & save
    $out->output($params['ext'], 80);
    $out->saveToFile($spoolFile, 80);
    
} catch (SpoolException $e) {
    // add message to log file
    $logtext .= 'ERROR: '.$e->getMessage().PHP_EOL;
    // get noimage file
    $noimageFile = PrintProduct::getSpoolNoImageFile($params['alias']);
    if(!$noimageFile || !($noimage = WideImage::load($noimageFile)) || !$noimage->isValid()){
        $logtext .= 'ERROR: NoImage file is not valid on path ' . $noimageFile.PHP_EOL;
        // Send 404 error to header
        SpoolException::send404();
    } else {
        $noimage->output($params['ext'], 80);
    }
    
} catch (Exception $e) {
    // add message to log file
    $logtext .= 'ERROR: '.$e->getMessage().PHP_EOL;
    // Send 404 error to header
    SpoolException::send404();
    
}

$logtext .= 'REQUEST: '.$_SERVER['REQUEST_URI'].PHP_EOL;
$logtext .= 'Work finished by ' . (microtime(true) - $mctime) .' s. '.PHP_EOL;
$logtext .= str_repeat('-', 50).PHP_EOL.PHP_EOL;

//Write log file
if (($fp = @fopen($logfile, 'a'))) {
    fwrite($fp, $logtext);
    fclose($fp);
}

exit();