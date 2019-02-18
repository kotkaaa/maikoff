<?php 

define('WEBlife', 1);
define('WLCMS_ZONE', "site");

if (!defined("DS")) define('DS', DIRECTORY_SEPARATOR);

error_reporting(E_ALL);

// change to root dir
chdir("..".DS);

require_once('include/functions/base.php');

define('WLCMS_WRITABLE_CHMOD',          '0775');
define('UPLOAD_DIR',          'uploaded'); // set WebLife CMS UPLOAD DIR
define('UPLOAD_URL_DIR',      '/'.UPLOAD_DIR.'/'); // set WebLife CMS UPLOAD URL DIR
define('SPOOL_URL_DIR',       UPLOAD_URL_DIR."catalog_spool/"); // set WebLife CMS UPLOAD URL DIR
define('SPOOL_DIR',           prepareDirPath(SPOOL_URL_DIR, TRUE)); // set WebLife CMS UPLOAD URL DIR
define('WLCMS_USE_HTTPS',     (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')); // [ 0 | 1 ] SET Chmod to files and directories to can change them
define('WLCMS_HTTP_PROTOCOL', "http".(WLCMS_USE_HTTPS ? "s" : "")); // [ 0 | 1 ] SET Chmod to files and directories to can change them
define('WLCMS_HTTP_PREFIX',   WLCMS_HTTP_PROTOCOL."://"); // [ 0 | 1 ] SET Chmod to files and directories to can change them
define('WLCMS_HTTP_HOST',     WLCMS_HTTP_PREFIX.$_SERVER["HTTP_HOST"]); // [ 0 | 1 ] SET Chmod to files and directories to can change them

try {
    $image     = $_SERVER["DOCUMENT_ROOT"].DS.cleanDirPath($_SERVER['REQUEST_URI']);
    $basename  = @basename($image);
    $watermark = $_SERVER["DOCUMENT_ROOT"].DS.cleanDirPath("/images/site/smart/watermark_pattern".(preg_match("/^big_/", $basename) ? "_small" : "").".png");
    waterMark($image, $watermark);
} catch (Exception $e) {
    $e->getMessage();
}
    
/*
 * You can add file .htaccess to directory to show all files in directory with watermark
 * and change parameter $original = $_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI']
.htaccess content:
DirectoryIndex index.php
<FilesMatch "\.(gif|jpg|png|JPG|JPEG|jpeg)$">
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} -f
   RewriteRule ^!(middle|small)\.jpg$ /include/watermark_catalog.php [T=application/x-httpd-php,L,QSA]
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
function waterMark($filename, $watermark) {
    $hash = md5(sha1($filename.$watermark));
    $base = basename($filename);
    $ext  = getFileExt($base);
    $spoolname = $hash.".".$ext;
    if (file_exists(SPOOL_DIR.$spoolname)) {
        $filename = SPOOL_URL_DIR.$spoolname;
        header("Content-Type: image/" . $ext);
        exit(file_get_contents(SPOOL_DIR.$spoolname));
    }
    $Original = new Imagick();
    $Stamp  = clone $Original;
    $Original->readImage($filename);
    $Stamp->readImage($watermark);
    // main image dimension
    $geo = $Original->getImageGeometry();
    $x   = $geo['width'];
    $y   = $geo['height'];
    unset($geo);
    // stamp image dimension
    $geo_stamp = $Stamp->getImageGeometry();
    $sx = $geo_stamp['width'];
    $sy = $geo_stamp['height'];
    unset($geo_stamp);
    // repeat stamp image and write it on main image
    for ($i = 0; $i <= round($x / $sx) + 1; $i++) {
        for ($j = 0; $j <= round($y / $sy) + 1; $j++) {
            // compose watermark onto image
            $Original->compositeImage($Stamp, $Stamp->getImageCompose(), ($i * $sx), ($j * $sy));
        }
    }
    if ($f = fopen(SPOOL_DIR.$spoolname, "w+")) {
        $Original->writeImageFile($f);
        fclose($f);
        header("Content-Type: image/" . $ext);
        exit(file_get_contents(SPOOL_DIR.$spoolname));
    }
}
?>