<?php

namespace Harassmap\Incidents\Classes;

use Harassmap\Incidents\Models\Domain;
use Lang;
use Cache;
use Config;
use Cms\Classes\Theme;
use RainLab\Pages\Classes\Page;
use October\Rain\Support\Str;
use October\Rain\Router\Helper as RouterHelper;
use RainLab\Pages\Classes\PageList;

/**
 * A router for static pages.
 *
 * @package rainlab\pages
 * @author Alexey Bobkov, Samuel Georges
 */
class Router
{
    /**
     * @var \Cms\Classes\Theme A reference to the CMS theme containing the object.
     */
    protected $theme;

    /**
     * @var Domain
     */
    protected $domain;

    /**
     * @var array Contains the URL map - the list of page file names and corresponding URL patterns.
     */
    private static $urlMap = [];

    /**
     * @var array Request-level cache
     */
    private static $cache = [];

    /**
     * Creates the router instance.
     * @param \Cms\Classes\Theme $theme Specifies the theme being processed.
     * @param Domain $domain
     */
    public function __construct(Theme $theme, Domain $domain)
    {
        $this->theme = $theme;
        $this->domain = $domain;
    }

    /**
     * Finds a static page by its URL.
     * @param string $url The requested URL string.
     * @return \RainLab\Pages\Classes\Page Returns \RainLab\Pages\Classes\Page object or null if the page cannot be found.
     */
    public function findByUrl($url)
    {
        $url = Str::lower(RouterHelper::normalizeUrl($url));

        if (array_key_exists($url, self::$cache)) {
            return self::$cache[$url];
        }

        $urlMap = $this->getUrlMap();
        $urlMap = array_key_exists('urls', $urlMap) ? $urlMap['urls'] : [];

        if (!array_key_exists($url, $urlMap)) {
            return null;
        }

        $fileName = $urlMap[$url];

        if (($page = Page::loadCached($this->theme, $fileName)) === null) {
            /*
             * If the page was not found on the disk, clear the URL cache
             * and try again.
             */
            $this->clearCache();

            return self::$cache[$url] = Page::loadCached($this->theme, $fileName);
        }

        return self::$cache[$url] = $page;
    }

    /**
     * Autoloads the URL map only allowing a single execution.
     * @return array Returns the URL map.
     */
    protected function getUrlMap()
    {
        if (!count(self::$urlMap)) {
            $this->loadUrlMap();
        }

        return self::$urlMap;
    }

    /**
     * Loads the URL map - a list of page file names and corresponding URL patterns.
     * The URL map can is cached. The clearUrlMap() method resets the cache. By default
     * the map is updated every time when a page is saved in the back-end, or
     * when the interval defined with the cms.urlCacheTtl expires.
     * @return boolean Returns true if the URL map was loaded from the cache. Otherwise returns false.
     */
    protected function loadUrlMap()
    {
        $key = $this->getCacheKey('static-page-url-map');

        $cacheable = Config::get('cms.enableRoutesCache');
        $cached = $cacheable ? Cache::get($key, false) : false;

        if (!$cached || ($unserialized = @unserialize($cached)) === false) {

            /*
             * The item doesn't exist in the cache, create the map
             */
            $pageList = new PageList($this->theme);

            $pages = $pageList->listPages();

            $map = [
                'urls'   => [],
                'files'  => [],
                'titles' => []
            ];
            foreach ($pages as $page) {
                $url = $page->getViewBag()->property('url');
                $domain = $page->getViewBag()->property('domain');

                if (!$url || intval($domain) !== $this->domain->id) {
                    continue;
                }

                $url = Str::lower(RouterHelper::normalizeUrl($url));
                $file = $page->getBaseFileName();

                $map['urls'][$url] = $file;
                $map['files'][$file] = $url;
                $map['titles'][$file] = $page->getViewBag()->property('title');
            }

            self::$urlMap = $map;

            if ($cacheable) {
                Cache::put($key, serialize($map), Config::get('cms.urlCacheTtl', 1));
            }

            return false;
        }

        self::$urlMap = $unserialized;

        return true;
    }

    /**
     * Returns the caching URL key depending on the theme.
     * @param string $keyName Specifies the base key name.
     * @return string Returns the theme-specific key name.
     */
    protected function getCacheKey($keyName)
    {
        return crc32($this->theme->getPath()).$keyName.Lang::getLocale().$this->domain->id;
    }

    /**
     * Clears the router cache.
     */
    public function clearCache()
    {
        Cache::forget($this->getCacheKey('static-page-url-map'));
    }
}
