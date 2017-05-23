<?php

namespace Harassmap\Domain\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use Harassmap\Domain\Models\Tip as TipModel;
use Harassmap\Domain\Models\Domain;

class Tip extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Weekly Tip',
            'description' => 'Render the current weekly tip'
        ];
    }

    public function onRender()
    {
        $domains = Domain::getMatchingDomains();
        $found = false;

        foreach ($domains as $domain) {
            $content = TipModel
                ::where('domain_id', '=', $domain->id)
                ->where('featured_from', '<', Carbon::now())
                ->orderBy('featured_from')
                ->first();

            if ($content) {
                $found = $content;
                break;
            }
        }

        // if we have found the content block then
        if ($found) {
            $this->page['tip'] = $found->tip;
        }
    }

}
