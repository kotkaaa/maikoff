<?php defined('WEBlife') or die( 'Restricted access' ); // no direct access

/*
 * WebLife CMS
 * Created on 06.06.2018, 14:14:41
 * Developed by http://weblife.ua/
 */

require_once 'ProductInterface.php';
require_once 'SpoolException.php';

/**
 * Description of Product
 * Use $product_id for record with color definition
 *
 * @author Andreas
 */
abstract class Product implements ProductInterface {

    const SPOOL_SUBDIR_STEP = 1000;
    const SPOOL_SUFFIX = '_spool';
    const SPOOL_EXT = 'jpg';
    const IMAGE_ALIAS = 'def';
    const IMAGE_UNDEF = 'noimage.jpg';
    const SEOTEXT_SEP = '-';
    const PATTERN_IMAGE = '*.jpg';
    const PATTERN_NUMERIC = '[0-9]*';
    const PATTERN_STRING = '[a-z]*';

    /**
     * @param array $args
     * @param string $format
     * @return string
     */
    protected static function packItemIdKey (array $args, $format = 'p%d') {
        return vsprintf($format, $args);
    }

    /**
     * @param string $idKey
     * @param string $pattern
     * @return array
     */
    public static function unPackItemIdKey ($idKey, $pattern = '/^p(\d+)$/i') {
        $matches = null;
        if(preg_match($pattern, $idKey, $matches)){
            $matches[0] = static::getModule();
        }
        return $matches;
    }

    /**
     * @return string
     */
    protected static function getSpoolRootUrl(){
        return self::getImagesUrl().self::SPOOL_SUFFIX;
    }

    /**
     * @return string
     */
    final public static function getSpoolRootDir(){
        return self::getImagesDir().self::SPOOL_SUFFIX;
    }

    /**
     * Get name to spool subfolder
     * @param int $itemID
     * @return string
     */
    final public static function getSpoolSubDirName($itemID){
        return ($itemID > self::SPOOL_SUBDIR_STEP) ? ceil($itemID/self::SPOOL_SUBDIR_STEP) : 1;
    }

    /**
     * Get Url to spool subfolder or if filename is not empty - to file
     * @param int $itemID
     * @param string $filename
     * @return string
     */
    final public static function getSpoolUrl($itemID, $filename=''){
        return self::getSpoolRootUrl() . '/' . self::getSpoolSubDirName($itemID) . ($filename ? '/' . $filename : '');
    }

    /**
     * Get path to spool subfolder or if filename is not empty - to file
     * @param int $itemID
     * @param string $filename
     * @return string
     */
    final public static function getSpoolPath($itemID, $filename=''){
        $spoolUrl = ltrim(self::getSpoolUrl($itemID, $filename), '/');
        return ('/' == DIRECTORY_SEPARATOR ? $spoolUrl : str_replace('/', DIRECTORY_SEPARATOR, $spoolUrl));
    }

    /**
     * @return string
     */
    final public static function getSpoolInteractiveFileUrl(){
        return '/interactive/spool_'.static::getModule().'.php';
    }

    /**
     * @return string
     */
    final public static function getSpoolInteractiveDefaultFileContent(){
        return 'Header("HTTP/1.0 404 Not Found"); // code' . PHP_EOL
             . 'Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // date in past' . PHP_EOL
             . 'Header("Last-Modified: " . gmdate("D, d M Y H(idea)(worry)") . " GMT");' . PHP_EOL
             . 'Header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1' . PHP_EOL
             . 'Header("Cache-Control: post-check=0, pre-check=0", false);' . PHP_EOL
             . 'Header("Pragma: no-cache"); // HTTP/1.0' . PHP_EOL
             . 'exit();' . PHP_EOL;
    }

    /**
     * @param array $imagesParams
     * @return array
     */
    public static function getNoImageFiles(array $imagesParams){
        $files = array('' => self::IMAGE_UNDEF);
        foreach($imagesParams as $params){
            list($prefix, , , $alias) = $params;
            $files[$alias] = $prefix . self::IMAGE_UNDEF;
        } return $files;
    }

    /**
     * @return array
     */
    public static function prepareImagesParams(){
        $query = 'SELECT `aliases` FROM `'.IMAGES_PARAMS_TABLE.'` WHERE `module`="'.static::getModule().'" LIMIT 1';
        $result = mysql_query($query);
        return SystemComponent::prepareImagesParams(($result && mysql_num_rows($result) > 0) ? mysql_result($result, 0) : null);
    }

    /**
     * @param string $imageAlias
     * @return array
     */
    public static function getSpoolNoImageFile($imageAlias){
        foreach (self::getSpooledImagesParams() as $params) {
            list($prefix, , , $alias) = $params;
            if ($alias == $imageAlias) {
                return self::getSpoolRootDir() . DIRECTORY_SEPARATOR . $prefix . self::IMAGE_UNDEF;
            }
        } return null;
    }

    /**
     * @param string $imageAlias
     * @return array
     */
    public static function getSpooledImageSize($imageAlias){
        foreach(self::getSpooledImagesParams() as $params){
            list(, $width, $height, $alias) = $params;
            if($alias == $imageAlias){
                return array(
                    'width' => $width,
                    'height' => $height,
                );
            }
        } return null;
    }

    /**
     * @return array
     */
    public static function getSpooledImagesParams(){
        $params = @include self::getSpoolRootDir().DIRECTORY_SEPARATOR.'images_params.inc';
        return $params ? $params : array();
    }

    /**
     * @return array
     */
    public static function prepareSpoolImagesParams(){
        $spoolDir = self::getSpoolRootDir();
        $params = array();
        if (is_dir($spoolDir)) {
            $params = self::prepareImagesParams();
            $string = '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($params, true) . ';' . PHP_EOL;
            file_put_contents($spoolDir.DIRECTORY_SEPARATOR.'images_params.inc', $string);
        } return $params;
    }

    /**
     * @param string $pathPattern
     * @return array
     */
    final public static function findFiles($pathPattern){
        return glob($pathPattern, GLOB_NOSORT);
    }

    /**
     * @param string $pathPattern
     * @return string
     */
    final public static function findFile($pathPattern){
        $files = self::findFiles($pathPattern);
        return $files ? reset($files) : null;
    }

    /**
     * @param string $pattern
     * @return int
     */
    final public static function deleteSpoolFiles($pattern = self::PATTERN_IMAGE){
        $affected = 0;
        if ($pattern) {
            $spoolDir = self::getSpoolRootDir();
            $dirHandle = opendir($spoolDir);
            while (($dir = readdir($dirHandle)) !== false) {
                $path = $spoolDir . DIRECTORY_SEPARATOR . $dir;
                if ($dir != '..' && $dir != '.' && is_dir($path)) {
                    foreach (self::findFiles($path . DIRECTORY_SEPARATOR . $pattern) as $file) {
                        if (@unlink($path . DIRECTORY_SEPARATOR . $file)) {
                            $affected++;
                        }
                    }
                }
            } closedir($dirHandle);
        } else die('Hey, you set empty pattern');
        return $affected;
    }

    /**
     * @param string $itemID
     * @return int
     */
    public static function deleteSpoolByItem($itemID){
        return self::deleteSpoolFiles(self::createItemFileName($itemID, self::PATTERN_IMAGE));
    }

    /**
     * @param int $itemID
     * @return string
     */
    final public static function getImagesUrl($itemID = 0){
        return UPLOAD_URL_DIR.static::getModule().($itemID ? '/'.$itemID : '');
    }

    /**
     * @param int $itemID
     * @return string
     */
    final public static function getImagesDir($itemID = 0){
        return UPLOAD_DIR . DIRECTORY_SEPARATOR . static::getModule().($itemID ? DIRECTORY_SEPARATOR.$itemID : '');
    }

    /**
     * @param string $itemID
     * @param string $seoName
     * @return string
     */
    final public static function createItemFileName($itemID, $seoName){
        return self::createFileNameElement('p', $itemID, $seoName);
    }

    /**
     * @param string $preFix
     * @param string $itemID
     * @param string|bool $suFix default false - not add with SEOTEXT_SEP
     * @return string
     */
    final protected static function createFileNameElement($preFix, $itemID, $suFix = false){
        return $preFix.$itemID.($suFix===false ? '' : self::SEOTEXT_SEP.$suFix);
    }

    /**
     * prepare spool directory to use
     * @return string
     */
    final public static function initSpool(){
        $spoolDir = self::getSpoolRootDir();
        if (!is_dir($spoolDir)) {
            self::prepareSpool();
            $interactive_file = WLCMS_ABS_ROOT . ltrim(self::getSpoolInteractiveFileUrl(), '/');
            if (!file_exists($interactive_file)){
                $interactive_content = '<pre>'
                    . 'Interactive spool file is ubsent in path ' . $interactive_file . PHP_EOL
                    . 'Default code that return 404 answer : ' . PHP_EOL . PHP_EOL
                    . htmlspecialchars('<?php') . PHP_EOL . PHP_EOL
                    . self::getSpoolInteractiveDefaultFileContent()
                    . '</pre>' . PHP_EOL
                ; die($interactive_content);
            }
        } return $spoolDir;
    }

    /**
     * prepare spool directory to use
     * @return string
     */
    final public static function prepareSpool(){
        // интерактивный файл необходимый для обслуживания директории
        $interactiveUrl = self::getSpoolInteractiveFileUrl();
        $interactiveFile = WLCMS_ABS_ROOT . ltrim($interactiveUrl, '/');
        if (!file_exists($interactiveFile)){
            $interactiveContent = '<?php' . PHP_EOL . PHP_EOL . self::getSpoolInteractiveDefaultFileContent();
            file_put_contents($interactiveFile, $interactiveContent);
        }
        // spool папка
        $spoolDir = self::getSpoolRootDir();
        if (!is_dir($spoolDir)) {
            mkdir($spoolDir, 0775);
        }
        // htaccess файл 
        $accessFile = $spoolDir . DIRECTORY_SEPARATOR . '.htaccess';
        if (!file_exists($accessFile)) {
            $accessContent =  'Options All -Indexes' . PHP_EOL
                            . 'RewriteEngine On' . PHP_EOL
                            . 'ErrorDocument 404 '.$interactiveUrl.'?spoolled' . PHP_EOL . PHP_EOL
                            . 'Deny from all' . PHP_EOL
                            . '<FilesMatch "^.*(\.'.self::SPOOL_EXT.')?$">' . PHP_EOL
                            . '  Order Deny,Allow' . PHP_EOL
                            . '  Allow from all' . PHP_EOL
                            . '</FilesMatch>' . PHP_EOL
            ; file_put_contents($accessFile, $accessContent);
        }
        // подготовка параметров изображений
        $imagesParams = self::prepareSpoolImagesParams();
        // производные изображения и настройки
        $sourceDir = self::getImagesDir();
        foreach(static::getNoImageFiles($imagesParams) as $file){
            $sourcePath = $sourceDir . DIRECTORY_SEPARATOR . $file;
            $spoolPath = $spoolDir . DIRECTORY_SEPARATOR . $file;
            if(file_exists($sourcePath) && !file_exists($spoolPath)) {
                copy($sourcePath, $spoolPath);
            }
        } return $spoolDir;
    }
    /**
     * @param int $categoryID
     * @param int $linkID
     * @param string $linkColumn
     * @param string $linkTable
     * @param boolean $inList
     * @param boolean $showAll
     * @param boolean $showEmptyAttr
     * @return array
     */
    protected static function getItemAttributes($categoryID, $linkID, $linkColumn, $linkTable, $inList=false, $showAll=false, $showEmptyAttr=false){
        $files_url  = UPLOAD_URL_DIR."attributes/";
        $files_path = prepareDirPath($files_url);
        $attributes = array();
        if (!$inList) {
            $query  = "SELECT a.*, CONCAT(GROUP_CONCAT(DISTINCT pa.`value`), ',', 0) AS `vals`, "
                    . "IF(ca.`order` IS NULL, 0, ca.`order`) AS `itemorder`, "
                    . "IF(cag.`order` IS NULL, 0, cag.`order`) AS `grouporder` "
                    . "FROM `".ATTRIBUTES_TABLE."` a "
                    . "LEFT JOIN `".$linkTable."` pa ON(pa.`aid`=a.`id`) "
                    . "LEFT JOIN `".ATTRIBUTE_GROUPS_TABLE."` ag ON(ag.`id`=a.`gid`) "
                    . "LEFT JOIN `".CATEGORY_ATTRIBUTES_TABLE."` ca ON(ca.`aid`=a.`id` AND ca.`cid`='{$categoryID}') "
                    . "LEFT JOIN `".CATEGORY_ATTRIBUTE_GROUPS_TABLE."` cag ON(cag.`gid`=ag.`id` AND cag.`cid`='{$categoryID}') "
                    . "WHERE pa.`{$linkColumn}`={$linkID} ".(!$showAll ? "AND ca.`cid`='{$categoryID}' AND cag.`cid`='{$categoryID}' " : "")
                    . "GROUP BY a.`id` ".(!$showEmptyAttr ? "HAVING CHAR_LENGTH(vals)>0 " : "")
                    . "ORDER BY `grouporder`, `itemorder` ASC";
            $result = mysql_query($query);
            if ($result and mysql_num_rows($result)>0) {
                while ($row = mysql_fetch_assoc($result)) {
                    $row['values'] = getComplexRowItems(ATTRIBUTES_VALUES_TABLE, "id, title, title_single, title_multi, image", "WHERE `id` IN({$row['vals']})", "`order`");
                    for ($i=0; $i<count($row['values']); $i++) {
                        $row['values'][$i]["image"] = (!empty($row['values'][$i]["image"]) and file_exists($files_path.$row['values'][$i]["image"])) ? $files_url.$row['values'][$i]["image"] : "";
                    } $attributes[$row['id']] = $row;
                }
            }
        } return $attributes;
    }

    protected static function setItemBasketQty(&$item) {
        global $Basket;
        $item["basket_qty"] = 0;
        foreach(array_keys($item["sizes"]) as $key){
            $item["sizes"][$key]["basket_qty"] = $Basket->qty($item["sizes"][$key]["idKey"]);
            $item["basket_qty"] += $item["sizes"][$key]["basket_qty"];
        }
    }
}