<?php

/**
 * Description of Pager class
 * This class provides methods for create and manage pages
 * @author WebLife
 * @copyright 2015
 */
class Pager {

    /**
     * @var UrlWL
     */
    protected $UrlWL;

    /**
     * @var int
     */
    protected $first;

    /**
     * @var int
     */
    protected $last;

    /**
     * @var int
     */
    protected $prev;

    /**
     * @var int
     */
    protected $next;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var array
     */
    protected $pages;

    /**
     * @var UrlWL for url generate
     */
    private $_UrlWL;

    /**
     *
     * @var bool
     */
    private $incorrectPage;

    const PAGES_SEP = '...';

    /**
     * @param UrlWL $UrlWL
     * @param int $current
     * @param int $total
     * @param int $limit
     * @param mixed $separator | string or false
     */
    public function __construct(UrlWL $UrlWL, $current, $total, $limit, $separator=self::PAGES_SEP) {
        $this->UrlWL = $UrlWL;
        $this->count = 0;
        $this->pages = array();
        $this->prev = $this->next = $this->first = $this->last = 1;
        $this->calc($current, $total, $limit, $separator);
        $this->UrlWL->setPage($current);
    }
    /**
     *
     * @param type $current
     * @param int $total
     * @param type $limit
     * @param type $separator
     * @return \Pager
     */
    private function calc(&$current, $total, $limit, $separator=false){
        if(!$total) $total = 1;
        $this->count = $this->last = intval(ceil($total / $limit));
        $this->incorretPage = ($current > $this->count);
        if($current > $this->count) {
            $current = $this->count;
        }
        if ($this->count > 1) {
            $this->prev = ($current > 1)            ? $current-1 : 1;
            $this->next = ($current < $this->count) ? $current+1 : $this->count;
            $this->pages[] = 1;
            if ($this->count <= 5 || $separator === false) {
                for($i = 2; $i < $this->count; $i++){
                    $this->pages[] = $i;
                }
            } else if($current <= 3) {
                for($i = 2; $i <= 5; $i++){
                    $this->pages[] = $i;
                }
                $this->pages[] = $separator;
            } else {
                $start = $this->count- ($this->count - $current + 2);
                if($current == $this->count){
                    $start -= 2;
                }
                if($current == $this->count - 1){
                    $start--;
                }
                if($start < 0) {
                    $start = 0;
                }
                $end = $this->count - ($this->count - $current-2);
                if( $end > $this->count){
                    $end=$this->count;
                }
                if($current > 2){
                    $this->pages[] = $separator;
                }
                for($i = 1+$start; $i < $end; $i++){
                    $this->pages[] = $i;
                }
                if($current < $this->count-2){
                    $this->pages[] = $separator;
                }
            } $this->pages[] = $this->count;
        } return $this;
    }

    public function isIncorrectPage() {
        return $this->incorrectPage;
    }

    /**
     * @param int $page
     * @return string
     */
    public function getUrl($page){
        if($this->_UrlWL===null){
            $this->_UrlWL = $this->UrlWL->copy();
        }
        $this->_UrlWL->setPath($this->UrlWL->getPath());
        if($page == UrlWL::PAGES_ALL_VAL){
            $page = 1;
            $this->_UrlWL->setParam(UrlWL::PAGES_KEY_NAME, UrlWL::PAGES_ALL_VAL);
        } else {
            $this->_UrlWL->unsetParam(UrlWL::PAGES_KEY_NAME);
        }
        $this->_UrlWL->setPage($page);
        return $this->_UrlWL->buildUrl();
    }
    /**
     * @return string
     */
    public function getAllUrl() {
        return $this->getUrl(UrlWL::PAGES_ALL_VAL);
    }
    /**
     * @return int
     */
    public function getFirst() {
        return $this->first;
    }
    /**
     * @return int
     */
    public function getLast() {
        return $this->last;
    }
    /**
     * @return int
     */
    public function getPrev() {
        return $this->prev;
    }
    /**
     * @return int
     */
    public function getNext() {
        return $this->next;
    }
    /**
     * @return int
     */
    public function getCount() {
        return $this->count;
    }
    /**
     * @return array
     */
    public function getPages() {
        return $this->pages;
    }
    /**
     * @return string
     */
    public function getSeparator() {
        return self::PAGES_SEP;
    }
}
