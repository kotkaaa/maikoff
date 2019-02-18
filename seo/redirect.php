<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'include/classes/SeoTools.php';
$SM = new SeoTools();
$RUri       = $_SERVER["REQUEST_URI"];
$QString    = $_SERVER['QUERY_STRING'];
$Hash       = (substr($RUri, -1, 1)=="#") ? "#" : false;

if (!empty($RUri)) {
    // Redirect without index.php in url
    if (!$IS_DEV and strpos($RUri, '/index.php')===0) {
        $RUri = "/".($QString ? '?'.$QString : '');
        $SM->Redirect301($RUri);
    }
    
    // redirect to root from home seo path
    if (preg_match("/^\/home/", $RUri)) {
        $RUri = preg_replace("/^\/home/", "/", $RUri);
        $SM->Redirect301($RUri);
    }
    
    // convert uri case with redirect 301
    $nUri = str_replace("?".$QString, "", $RUri);
    if ($SM->ConvertUriCase($nUri) != $nUri) {
        $RUri = $SM->ConvertUriCase($nUri).($QString ? "?".$QString : "");
        $SM->Redirect301($RUri);
    }
    
    // add slash before query string && hash
    // remove query string
    $nUri = str_replace("?".$QString, "", $RUri);
    // remove hash
    if($Hash) $nUri = str_replace($Hash, "", $nUri);
    // add suffix
    $nSuffix = (empty($nUri) || $nUri=='/') ? '/' : URL_SEO_SUFFIX;
    $nLn = strlen($nSuffix);
    if (substr($nUri, -$nLn, $nLn)!=$nSuffix) {
        if($Hash) $QString .= $Hash;
        $RUri = $SM->AddEndingSuffix($nUri, $QString, "?", $nSuffix);
        $SM->Redirect301($RUri);
    }
    
    // remove unnecessary slashes
    if (preg_match("/\/{2,}/", $RUri)) {
        $RUri = $SM->RemoveSlashes($RUri);
        $SM->Redirect301($RUri);
    }
}