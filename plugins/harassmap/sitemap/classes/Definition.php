<?php

namespace Harassmap\Sitemap\Classes;

use Harassmap\Incidents\Models\Domain;
use October\Rain\Support\Traits\Singleton;
use DOMDocument;

class Definition
{
    use Singleton;

    /**
     * @var DOMDocument element
     */
    protected $urlSet;

    /**
     * @var DOMDocument
     */
    protected $xmlObject;

    public function generateSitemap()
    {
        $domain = Domain::getBestMatchingDomain();


        $urlSet = $this->makeUrlSet();
        $xml = $this->makeXmlObject();
        $xml->appendChild($urlSet);

        return $xml->saveXML();
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
        $urlSet->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $urlSet->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

        return $this->urlSet = $urlSet;
    }

}