<?php

/*
 * WebLife CMS
 * Created on 01.11.2018, 10:22:37
 * Developed by http://weblife.ua/
 */

namespace Sitemap;

/**
 * Description of SitemapXMLNode
 *
 * @author user5
 */
class SitemapXMLNode {
    //put your code here
    protected $loc;

    protected $lastmod;

    protected $changefreq;

    protected $priority;

    public function toXml () {
        return '    <url>
        <loc>' . $this->loc . '</loc>
        <priority>' . $this->priority . '</priority>
    </url>' . PHP_EOL;
    }

    public function setLoc($loc) {
        $this->loc = $loc;
    }

    public function getLoc() {
        return $this->loc;
    }

    public function setLastMod($lastmod) {
        $this->lastmod = $lastmod;
    }

    public function getLastMod() {
        return $this->lastmod;
    }

    public function setChangeFreq($changefreq) {
        $this->changefreq = $changefreq;
    }

    public function getChangeFreq() {
        return $this->changefreq;
    }

    public function setPriority($priority) {
        $this->priority = $priority;
    }

    public function getPriority() {
        return $this->priority;
    }
}
