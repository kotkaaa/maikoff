<?php 
/**
 * Description of this class
 *
 * ForwardsCached is a wrapper class to manipulate Forwards URI from MemCache
 * Created on 04.02.2015, 13:02:36
 * @author Andreas, WebLife
 * @copyright 2015
 */
class ForwardsCached {

    public static function getTarget($uri) {
        $key = self::prepareUri($uri);
        $exp = 'SELECT * FROM `'.FORWARDS_TABLE.'` WHERE `active`=1 AND `urifrom`="'.$key.'"';
        $res = mysql_query($exp);
        if($res && mysql_num_rows($res) && ($row = mysql_fetch_assoc($res))) {            
            return $row['urito'];
        }
        return '';
    }

    // чтобы устранит двойную переадресацию перенес из htaccess сюда
    #    RewriteCond %{REQUEST_FILENAME} !-f
    #    RewriteCond %{REQUEST_URI} \.html [NC]
    #    RewriteRule ^(.*)\.html(.*)$ /$1/$2 [R=301,L]
    public static function getCompatibilityTarget($uri) {
        // поскольку сеошники запросили оставить суффикс html, то нижеследующий код не нужен
        // если ссылка нового образца 
//        if(strpos($uri, '.html')===false){
            return self::getTarget($uri);
//        }
        // иначе, если ссылка старого образца, т.е. с html, тогда отправляем через LIKE старого образца и нового 
        $key = self::prepareUri($uri);
        $new = str_replace('.html', '/', $key);
        $keys = array($key, $new);
        $exp = 'SELECT * FROM `'.FORWARDS_TABLE.'` WHERE `active`=1 AND `urifrom` IN ("'.implode('","', $keys).'") LIMIT 1';
        $res = mysql_query($exp);
        if($res && mysql_num_rows($res) && ($row = mysql_fetch_assoc($res))) {  
            return $row['urito'];
        } else  {
            // редиректим на новую ссылку
            return $new;
        }
    }

    public static function existUri($uri, $excludeID=0){
        $key = self::prepareUri($uri);
        $exp='SELECT `id` FROM `'.FORWARDS_TABLE.'` WHERE `id`<>'.intval($excludeID).' AND (`urifrom`="'.$key.'" OR TRIM(TRAILING "/" FROM `urito`)="'.$key.'")';
        $res=mysql_query($exp) or die('<br>Invalid query:<br>'.$exp.'<br>'.mysql_error());
        return ($res && mysql_num_rows($res)>0);
    }

    public static function prepareUri($uri, $checkSuffix = false){     
        //remove protocol
        $uri = str_replace(array('http://', 'https://', 'http://www.', 'https://www.',), '', $uri);
        //remove get        
        $parts = explode('?', $uri);
        $uri = $parts[0];
        //remove domain
        $parts = explode('/', $uri, 2);
        $uri = count($parts)>1 ? '/'.$parts[1] : $parts[0]; 
        //add first /
        if($uri[0] != '/') {
            $uri = '/'.$uri;
        }        
        if($checkSuffix && URL_SEO_SUFFIX && (substr($uri, -1, strlen(URL_SEO_SUFFIX)) != URL_SEO_SUFFIX)) {
            $uri .= URL_SEO_SUFFIX;
        }
        //replace double /
        $uri = str_replace('//', '/', $uri);
        return $uri;
    }
}
