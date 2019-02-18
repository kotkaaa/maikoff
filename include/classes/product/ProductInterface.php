<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

/*
 * WebLife CMS
 * Created on 07.06.2018, 16:08:38
 * Developed by http://weblife.ua/
 */

/**
 *
 * @author Andreas
 */
interface ProductInterface {

    /**
     * @return string
     */
    public static function getModule();

    /**
     * Build Select Items Sql
     * @param string $where
     * @param string $group
     * @param string $having
     * @param string $order
     * @param string $limit
     * @param int $sortID
     * @return string
     */
    public static function getItemsSql($where = '', $group = '', $having = '', $order = '', $limit = '', $sortID = 0);

    /**
     * @param string $idKey
     * @return array
     */
    public static function parseItemIdKey ($idKey);

}
