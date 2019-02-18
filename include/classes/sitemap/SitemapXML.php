<?php

/*
 * WebLife CMS
 * Created on 01.11.2018, 10:22:17
 * Developed by http://weblife.ua/
 */

namespace Sitemap;

use Sitemap\SitemapXMLNode;

interface SitemapXMLInterface {

    public function header();

    public function nodes();

    public function footer();

    public function write();

    public function open();

    public function close();

    public function flush();
}

/**
 * Description of SitemapXML
 *
 * @author user5
 */
class SitemapXML implements SitemapXMLInterface {

    private $fp;
    private $code;

    protected $filename;
    protected $items;
    protected $base;

    public function setBase ($base) {
        $this->base = $base;
    }

    public function getBase () {
        return $this->base;
    }

    public function setFilename ($filename) {
        $this->filename = $filename;
    }

    public function getFilename () {
        return $this->filename;
    }

    public function addItems ($items = []) {
        $this->items = $items;
    }

    public function header () {
        $this->code .= '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $this->code .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;
        $this->code .= '<!-- Last update of sitemap ' . date('c') . ' -->' . PHP_EOL;
    }
    
    public function footer () {
        $this->code .= '</urlset>';
    }
    
    public function nodes () {
        $i = 0;
        $node = new SitemapXMLNode();
        do {
            if (!isset($this->items[$i]) or is_null($this->items[$i])) continue;
            $node->setLoc($this->base.$this->items[$i]->loc);
            $node->setPriority($this->items[$i]->priority);
            $this->code .= $node->toXml();
            unset($this->items[$i]);
            $i++;
        } while (!empty($this->items));
        unset($node);
    }

    public function write () {
        // open file
        $this->open();
        // add header
        $this->header();
        // add nodes
        $this->nodes();
        // add footer
        $this->footer();
        // write file
        @fwrite($this->fp, $this->code);
        // close file & flush data
        $this->close();
    }
    
    public function index (array $maps) {
        // open file
        $this->open();
        // add header
        $this->code .= '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $this->code .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        // add nodes
        foreach ($maps as $map) {
            $this->code .= '    <sitemap>' . PHP_EOL;
            $this->code .= '        <loc>' . $map . '</loc>' . PHP_EOL;
            $this->code .= '        <lastmod>' . date('c') . '</lastmod>' . PHP_EOL;
            $this->code .= '    </sitemap>' . PHP_EOL;
        }
        // add footer
        $this->code .= '</sitemapindex>';
        // write file
        @fwrite($this->fp, $this->code);
        // close file & flush data
        $this->close();
    }

    public function open () {
        try {
            $this->fp = @fopen($this->filename, 'w+');
        } catch (\Exception $ex) {
            print nl2br($ex->getMessage());
        }
    }

    public function close () {
        fclose($this->fp);
        $this->flush();
    }

    public function flush() {
        $this->code = null;
        $this->fp   = null;
    }
}
