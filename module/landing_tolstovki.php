<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

// include page css
$arrPageData['headCss'][]     = '/css/smart/landing.css';
$arrPageData['headScripts'][] = "/js/smart/landing".(!$IS_DEV ? ".min" : "").".js";