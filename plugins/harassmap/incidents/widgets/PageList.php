<?php

namespace Harassmap\Incidents\Widgets;

use Input;
use Harassmap\Incidents\Classes\EventRegistry;
use Harassmap\Incidents\Classes\PageList as StaticPageList;
use RainLab\Pages\Widgets\PageList as BasePageList;
use Str;

class PageList extends BasePageList
{

    public function onReorder()
    {
        $structure = json_decode(Input::get('structure'), true);

        if (!$structure) {
            throw new SystemException('Invalid structure data posted.');
        }

        $pageList = new StaticPageList($this->theme);
        $pageList->updateStructurePart($structure);
    }

    protected function getData()
    {
        $pageList = new StaticPageList($this->theme);
        $pages = $pageList->getPageTree(true);

        $searchTerm = Str::lower($this->getSearchTerm());

        if (strlen($searchTerm)) {
            $words = explode(' ', $searchTerm);

            $iterator = function ($pages) use (&$iterator, $words) {
                $result = [];

                foreach ($pages as $page) {
                    if ($this->textMatchesSearch($words, $this->subtreeToText($page))) {
                        $result[] = (object)[
                            'page' => $page->page,
                            'subpages' => $iterator($page->subpages)
                        ];
                    }
                }

                return $result;
            };

            $pages = $iterator($pages);
        }

        $pages = EventRegistry::instance()->pruneDomainPages($pages);

        return $pages;
    }

}