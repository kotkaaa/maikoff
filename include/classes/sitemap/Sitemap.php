<?php

/*
 * WebLife CMS
 * Created on 01.11.2018, 10:19:11
 * Developed by http://weblife.ua/
 */

namespace Sitemap;

require dirname(__FILE__).DS."SitemapFactory.php";
require dirname(__FILE__).DS."SitemapDataProvider.php";
require dirname(__FILE__).DS."SitemapXML.php";
require dirname(__FILE__).DS."SitemapXMLNode.php";

use Sitemap\SitemapXML,
    Sitemap\DataProvider;

/**
 * Description of Sitemap
 *
 * @author user5
 */
interface Sitemap {

    public function xml ();

    public function export (DataProvider $DataProvider);

    public function index (array $maps);
}

class SitemapInstance implements Sitemap {

    protected $XML;

    public function __construct () {
        $this->XML = new SitemapXML();
    }

    public function xml () {
        return $this->XML;
    }

    public function export(DataProvider $DataProvider) {
        $this->XML->addItems($DataProvider->get());
        $DataProvider->free();
        $this->XML->write();
    }

    public function index(array $maps) {
        $this->XML->index($maps);
    }
}