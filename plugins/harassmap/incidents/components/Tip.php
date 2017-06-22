<?php

namespace Harassmap\Incidents\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Tip as TipModel;

class Tip extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Weekly Tip',
            'description' => 'Render the current weekly tip'
        ];
    }

    public function defineProperties()
    {
        return [
            'listPage' => [
                'title' => 'harassmap.domain::lang.tip.list.page_name',
                'description' => 'harassmap.domain::lang.tip.list.page_help',
                'type' => 'dropdown',
                'group' => 'Links',
            ],
        ];
    }

    public function getListPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRender()
    {
        $domains = Domain::getMatchingDomains();
        $found = false;

        foreach ($domains as $domain) {
            $content = TipModel
                ::where('domain_id', '=', $domain->id)
                ->where('featured_from', '<', Carbon::now())
                ->orderBy('featured_from', 'desc')
                ->first();

            if ($content) {
                $found = $content;
                break;
            }
        }

        // if we have found the content block then
        if ($found) {
            $this->page['tip'] = $found->tip;
            $this->page['listPage'] = $this->property('listPage');
        }
    }

}
