<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$arrPageData['headCss'][] = '/css/smart/error.css';

if (!headers_sent()) {
    header('HTTP/1.0 404 Not Found');
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // дата в прошлом
    header("Last-Modified: " . gmdate("D, d M Y H(idea)(worry)") . " GMT");
     // всегда модифицируется
    header("Cache-Control: no-store, no-cache, must-revalidate");// HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");// HTTP/1.0
}