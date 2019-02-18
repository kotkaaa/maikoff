<?php

/**
 * Description of Selections
 *
 * @author user5
 */
class Selections {
    /**
     * Selections alias keys
     */
    const SELECTIONS_HOME_PAGE_1  = 'home_1';
    const SELECTIONS_HOME_PAGE_2  = 'home_2';
    const SELECTIONS_THANKS_PAGE  = 'thanks';
    /**
     * Selection types (product | custom)
     */
    const SELECTION_TYPE_PRODUCTS = 1;
    const SELECTION_TYPE_CUSTOM   = 2;

    static $SELECTIONS = array(
        self::SELECTIONS_HOME_PAGE_1 => 'Главная страница - 1 блок',
        self::SELECTIONS_HOME_PAGE_2 => 'Главная страница - 2 блок',
        self::SELECTIONS_THANKS_PAGE => 'Страница спасибо за покупку - блок товаров к празднику',
    );

    protected $UrlWL;
    protected $DB;
    protected $use_cache;

    public function __construct (DbConnector $DB, $UrlWL = null) {
        $this->DB        = $DB;
        $this->UrlWL     = ($UrlWL instanceof UrlWL ? $UrlWL->copy() : $UrlWL);
        $this->use_cache = 0;
    }
}
/**
 * Custom (type-free) selections class
 */
class CustomSelections extends Selections {
    /**
     * @example $CustomSelections->getSeletion($ailas)
     * @param String $alias
     * @return array
     */
    public function getSelection ($alias) {
        if (($item = $this->use_cache ? PHPHelper::getMemCache()->get(CacheWL::KEY_SELECTION.'_'.$alias) : false) === false) {
            $query = $this->DB->Query("SELECT * FROM `".SELECTIONS_TABLE."` WHERE `alias`='{$alias}' LIMIT 1");
            if ($this->DB->getNumRows() > 0) {
                $item = $this->DB->fetchAssoc();
                $item["title"] = unScreenData($item["title"]);
                $item["descr"] = unScreenData($item["descr"]);
                $item["items"] = $this->getSelectionItems($item["id"]);
            } else $item = null;
            if ($this->use_cache) PHPHelper::getMemCache()->set(CacheWL::KEY_SELECTION.'_'.$alias, $item, 3600 * 4);
        } return $item;
    }
    /**
     * @example $this->getSelectionItems($selectionID)
     * @param int $selectionID
     * @return array
     */
    private function getSelectionItems ($selectionID = 0) {
        $items = array();
        $files_url  = UPLOAD_URL_DIR."selections/";
        $files_path = prepareDirPath($files_url);
        $query = $this->DB->Query("SELECT DISTINCT * FROM `".SELECTIONFILES_TABLE."` WHERE `active`>0 AND `selection_id`='{$selectionID}' ORDER BY `order`");
        while ($item = $this->DB->fetchAssoc()) {
            $item["title"] = unScreenData($item["title"]);
            $item["url"]   = trim($item["url"]);
            $item["image"] = (!empty($item["filename"]) and file_exists($files_path.$item["filename"])) ? $files_url.$item["filename"] : $files_url."nomage.jpg";
            $items[]       = $item;
        } return $items;
    }
}

class ProductSelections extends Selections {

}