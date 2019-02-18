<?php

/*
 * WebLife CMS
 * Created on 12.06.2018, 0:51:44
 * Developed by http://weblife.ua/
 */

/**
 * Description of SpoolException
 *
 * @author Andreas
 */
class SpoolException extends Exception {

    public static function send404(){
        // Send 404 error to header
        header('HTTP/1.0 404 Not Found');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");// дата в прошлом
        header("Last-Modified: " . gmdate("D, d M Y H(idea)(worry)") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");// HTTP/1.1
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");// HTTP/1.0
    }

}
