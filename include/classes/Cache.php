<?php

/*
 * WebLife CMS
 * Created on 06.07.2018, 10:24:04
 * Developed by http://weblife.ua/
 */

include_once 'cache/CDummyCache.php';
include_once 'cache/CRedisCache.php';
include_once 'cache/CMemCache.php';

/**
 * @inheritdoc
 */
class DummyCacheWL extends CDummyCache {

    /**
     * @inheritdoc
     */
    public function info() {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function getFreeBytes() {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function getUsedBytes() {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function isLoaded() {
        return true;
    }
}

/**
 * @inheritdoc
 */
class RedisCacheWL extends CRedisCache {

    /**
     * @inheritdoc
     * */
    public $hashKey = false;

    /**
     * @link https://redis.io/commands/info
     * @return array information and statistics about the server
     */
    public function info() {
        $info = array();
        foreach(explode('# ', trim($this->executeCommand('INFO'))) as $section) {
            $lines = explode("\r\n", trim($section));
            $section = strtolower(trim(array_shift($lines)));
            if($section) {
                foreach($lines as $line) {
                    list($name, $value) = explode(':', trim($line));
                    if($name) {
                        $info[$name] = $value;
                    }
                }
            }
        }
        return $info;
    }

    /**
     * @inheritdoc
     */
    public function getFreeBytes() {
        $stats = $this->info();
        isset($stats["total_system_memory"]) or $stats["total_system_memory"] = $stats["used_memory_peak"];
        return (($stats && $stats["total_system_memory"] > $stats["used_memory"]) ? $stats["total_system_memory"] - $stats["used_memory"] : 0);
    }

    /**
     * @inheritdoc
     */
    public function getUsedBytes() {
        $stats = $this->info();
        return ($stats ? $stats["used_memory"] : 0);
    }

    /**
     * @inheritdoc
     */
    public function isLoaded() {
        return extension_loaded('redis');
    }

}

/**
 * @inheritdoc
 */
class MemCacheWL extends CMemCache {

    /**
     * @inheritdoc
     * */
    public $hashKey = false;

    /**
     * @link https://redis.io/commands/info
     * @return array information and statistics about the server
     */
    public function info() {
        return $this->getMemCache()->getStats();
    }

    /**
     * @inheritdoc
     */
    public function getFreeBytes() {
        $stats = $this->info();
        return (($stats && $stats["limit_maxbytes"] > $stats["bytes"]) ? $stats["limit_maxbytes"] - $stats["bytes"] : 0);
    }

    /**
     * @inheritdoc
     */
    public function getUsedBytes() {
        $stats = $this->info();
        return (($stats && $stats["bytes"]) ? $stats["bytes"] : 0);
    }

    /**
     * @inheritdoc
     */
    public function isLoaded() {
        return extension_loaded($this->useMemcached ? 'memcached' : 'memcache');
    }

}