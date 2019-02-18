<?php

namespace Sitemap;

use Sitemap\SitemapInstance;

abstract class SitemapFactory {
    /**
     *
     * @return \Sitemap\SitemapInstance
     */
    public static function getInstance () {
        return new SitemapInstance();
    }
}