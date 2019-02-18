<?php

/*
 * WebLife CMS
 * Developed by http://weblife.ua/
 */

/**
 * Description of IndexItem class
 *
 * @package indexer
 * @version 2018-04-10
 * @author weblife
 */
class IndexItem {
    const TYPE_DEFAULT = '-';
    /**
     * Boolean marker to update by stack table or directly to index table
     * @var bool 
     */
    private $force = false;
    /**
     * @var int 
     */
    protected $rowID = 0;
    /**
     * @var int 
     */
    protected $entityID = 0;
    /**
     * @var string 
     */
    protected $entityType = self::TYPE_DEFAULT;
    /**
     * columnMap with entityTypes
     * @var array 
     */
    protected static $entityTypes = [self::TYPE_DEFAULT];

    public static function entityTypes() {
        return array_merge(self::$entityTypes, static::$entityTypes);
    }

    public static function columnsMap() {
        $map = [];
        foreach (static::$entityTypes as $type) {
            if ($type != self::TYPE_DEFAULT) {
                $map[$type . '_id'] = $type;
            }
        }
        return $map;
    }

    public static function getInstance() {
        return new static;
    }

    public function isEmpty() {
        return (!$this->entityID || $this->entityType==self::TYPE_DEFAULT);
    }

    public function isForced() {
        return $this->force;
    }
    
    public function getForce() {
        return $this->force;
    }

    public function getID() {
        return $this->rowID;
    }

    public function getEntityID() {
        return $this->entityID;
    }

    public function getEntityType() {
        return $this->entityType;
    }

    public function setForce($force) {
        $this->force = $force;
        return $this;
    }

    public function setID($rowID) {
        $this->rowID = $rowID;
        return $this;
    }

    public function setEntityID($itemID) {
        $this->entityID = $itemID;
        return $this;
    }

    public function setEntityType($type) {
        if (in_array($type, $this::$entityTypes)) $this->entityType = $type;
        return $this;
    }
    
    public function toArray() {
        return [
            'id' => $this->rowID,
            'entity_id' => $this->entityID,
            'entity_type' => $this->entityType,
        ];
    }
}