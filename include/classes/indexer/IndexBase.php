<?php

/*
 * WebLife CMS
 * Developed by http://weblife.ua/
 */

require_once 'IndexStack.php';

/**
 * Description of IndexBase class
 * Use $product_id for record with color definition
 *
 * @package indexer
 * @version 2018-04-10
 * @author weblife
 */
abstract class IndexBase {
    protected $indexTable;
    protected $stackTable;
    protected $stackFunc;
    
    public function __construct($table) {
        $this->indexTable = $table;
        $this->stackTable = $this->indexTable . '_stack';
        $this->stackFunc = $this->stackTable . '_update';
    }
    
    public function getIndexTable() {
        return $this->indexTable;
    }

    public function getStackTable() {
        return $this->stackTable;
    }

    public function getStackFunc() {
        return $this->stackFunc;
    }

    public function init() {
        // drop all
        $this->drop();
        // create stack table 
        $this->createStackTable($this->stackTable);
        // create index table
        $this->createIndexTable($this->indexTable);
        // add stored procedures
        $this->addProcedures();
        // add triggers
        $this->addTriggers();
    }

    public function drop() {
        // drop triggers
        $this->dropTriggers();
        // drop stored procedures
        $this->dropProcedures();
        // delete stack table
        self::dropTable($this->stackTable);
        // delete index table
        self::dropTable($this->indexTable);
    }

    /**
     * Общий метод для изменений по индексной таблице
     * @param IndexItem $IndexItem По умолчанию NULL - полная переиндексация 
     * Возможные вариации:
     * NULL или IndexItem::getInstance()->setForced(true) - полная переиндексация
     * IndexItem::getInstance()->setForced(true)->setColorID(10) - обновление индексной таблицы напрямую где цвет с ID 10
     * IndexItem::getInstance() - обновление данными взятыми из стековой таблицы где applied=0
     * IndexItem::getInstance()->setColorID(10) - добавление в стековую таблицу  цвета с ID 10
     * @return int
     */
    public function update(IndexItem $IndexItem = null) {
        $affected = 0;
        // полная очистка если передан пустой параметр
        if (!$IndexItem || ($IndexItem->isEmpty() && $IndexItem->isForced())) {
            // запоминаем имена временных таблиц
            $temptable = '_temp_'.$this->indexTable;
            $oldtable = '_old_'.$this->indexTable;
            //удаляем олдовые таблицы, если есть
            self::dropTable($temptable);
            self::dropTable($oldtable);
            //создаем темповую таблицу
            $this->createIndexTable($temptable);
            // отключаем индексы
            self::disableKeys($temptable);
            // обновляем стековую таблицу
            $this->stackApply();
            // заполнение
            if($this->indexesInsert($temptable)) {
                $affected = mysql_affected_rows();
            }
            // включаем индексы
            self::enableKeys($temptable);
            // переименовываем таблицы
            $this->swapTables($temptable, $oldtable);
            // удаляем предыдущую таблицу
            self::dropTable($oldtable);
        } 
        // если передался обновляемый обьект и он должен напрямую
        else if($IndexItem->isForced()){
            if($this->indexesUpdate(IndexStack::getInstance()->addRow($IndexItem->toArray()))) {
                $affected = mysql_affected_rows();
            }
        } 
        // если передался пустой обьект - обновляем из стека
        else if($IndexItem->isEmpty()){
            $affected = $this->stackUpdate();
        } 
        // иначе пишем в stack
        else {
            if($this->stackPush($IndexItem)){
                $affected = 1;
            }
        }
        return $affected;
    }

    public function count() {
        return $this->indexesCount();
    }
    
    abstract protected function columnsMap();
    abstract protected function createStackTable($table);
    abstract protected function createIndexTable($table);
    abstract protected function createSelectQuery($conditions = '');
    abstract protected function addProcedures();
    abstract protected function addTriggers();
    abstract protected function dropProcedures();
    abstract protected function dropTriggers();
    
    /**
     * @tutorial подсчитывает количество рабочих (не удаленных) записей
     * @return int
     */
    protected function indexesCount() {
        $query  = "SELECT COUNT(`id`) FROM `{$this->indexTable}` WHERE `is_deleted`=0";
        $result = mysql_query($query) or die('Count index rows: '.mysql_error());
        return mysql_result($result, 0);
    }
    /**
     * @tutorial Создает индексы из нужных таблиц
     * @param string $table
     * @param string $conditions
     */
    protected function indexesInsert($table, $conditions = '') {
        $query = "INSERT INTO `{$table}` " . $this->createSelectQuery($conditions);
        return mysql_query($query) or die('Create products indexes: '.mysql_error());
    }
    /**
     * @tutorial Пометка о том что данные были использованы
     * @param IndexStack $Stack 
     * @return bool
     */
    protected function indexesUpdate(IndexStack $Stack) {
        // генерируем кондишини
        $arConditions = [];
        $typesMap = array_flip($this->columnsMap());
        foreach ($Stack->getTypedEntitiesIdSet() as $type => $idSet) {
            if(array_key_exists($type, $typesMap) && $typesMap[$type]){
                $arConditions[] = "t.`{$typesMap[$type]}` IN ({$idSet})";
            }
        }
        // если есть изменения
        if ($arConditions) {
            // создаем условие с WHERE ключевым словом
            $conditions = 'WHERE ('.implode(' OR ', $arConditions).')';
            //удаляем индексы
            $this->indexesDelete($conditions);
            //базовый индекс
            return $this->indexesInsert($this->indexTable, $conditions);
        }
        return false;
    }
    /**
     * @tutorial Помечает индексы как удаленные (остаются в базе но не участвуют в запросах)
     * @param string $conditions with WHERE clouse
     * @return bool
     */
    protected function indexesDelete($conditions) {
        $query = "UPDATE `{$this->indexTable}` t SET t.`is_deleted`=1 {$conditions}";
        return mysql_query($query) or die('Delete index rows: '.mysql_error());
    }
    /**
     * @tutorial Сохранение в стековую таблицу данных измененных объектов для дальнейшего индексирования. Вызывается при добавлении, редактировании и удалении объектов
     * @param IndexItem $IndexItem
     */
    protected function stackPush(IndexItem $IndexItem) {
        $query  = "CALL {$this->stackFunc} ('{$IndexItem->getEntityID()}', '{$IndexItem->getEntityType()}')";
        return mysql_query($query) or die('Save index error: '.mysql_error());
    }
    /**
     * @tutorial Создание индексов из данных стековой таблицы
     * @return int
     */
    protected function stackUpdate() {
        // блокируем таблицу
        self::lockTable($this->stackTable);
        // собираем данные
        $Stack  = new IndexStack;
        $query  = "SELECT * FROM `{$this->stackTable}` WHERE `applied`='0'";
        $result = mysql_query($query) or die('Select from index stack table: '.mysql_error());
        if ($result && mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                $Stack->addRow($row);
            }
        }
        // если есть данные то помечаем что они получены
        if($Stack->count())
            $this->stackApply($Stack);
        // разблокировываем таблицу
        self::unLockTable();
        // применяем к индексной таблице изменения
        if($Stack->count())
            $this->indexesUpdate($Stack);
        // возвращаем количество найденных елементов
        return $Stack->count();
    }
    /**
     * @tutorial Пометка о том что данные были использованы
     * @param IndexStack $Stack if null thats means all update
     * @return boolean
     */
    protected function stackApply(IndexStack $Stack = null) {
        if($Stack === null OR $Stack->count()) {
            $query = "UPDATE `{$this->stackTable}` SET `applied`='1'" . (($Stack && $Stack->count()) ? ' WHERE `id` IN('.$Stack->getIdSet().')' : '');
            mysql_query($query) or die('Update stack table: '.mysql_error());
            return true;
        } return false;
    }

    /**
     * @param string $tempTable
     * @param string $oldTable
     * @return bool
     */
    protected function swapTables($tempTable, $oldTable) {
        $query = "RENAME TABLE `{$this->indexTable}` TO `{$oldTable}`, `{$tempTable}` TO `{$this->indexTable}`";
        return mysql_query($query) or die('Rename indexes table: '.mysql_error());
    }

    public static function enableKeys($table) {
        $query = "ALTER TABLE `{$table}` ENABLE KEYS";
        return mysql_query($query) or die("Enable Keys: ".mysql_error());
    }

    public static function disableKeys($table) {
        $query = "ALTER TABLE `{$table}` DISABLE KEYS";
        return mysql_query($query) or die("Disable Keys: ".mysql_error());
    }

    public static function lockTable($table, $type = 'WRITE') {
        $query = "LOCK TABLES `{$table}` {$type}";
        return mysql_query($query) or die('Lock table: '.mysql_error());
    }

    public static function unLockTable() {
        $query = "UNLOCK TABLES";
        return mysql_query($query) or die('UnLock table: '.mysql_error());
    }

    public static function dropTable($table) {
        $query = "DROP TABLE IF EXISTS `{$table}`";
        return mysql_query($query) or die('Remove indexes table: '.mysql_error());
    }

    public static function dropTrigger($name) {
        $query = "DROP TRIGGER IF EXISTS `{$name}`";
        return mysql_query($query) or die("Error when drop trigger '{$name} from dropTrigger': ".mysql_error());
    }

    public static function dropProcedure($name){
        $query = "DROP PROCEDURE IF EXISTS `{$name}`";
        return mysql_query($query) or die("Error when drop procedure '{$name} from dropProcedure': ".mysql_error());
    }

    public static function dropFunction($name){
        $query = "DROP FUNCTION IF EXISTS `{$name}`";
        return mysql_query($query) or die("Error when drop function '{$name} from dropFunction': ".mysql_error());
    }

    /**
     * @param array $instances
     */
    final public static function addSharedProcedures(array $instances){
    }

    /**
     * @param array $instances
     */
    final public static function addSharedTriggers(array $instances){
        /**
         * ru_attributes *******************************************************
         */
        $table = ATTRIBUTES_TABLE;
        // after insert
        $trigg  = $table.'_ai';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER INSERT ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL;
        foreach($instances as $instance) 
            $query .= "    CALL `{$instance->getStackFunc()}`(NEW.`id`, 'attribute'); " . PHP_EOL;
        $query .=  "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL;
        foreach($instances as $instance) 
            $query .= "    CALL `{$instance->getStackFunc()}`(NEW.`id`, 'attribute'); " . PHP_EOL;
        $query .=  "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL;
        foreach($instances as $instance) 
            $query .= "    CALL `{$instance->getStackFunc()}`(OLD.`id`, 'attribute'); " . PHP_EOL;
        $query .=  "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * ru_attributes_values ************************************************
         */
        $table = ATTRIBUTES_VALUES_TABLE;
         // after insert 
        $trigg  = $table.'_ai';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER INSERT ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL;
        foreach($instances as $instance) 
            $query .= "    CALL `{$instance->getStackFunc()}`(NEW.`id`, 'value'); " . PHP_EOL;
        $query .=  "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`aid`!=OLD.`aid` OR " . PHP_EOL
                 . "        NEW.`title`!=OLD.`title` OR " . PHP_EOL
                 . "        NEW.`order`!=OLD.`order`) " . PHP_EOL
                 . "    THEN " . PHP_EOL;
        foreach($instances as $instance) 
            $query .= "        CALL `{$instance->getStackFunc()}`(NEW.`id`, 'value');" . PHP_EOL;
        $query .=  "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL;
        foreach($instances as $instance) 
            $query .= "    CALL `{$instance->getStackFunc()}`(OLD.`id`, 'value'); " . PHP_EOL;
        $query .=  "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * ru_colors ***********************************************************
         */
        $table = COLORS_TABLE;
        // after insert 
        $trigg  = $table.'_ai';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER INSERT ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL;
        foreach($instances as $instance) 
            $query .= "    CALL `{$instance->getStackFunc()}`(NEW.`id`, 'color'); " . PHP_EOL;
        $query .=  "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF (NEW.`title`!=OLD.`title` OR " . PHP_EOL
                 . "        NEW.`hex`!=OLD.`hex` OR " . PHP_EOL
                 . "        NEW.`order`!=OLD.`order`) " . PHP_EOL
                 . "    THEN " . PHP_EOL;
        foreach($instances as $instance) 
            $query .= "        CALL `{$instance->getStackFunc()}`(NEW.`id`, 'color'); " . PHP_EOL;
        $query .=  "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL;
        foreach($instances as $instance) 
            $query .= "    CALL `{$instance->getStackFunc()}`(OLD.`id`, 'color'); " . PHP_EOL;
        $query .=  "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        /**
         * ru_main *************************************************************
         */
        $table = MAIN_TABLE;
        // after update
        $trigg  = $table.'_au';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER UPDATE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL
                 . "    IF ((NEW.`module`='catalog' OR OLD.`module`='catalog' OR NEW.`module`='print' OR OLD.`module`='print') AND" . PHP_EOL
                 . "        NEW.`pid`!=OLD.`pid` OR " . PHP_EOL
                 . "        NEW.`title`!=OLD.`title` OR " . PHP_EOL
                 . "        NEW.`active`!=OLD.`active` OR " . PHP_EOL
                 . "        NEW.`order`!=OLD.`order`) " . PHP_EOL
                 . "    THEN " . PHP_EOL;
        foreach($instances as $instance) 
            $query .= "        CALL `{$instance->getStackFunc()}`(NEW.`id`, 'category'); " . PHP_EOL;
        $query .=  "    END IF; " . PHP_EOL
                 . "    IF (NEW.`title`!=OLD.`title`) THEN " . PHP_EOL;
        foreach($instances as $instance) 
            $query .= "        CALL `{$instance->getStackFunc()}`(NEW.`id`, 'category_parent'); " . PHP_EOL;
        $query .=  "    END IF; " . PHP_EOL
                 . "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
        // after delete
        $trigg  = $table.'_ad';
        $query  =  "CREATE TRIGGER `{$trigg}` AFTER DELETE ON `{$table}` FOR EACH ROW BEGIN " . PHP_EOL;
        foreach($instances as $instance) 
            $query .= "    CALL `{$instance->getStackFunc()}`(OLD.`id`, 'category'); " . PHP_EOL;
        $query .=  "END";
        mysql_query($query) or die("Create trigger '{$trigg}': ".mysql_error());
    }

    /**
     * @param array $instances
     */
    final public static function dropSharedProcedures(array $instances){
    }

    /**
     * @param array $instances
     */
    final public static function dropSharedTriggers(array $instances){
        /**
         * ru_attributes *******************************************************
         */
        IndexBase::dropTrigger(ATTRIBUTES_TABLE."_ai");
        IndexBase::dropTrigger(ATTRIBUTES_TABLE."_au");
        IndexBase::dropTrigger(ATTRIBUTES_TABLE."_ad");
        /**
         * ru_attributes_values ************************************************
         */
        IndexBase::dropTrigger(ATTRIBUTES_VALUES_TABLE."_ai");
        IndexBase::dropTrigger(ATTRIBUTES_VALUES_TABLE."_au");
        IndexBase::dropTrigger(ATTRIBUTES_VALUES_TABLE."_ad");
        /**
         * ru_colors ***********************************************************
         */
        IndexBase::dropTrigger(COLORS_TABLE."_ai");
        IndexBase::dropTrigger(COLORS_TABLE."_au");
        IndexBase::dropTrigger(COLORS_TABLE."_ad");
        /**
         * ru_main *************************************************************
         */
        IndexBase::dropTrigger(MAIN_TABLE."_au");
        IndexBase::dropTrigger(MAIN_TABLE."_ad");
    }
    
}
