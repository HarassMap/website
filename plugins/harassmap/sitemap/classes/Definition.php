<?php

namespace Harassmap\Sitemap\Classes;

use Cms\Classes\Page;
use Cms\Classes\Theme;
use DOMDocument;
use Harassmap\Incidents\Classes\EventRegistry;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Incident;
use October\Rain\Support\Traits\Singleton;
use RainLab\Pages\Classes\Page as StaticPage;
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

    /**
     * @var Domain
     */
    protected $domain;

    protected $languages = [];

    protected $defaultLocale = 'en';

    protected $translator;

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
        $this->domain = Domain::getBestMatchingDomain();
        $this->languages = explode(',', $this->domain->languages);

        // get the default locale
        $this->defaultLocale = $this->domain->default_language;
        if (!Locale::isValid($this->defaultLocale)) {
            $this->defaultLocale = 'en';
        }

        $this->translator = Translator::instance();
        if (!$this->translator->isConfigured()) {
            return;
        }

        // create the cms page links
        $this->createPageLinks();
        $this->createStaticPageLinks();
        $this->createReportLinks();

        $urlSet = $this->makeUrlSet();
        $xml = $this->makeXmlObject();
        $xml->appendChild($urlSet);

        return $xml->saveXML();
    }

    protected function createPageLinks()
    {
        $list = Page::sortBy('baseFileName')->all();

        // get every cms page
        foreach ($list as $page) {
            if (in_array($page->getBaseFileName(), $this->pages)) {
                $this->forceRouterLocale($this->defaultLocale);

                $url = Page::url($page->getBaseFileName());
                $alternate = [];

                // generate urls for all the alternate languages
                foreach ($this->languages as $language) {
                    if ($language !== $this->defaultLocale) {
                        $this->forceRouterLocale($language);
                        $alternate[$language] = Page::url($page->getBaseFileName());
                    }
                }

                $this->addItemToSet($url, $alternate, $page->mtime);
            }
        }
    }

    protected function createStaticPageLinks()
    {
        $themeActive = Theme::getActiveTheme()->getDirName();

        $list = StaticPage::listInTheme($themeActive);
        $pages = EventRegistry::instance()->removeDomainPages($list, [$this->domain->id]);

        // get every cms page
        foreach ($pages as $page) {
            $this->forceRouterLocale($this->defaultLocale);

            $url = StaticPage::url($page->page->getBaseFileName());
            $alternate = [];

            // generate urls for all the alternate languages
            foreach ($this->languages as $language) {
                if ($language !== $this->defaultLocale) {
                    $this->forceRouterLocale($language);
                    $alternate[$language] = StaticPage::url($page->page->getBaseFileName());
                }
            }

            $this->addItemToSet($url, $alternate, $page->page->mtime);
        }
    }

    protected function createReportLinks()
    {
        $incidents = Incident::where('domain_id', '=', $this->domain->id)->get();

        foreach ($incidents as $incident) {
            $this->forceRouterLocale($this->defaultLocale);

            $url = Page::url('reports/view', ['id' => $incident->public_id]);
            $alternate = [];
            foreach ($this->languages as $language) {
                if ($language !== $this->defaultLocale) {
                    $this->forceRouterLocale($language);
                    $alternate[$language] = Page::url('reports/view', ['id' => $incident->public_id]);
                }
            }

            $this->addItemToSet($url, $alternate, $incident->updated_at);
        }
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

        foreach ($languages as $code => $path) {
            $link = $url->appendChild($xml->createElement('xhtml:link'));
            $link->setAttribute('rel', 'alternate');
            $link->setAttribute('hreflang', $code);
            $link->setAttribute('href', $path);
        }

        return $url;
    }

}