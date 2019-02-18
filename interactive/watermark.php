<?php 

define('WEBlife', 1);

error_reporting(0);

// change to root dir
chdir("..".DIRECTORY_SEPARATOR);

require_once('include/functions/base.php');
require_once('include/classes/wideimage/WideImage.php');
require_once('include/classes/product/SpoolException.php');

$arAliases = array(
    'big_'    => array('x' => 'center', 'y' => 'center',      'w' => 'images/site/smart/watermark.png'),
    'middle_' => array('x' => 'center', 'y' => 'bottom - 100', 'w' => 'images/site/smart/watermark.png'),
);

try {
    if(!empty($_GET['img']) && !empty($_GET['dir']))
         waterMark($arAliases, $_SERVER["DOCUMENT_ROOT"].'/uploaded/'.$_GET['dir'].'/'.$_GET['img']);
    else waterMark($arAliases, $_SERVER["DOCUMENT_ROOT"].$_SERVER['REQUEST_URI']);
} catch (Exception $e) {
    // Send 404 error to header
    SpoolException::send404();
}
    
/*
 * You can add file .htaccess to directory to show all files in directory with watermark
 * and change parameter $original = $_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI']
.htaccess content:
DirectoryIndex index.php
<FilesMatch "\.(gif|jpg|png|JPG|JPEG|jpeg)$">
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} -f
   RewriteRule ^(.*)$ /include/watermark.php [T=application/x-httpd-php,L,QSA]
</FilesMatch>
<Files "*.php">
Deny from all
</Files>
<Files "*.pl">
Deny from all
</Files>
Allow from all

 * OR You can use url in image src like  src="/include/watermark.php?imgsrc=<?=urlencode($image)."&nhash=".date('U');?>"
 * and change parameter $original = $_SERVER['DOCUMENT_ROOT'].$_GET['imgsrc']
 */

############################ FUNCTIONS #########################################
/////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
function waterMark($arAliases, $original) {
    $original     = urldecode($original);
    $originalname = basename($original);
    $originalext  = getFileExt($original);
    // Get Images Size
    $info_o = @getImageSize($original);
    if (!$info_o) throw new Exception('can\'t load original image');
    $watermark = '';
    $x = $y = 'center';
    foreach ($arAliases as $alias => $settings) {
        if(strpos($originalname, $alias)!==false) {
            $x = $settings['x'];
            $y = $settings['y'];
            $watermark = $settings['w'];
            break;
        }
    }    
    $info_w = @getImageSize($watermark);
    if (!$info_w) throw new Exception('can\'t load watermark image');
    $original = WideImage::load($original);
    $watermark = WideImage::load($watermark);
    $merged = $original->resizeCanvas($info_o[0], $info_o[1], "center", "middle", $original->allocateColor(255, 255, 255), "any", true)->merge($watermark, $x, $y, 100);
    $merged->output($originalext);
    return true;
}
?>