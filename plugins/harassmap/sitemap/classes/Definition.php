<?php

namespace Harassmap\Sitemap\Classes;

use Cms\Classes\Page;
use DOMDocument;
use Harassmap\Incidents\Models\Domain;
use October\Rain\Support\Traits\Singleton;
use RainLab\Translate\Classes\Translator;
use RainLab\Translate\Models\Locale;
use Route;

class Definition
{
    use Singleton;

    /**
     * Maximum URLs allowed (Protocol limit is 50k)
     */
    const MAX_URLS = 50000;

    /**
     * Maximum generated URLs per type
     */
    const MAX_GENERATED = 10000;

    /**
     * @var integer A tally of URLs added to the sitemap
     */
    protected $urlCount = 0;

    /**
     * @var DOMDocument element
     */
    protected $urlSet;

    /**
     * @var DOMDocument
     */
    protected $xmlObject;

    protected $pages = [
        'home',
        'contact',
        'report/incidents',
        'report/intervention',
        'chart/index',
        'news/index',
        'reports/index',
        'tips/index',
    ];

    public function generateSitemap()
    {
        $domain = Domain::getBestMatchingDomain();
        $languages = explode(',', $domain->languages);

        // get the default locale
        $defaultLocale = $domain->default_language;
        if (!Locale::isValid($defaultLocale)) {
            $defaultLocale = 'en';
        }

        $translator = Translator::instance();
        if (!$translator->isConfigured()) {
            return;
        }

        $list = Page::sortBy('baseFileName')->all();

        // get every cms page
        foreach ($list as $page) {
            if (in_array($page->getBaseFileName(), $this->pages)) {

                $this->forceRouterLocale($defaultLocale);

                $url = Page::url($page->getBaseFileName());
                $alternate = [];

                // generate urls for all the alternate languages
                foreach ($languages as $language) {
                    if($language !== $defaultLocale) {
                        $this->forceRouterLocale($language);
                        $alternate[$language] = Page::url($page->getBaseFileName());
                    }
                }

                $this->addItemToSet($url, $alternate, $page->mtime);
            }
        }

        $urlSet = $this->makeUrlSet();
        $xml = $this->makeXmlObject();
        $xml->appendChild($urlSet);

        return $xml->saveXML();
    }

    protected function forceRouterLocale($locale)
    {
        Route::group(['prefix' => $locale], function () {
            Route::any('{slug}', 'Cms\Classes\CmsController@run')->where('slug', '(.*)?');
        });
    }

    protected function makeXmlObject()
    {
        if ($this->xmlObject !== null) {
            return $this->xmlObject;
        }

        $xml = new DOMDocument;
        $xml->encoding = 'UTF-8';

        return $this->xmlObject = $xml;
    }

    protected function makeUrlSet()
    {
        if ($this->urlSet !== null) {
            return $this->urlSet;
        }

        $xml = $this->makeXmlObject();
        $urlSet = $xml->createElement('urlset');
        $urlSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlSet->setAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');

        return $this->urlSet = $urlSet;
    }

    protected function addItemToSet($url, $languages = [], $mtime = null, $changefreq = 'weekly', $priority = 0.5)
    {
        if ($mtime instanceof \DateTime) {
            $mtime = $mtime->getTimestamp();
        }

        $xml = $this->makeXmlObject();
        $urlSet = $this->makeUrlSet();
        $mtime = $mtime ? date('c', $mtime) : date('c');

        $urlElement = $this->makeUrlElement(
            $xml,
            $url,
            $languages,
            $mtime,
            $changefreq,
            $priority
        );

        if ($urlElement) {
            $urlSet->appendChild($urlElement);
        }

        return $urlSet;
    }

    protected function makeUrlElement($xml, $pageUrl, $languages, $lastModified, $frequency, $priority)
    {
        if ($this->urlCount >= self::MAX_URLS) {
            return false;
        }

        $this->urlCount++;

        $url = $xml->createElement('url');
        $url->appendChild($xml->createElement('loc', $pageUrl));
        $url->appendChild($xml->createElement('lastmod', $lastModified));
        $url->appendChild($xml->createElement('changefreq', $frequency));
        $url->appendChild($xml->createElement('priority', $priority));

        foreach($languages as $code => $path) {
            $link = $url->appendChild($xml->createElement('xhtml:link'));
            $link->setAttribute('rel', 'alternate');
            $link->setAttribute('hreflang', $code);
            $link->setAttribute('href', $path);
        }

        return $url;
    }

}