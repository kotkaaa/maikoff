<?php

/*
 * WebLife CMS
 * Developed by http://weblife.ua/
 */

require_once 'IndexItem.php';

/**
 * Description of PrintIndexItem class
 * This class extends IndexItem class functionality for print module
 *
 * @package indexer
 * @version 2018-04-10
 * @author weblife
 */
class PrintIndexItem extends IndexItem {

    protected static $entityTypes = ['substrate', 'category', 'category_parent', 'print', 'shortcut', 'logo', 'assortment', 'color', 'attribute', 'value'];

    public function setSubstrateID($id) {
        return $this->setEntityType('substrate')->setEntityID($id);
    }

    public function setCategoryID($id) {
        return $this->setEntityType('category')->setEntityID($id);
    }

    public function setCategoryParentID($id) {
        return $this->setEntityType('category_parent')->setEntityID($id);
    }

    public function setPrintID($id) {
        return $this->setEntityType('print')->setEntityID($id);
    }

    public function setShortcutID($id) {
        return $this->setEntityType('shortcut')->setEntityID($id);
    }

    public function setLogoID($id) {
        return $this->setEntityType('logo')->setEntityID($id);
    }

    public function setColorID($id) {
        return $this->setEntityType('color')->setEntityID($id);
    }

    public function setAttrID($id) {
        return $this->setEntityType('attribute')->setEntityID($id);
    }

    public function setValueID($id) {
        return $this->setEntityType('value')->setEntityID($id);
    }
}