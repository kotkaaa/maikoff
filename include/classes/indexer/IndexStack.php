<?php

/*
 * WebLife CMS
 * Developed by http://weblife.ua/
 */

/**
 * Description of IndexStack class
 *
 * @package indexer
 * @version 2018-04-10
 * @author weblife
 */
class IndexStack {

    private $count = 0;
    private $entities = [];
    private $typedSet = [];

    public function getTable() {
        return $this->table;
    }

    public function getFunction() {
        return $this->func;
    }

    public function count() {
        return $this->count;
    }

    public function getEntities() {
        return $this->entities;
    }

    public function getIdSet() {
        return implode(',', array_keys($this->entities));
    }

    public function getTypedEntitiesIdSet() {
        $sets = [];
        foreach ($this->typedSet as $type => $arr) {
            $sets[$type] = implode(',', $arr);
        }
        return $sets;
    }

    public function addRow(array $row) {
        if ($row['id']) {
            $this->count++;
            $this->entities[$row['id']] = [
                'entity_type' => $row['entity_type'],
                'entity_id' => $row['entity_id'],
            ];
            $this->typedSet[$row['entity_type']][] = $row['entity_id'];
        }
    }

    public static function getInstance() {
        return new self;
    }

}
