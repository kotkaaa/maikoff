<?php

/*
 * WebLife CMS
 * Developed by http://weblife.ua/
 */

require_once 'IndexItem.php';

/**
 * Description of CatalogIndexItem class
 * This class extends IndexItem class functionality for catalog module
 *
 * @package indexer
 * @version 2018-04-10
 * @author weblife
 */
class CatalogIndexItem extends IndexItem {

    protected static $entityTypes = ['category', 'category_parent', 'model', 'brand', 'product', 'color', 'attribute', 'value'];

    public function setCategoryID($id) {
        return $this->setEntityType('category')->setEntityID($id);
    }

    public function setCategoryParentID($id) {
        return $this->setEntityType('category_parent')->setEntityID($id);
    }

    public function setModelID($id) {
        return $this->setEntityType('model')->setEntityID($id);
    }

    public function setBrandID($id) {
        return $this->setEntityType('brand')->setEntityID($id);
    }

    public function setProductID($id) {
        return $this->setEntityType('product')->setEntityID($id);
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
