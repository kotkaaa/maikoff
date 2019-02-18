<?php

/*
 * WebLife CMS
 * Created on 21.11.2018, 19:29:55
 * Developed by http://weblife.ua/
 */

/**
 * Description of OrderHelper
 *
 * @author user5
 */
class OrderHelper {
    /*
     * новый
     */
    const STATUS_NEW = 1;
    /*
     *  в ожидании
     */
    const STATUS_WAIT = 2;
    /*
     *  на производстве
     */
    const STATUS_INDUSTRY = 3;
    /*
     *  на доставке
     */
    const STATUS_DELIVERY = 4;
    /*
     * выполнен
     */
    const STATUS_DONE = 5;
    /*
     * отклонен
     */
    const STATUS_CANCEL = 6;

    /*
     *  доставка курьером
     */
    const DELIVERY_TYPE_COURIER = 1;
    /*
     *  доставка почтовой службой
     */
    const DELIVERY_TYPE_POST = 2;
    /*
     *  доставка своими силами
     */
    const DELIVERY_TYPE_SELF = 3;
       
    /**
     * оплата наличкой
     */
    const PAYMENT_TYPE_CASH = 1;
    /**
     * оплата на карту
     */
    const PAYMENT_TYPE_CARD = 4;

    /*
     *  обратный звонок
     */
    const TYPE_ADMIN = 1;
    /*
     *  обратный звонок
     */
    const TYPE_CALLBACK = 2;
    /*
     *  через корзину
     */
    const TYPE_BASKET = 3;
    /*
     *  в один клик
     */
    const TYPE_FAST = 4;
    /*
     *  заявка
     */
    const TYPE_REQUEST = 5;

    const MAX_FILES_COUNT = 3;
    const MAX_EMAIL_FILESIZE = 24214400;
    const UPLOAD_MAX_FILESIZE_TEXT = 50; //mb
    const UPLOAD_MAX_FILESIZE = 52428800; //mb
    const UPLOAD_ALLOW_EXTENSION_TEXT = 'gif, jpeg, jpg, png, ai, eps, cdr, pdf, psd';
    const UPLOAD_ALLOW_EXTENSION = '/\.(gif|jpe?g|png|ai|eps|cdr|pdf|psd)$/i';
    /**
     * 
     * @param type $DB
     * @param type $orderID
     * @param type $filename
     * @param type $filepath
     * @param type $destfilepath
     * @return string
     */
    public static function saveAttachments($DB, $orderID, $filename, $filepath, $destfilepath) {
        $fileExt  = getFileExt($filename);
        $destfilename = trim(trim(basename($filename, ".".$fileExt), "."));
        $destfilename = createUniqueFileName($destfilepath, $fileExt, $destfilename, "o{$orderID}_");
        if (rename($filepath.$filename, $destfilepath.$destfilename)) {
            $DB->postToDB(array(
                "order_id" => $orderID,
                "filename" => $destfilename
            ), ORDER_FILES_TABLE);
            return $destfilename;
        }  
        return '';
    }
    
    public static function prepareAttachments($DB, $orderID, $files, $temp_filesize, $files_path, $files_url, $baseUrl) {
        $attachments = $attachmentsToEmail = array();
        if (!empty($files)) {
            $totalSize = 0;            
            foreach ($files as $file) {
                $filename = is_array($file) && isset($file['name']) ? $file['name'] : $file;
                if (file_exists($temp_filesize.$filename) && ($name = OrderHelper::saveAttachments($DB, $orderID, $filename, $temp_filesize, $files_path))) {
                    $attachment = array('name' => $name, 'filesize' => filesize($files_path.$name), 'url' => $baseUrl.$files_url.$name, 'inEmail' => false);
                    if(($totalSize += $attachment['filesize']) < self::MAX_EMAIL_FILESIZE) {
                        $attachmentsToEmail[] = $files_path.$name;
                        $attachment['inEmail'] = true;
                    }
                    $attachments[] = $attachment;
                }
            }
        }  
        return array('attachments' => $attachments, 'attachmentsToEmail' => $attachmentsToEmail);
    }

    /**
     * 1. Ищем пользователя в таблице users по номеру телефона
     * 2. Если не найден но введен емейл, то ищем в таблице users по емейлу
     * 3. Если не найден, то ищем в таблице orders по номеру телефона и емейлу одним запросом (номер телефона приоритетней)
     * 4. Если после всех потуг так и не нашли, то создаем нового
     * @param type $DB
     * @param type $arPostData
     * @return type
     */
    public static function getUserID($DB, $arPostData) {
        // check phone
        $uid = intval(getValueFromDB(USERS_TABLE, 'id', 'WHERE phone = "'.$arPostData['phone'].'" AND type="'.USER_TYPE_USER.'"'));
        // if not found check email
        if(!$uid && !empty($arPostData['email'])) {
            $uid = getValueFromDB(USERS_TABLE, 'id', 'WHERE email = "'.$arPostData['email'].'" AND type="'.USER_TYPE_USER.'"');
        }
        if(!$uid) {
            $uid = getValueFromDB(ORDERS_TABLE, 'user_id', 'WHERE phone="'.$arPostData['phone'].'"'.(!empty($arPostData['email']) ? ' OR email = "'.$arPostData['email'].'"' : '').' ORDER BY IF(phone="'.$arPostData['phone'].'", 1, 0) DESC');
        }
        // if not found add new user
        if(!$uid) {
            $arData = array(
                'login'      => PHPHelper::createLogin($arPostData['phone']),
                'firstname'  => !empty($arPostData['name']) ? $arPostData['name'] : '',
                'email'      => !empty($arPostData['email']) ? $arPostData['email'] : '',
                'phone'      => $arPostData['phone'],
                'created'    => date('Y-m-d H:i:s')
            );
            $result = $DB->postToDB($arData, USERS_TABLE);
            if($result && is_int($result)) {
                $uid = $result;
                foreach(SystemComponent::getAcceptLangs() as $key => $arLang) {
                    ActionsLog::getAuthInstance(ActionsLog::SYSTEM_USER, getRealIp())->save(ActionsLog::ACTION_CREATE, 'Создан новый пользователь', $key, $arData['firstname'], $uid, 'users');
                }
            }
        }
        return $uid;
    }
    
    /**
     * Определяем не забанен ли юзер
     * @param int $userID
     * @return int
     */
    public static function isBanned($userID) {
        return (int)getValueFromDB(USERS_TABLE, 'banned', 'WHERE id='.$userID);
    }

    /**
     * Определяем по статусу возможные изменения статуса
     * Если новый, то он может быть в ожидании или на производстве или отклонен
     * Если в ожидании, то он может быть на производстве или отклонен
     * Если на производстве, то он может быть на доставке или отклонен
     * Если на доставке, то он может быть выполнен или отклонен
     * Если выполнен или отклонен то нельзя сменить статус
     * @param int $statusID
     * @return array
     */
    public static function getAvailableStatuses($statusID) {
        $arStatuses = array();
        if($statusID == self::STATUS_NEW) {
            $arStatuses = array(self::STATUS_WAIT, self::STATUS_INDUSTRY, self::STATUS_CANCEL);
        } else if($statusID == self::STATUS_WAIT) {
            $arStatuses = array(self::STATUS_INDUSTRY, self::STATUS_CANCEL);
        } else if($statusID == self::STATUS_INDUSTRY) {
            $arStatuses = array(self::STATUS_DELIVERY, self::STATUS_DONE, self::STATUS_CANCEL);
        } else if($statusID == self::STATUS_DELIVERY) {
            $arStatuses = array(self::STATUS_DONE, self::STATUS_CANCEL);
        }
        return $arStatuses;
    }

    /**
     * Определяем по статусу можно ли редактировать заказ
     * Нельзя редактировать если статус выполнен или отклонен
     * @param int $statusID
     * @return boolean
     */
    public static function isEditable($statusID) {
        return in_array($statusID, array(self::STATUS_DONE, self::STATUS_CANCEL)) ? false : true;
    }

    /**
     * Определяем по статусу можно ли редактировать товары в заказе
     * Нельзя редактировать если статус выполнен или отклонен
     * @param int $statusID
     * @return boolean
     */
    public static function isEditableProducts($statusID) {
        return in_array($statusID, array(self::STATUS_DONE, self::STATUS_CANCEL/*, self::STATUS_INDUSTRY*/)) ? false : true;
    }

    /**
     * Проверяем корректность нового статуса
     * @param int $itemID
     * @return boolean
     */
    public static function checkStatusChange($statusID, $newStatusID) {
        return ($statusID == $newStatusID || in_array($newStatusID, self::getAvailableStatuses($statusID)));
    }

    /**
     * recalculate order totals
     * @param int $itemID
     */
    public static function updateTotals($itemID) {
        $totals = getItemRow(ORDER_PRODUCTS_TABLE, 'SUM(qty) total_qty, SUM((qty*price)-discount_value) total_price', 'WHERE order_id='.$itemID);
        updateRecords(ORDERS_TABLE, 'total_qty="'.$totals['total_qty'].'", total_price="'.$totals['total_price'].'"', 'WHERE id='.$itemID);
    }

    public static function getProductTotalPrice($item) {
        return ($item['price']*$item['qty']) - $item['discount_value'];
    }

    public static function getIndustryActions($item) {
        $text = '';
        if ($item['is_printed'])
            $text = '<div class="badge">порезано</div> <div class="badge">напечатано</div>';
        else if (!$item['is_cuted'])
            $text = '<input type="button" value="Порезано" class="buttons inline-block" data-id="'.$item['id'].'" data-column="is_cuted" onclick="setAction(this);"/>';
        else if (!$item['is_printed'])
            $text = '<div class="badge">порезано</div> &nbsp;&nbsp; <input type="button" value="Напечатано" class="buttons inline-block" data-id="'.$item['id'].'" data-column="is_printed" onclick="setAction(this);"/>';
        return $text;
    }

    public static function getProductBG($item) {
        return $item['is_printed'] ? '#e2ffdc' : ($item['is_cuted'] ? '#fffddc' : '#fff');
    }

    public static function getProductImageSource($relative_url) {
        $image = "";
        if($relative_url) {
            // удаляем временные метки и другую лабуду
            $relative_url = preg_replace('/[?#].*$/i', '', $relative_url);
            // подготавливаем путь и имя файла для прямого получени изображения
            $image_name = basename($relative_url);
            $image_path = prepareDirPath(dirname($relative_url)).$image_name;
            $image_tpath = WLCMS_RUNTIME_DIR.DS.'spool_'.date('YmdHis').'_'.$image_name;
            $image_unlink = false;
            // если не можем получить содержимое изображения напрямую, качаем через веб
            // если папка запаролирована, то можно с указанием логина и пароля
            // WLCMS_HTTP_HOST = WLCMS_HTTP_PREFIX.'weblife:maikoff20@'.$_SERVER["HTTP_HOST"]
            if(!file_exists($image_path) && self::loadFile(WLCMS_HTTP_HOST.$relative_url, $image_tpath)) {
                $image_path = $image_tpath;
                $image_unlink = true;
            }
            if(($info = getimagesize($image_path))){
                $image = "data:{$info["mime"]};base64," . base64_encode(file_get_contents($image_path));
            }
            if($image_unlink){
                @unlink($image_tpath);
            }
        }
        return $image;
    }

    /**
     * @param string $src
     * @param string $dest
     * @return bool
     */
    public static function loadFile($src, $dest) {
        // get file by cUrl
        if (function_exists('curl_version')) {
            $ch = curl_init();
            $fp = fopen($dest, 'wb');
            curl_setopt($ch, CURLOPT_URL, $src);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            if(strpos($src, 'https') !== FALSE)
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $res = curl_exec($ch) ? true : false;
            curl_close($ch);
            fclose($fp);
        } else { // no CUrl, try differently
            $res = file_put_contents($dest, file_get_contents($src)) ? true : false;
        }
        return $res;
    }

    public static function getRequestTypes() {
        return array(self::TYPE_ADMIN, self::TYPE_BASKET, self::TYPE_FAST);
    }

    public static function isRequest($typeID) {
        return in_array($typeID, array(self::TYPE_CALLBACK, self::TYPE_REQUEST));
    }
}