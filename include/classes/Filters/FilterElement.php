<?php

/*
 * WebLife CMS
 * Created on 16.11.2018, 11:18:26
 * Developed by http://weblife.ua/
 */

/**
 * Description of FilterElement
 *
 * @author user5
 */
namespace Filters;

class FilterElement {

    private $filterID;
    private $typeID;
    private $valueID;
    private $involved;
    private $hidden;
    private $total;
    private $isRagne;
    private $parent;

    public function __construct($filterID, $typeID, FilterElement $parent = null) {
        $this->valueID = $this->involved = $this->hidden = $this->total = 0;
        $this->filterID = $filterID;
        $this->typeID = $typeID;
        $this->parent = $parent;
    }

    public function getFilterID() {
        return $this->filterID;
    }

    public function getTypeID() {
        return $this->typeID;
    }

    public function getValueID() {
        return $this->valueID;
    }

    public function getInvolved() {
        return $this->involved;
    }

    public function getHidden() {
        return $this->hidden;
    }

    public function getTotal() {
        return $this->total;
    }

    public function getParent() {
        return $this->parent;
    }

    public function isRagne() {
        return $this->isRagne;
    }

    public function change($valueID, $isRagne = false) {
        $this->valueID = $valueID;
        $this->isRagne = $isRagne;
        return $this;
    }

    public function incrementInvolved() {
        $this->involved++;
        return $this;
    }

    public function decrementInvolved() {
        $this->involved--;
        return $this;
    }

    public function incrementHidden() {
        $this->hidden++;
        return $this;
    }

    public function decrementHidden() {
        $this->hidden--;
        return $this;
    }

    public function incrementTotal() {
        $this->total++;
        return $this;
    }

    public function decrementTotal() {
        $this->total--;
        return $this;
    }
}
