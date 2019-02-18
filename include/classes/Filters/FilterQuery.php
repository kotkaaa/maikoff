<?php

/*
 * WebLife CMS
 * Created on 16.11.2018, 11:18:42
 * Developed by http://weblife.ua/
 */

namespace Filters;

/**
 * Description of CatalogFilters
 *
 * @author user5
 */
class FilterQuery {

    public $select = '';
    public $where = '';
    public $group = '';
    public $having = '';

    private function prepare($var, $prefix, $suffix) {
        return $var==='' ? $var : $prefix.$var.$suffix;
    }

    public function getSelect($prefix='', $suffix = PHP_EOL) {
        return $this->prepare($this->select, $prefix.'SELECT ', $suffix);
    }

    public function getWhere($prefix='', $suffix = PHP_EOL) {
        return $this->prepare($this->where, $prefix.'WHERE ', $suffix);
    }

    public function getGroup($prefix='', $suffix = PHP_EOL) {
        return $this->prepare($this->group, $prefix.'GROUP BY ', $suffix);
    }

    public function getHaving($prefix='', $suffix = PHP_EOL) {
        return $this->prepare($this->having, $prefix.'HAVING ', $suffix);
    }

}