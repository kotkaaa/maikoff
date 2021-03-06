<?php

/**
 * WEBlife CMS
 * Created on 05.01.2011, 12:20:17
 * Developed by http://weblife.ua/
 */

defined('WEBlife') or die('Restricted access'); // no direct access

if(!defined('USERS_ACCESS_TABLE')){ define('USERS_ACCESS_TABLE', 'users_access');}


/**
 * Description of UsersAccess class
   Description: 
 *  
 * 
 //uid - только для пользователя, если группа, то uid=0 
 DROP TABLE IF EXISTS `users_access`;
 CREATE TABLE `users_access` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) default '0',
  `gid` int (11) not null,
  `modules` TINYTEXT,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_group` (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; 
 * @author WebLife
 * @copyright 2011
 */
class UserAccess {
    /*
     * константа
     */
    const TYPE_AUTH = 'auth';
    /*
     * доступ к авторизации
     */
    protected $authAccess;
    /*
     * доступные модули
     */
    protected $availableModules;
    /*
     * айди пользователя
     */
    protected $userID;
    /*
     * тип пользователя
     */
    protected $userType;
    
    /**
     * По идентификатору пользователя и его типу, определяет есть ли разрешение на авторизацию и массив доступных для него модулей
     * @param obj $user
     */
    public function __construct($userID=0, $userType='') {
        if($userID>0 && !empty($userType)) {
            $this->init($userID, $userType);
        } else {
            $this->availableModules = array();
            $this->authAccess = false;
        }
    }
    /**
     * 
     * @param type $userID
     * @param type $userType
     * @return $this
     */
    public function init($userID=0, $userType='') {
        if($userID>0 && !empty($userType) && ($this->userID !=$userID || $this->userType!=$userType)) {
            $settings = getItemRow(USERS_ACCESS_TABLE.' uat LEFT JOIN '.USERTYPES_TABLE.' ut ON uat.`gid`=ut.`id`', 'uat.*', 'WHERE ut.`name`="'.$userType.'" AND (uat.`uid`='.$userID.' OR uat.`uid`=0) ORDER BY uat.`uid` DESC');
            $this->availableModules = !empty($settings) ? explode(',', $settings['modules']) : array();
            $this->availableModules[] = 'welcome';
            $this->authAccess = in_array(self::TYPE_AUTH, $this->availableModules);
        }
        return $this;
    }
    /**
     * 
     * @param type $usertype
     * @return bool
     */
    public static function isEmployee($usertype) {
        return in_array($usertype, array(USER_TYPE_INDASTRIALIST, USER_TYPE_MANAGER, USER_TYPE_PUBLISHER));
    }
    /**
     * 
     * @param type $usertype
     * @return bool
     */
    public static function isUser($usertype) {
        return in_array($usertype, array(USER_TYPE_USER));
    }
    /**
     * 
     * @param type $usertype
     * @return bool
     */
    public static function isAdmin($usertype) {
        return in_array($usertype, array(USER_TYPE_ADMINISTRATOR));
    }
    /**
     * Проверяет есть ли текщий модуль в списке допустимых
     * @return array
     */
    public function getAccessToModule($module) {
        return in_array($module, $this->availableModules);
    }
    /**
     * Возвращает доступ авторизации
     * @return bool
     */
    public function getAuthAccess() {
        return $this->authAccess;
    }
    /**
     * 
     * @return string
     */
    public function getAccessError() {
        return 'У Вас нет доступа на совершение этого действия!';
    }
}
