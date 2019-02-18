<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SeoTools
 *
 * @author user5
 */
class SeoTools {
    /**
     * @example SeoTools::ConvertUriCase()
     * @param string $url
     * @param int $mode
     * @return string
     */
    public function ConvertUriCase ($url, $mode = MB_CASE_LOWER) {
        return mb_convert_case($url, $mode);
    }
    /**
     * @example SeoTools::AddEndingSlash()
     * @param string $url
     * @param string $query
     * @param string $sep
     * @param string $suffix
     * @return string
     */
    public function AddEndingSuffix ($url, $query = "", $sep = "?", $suffix = "/") {
        return $url.$suffix.($query ? $sep.$query : '');
    }
    /**
     * @example SeoTools::RemoveSlashes()
     * @param string $url
     * @return string
     */
    public function RemoveSlashes ($url) {
        $url =  preg_replace("/\/{2,}/", "/", $url);
        return $url;
    }
    /**
     * @example SeoTools::Redirect301()
     * @param string $url
     */
    public function Redirect301 ($url) {
        header('HTTP/1.1 301 Moved Permanently');
        header("Request-URI: {$url}");
        header("Content-Location: {$url}");
        header("Location: {$url}");
        exit();
    }
}