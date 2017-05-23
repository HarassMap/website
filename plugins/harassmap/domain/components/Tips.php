<?php

namespace Harassmap\Domain\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use Harassmap\Domain\Models\Domain;
use Harassmap\Domain\Models\Tip as TipModel;

class Tips extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Tip List',
            'description' => 'Render the list of tips so far'
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
                ->orderBy('featured_from', 'desc')
                ->paginate(10);

            if ($content) {
                $found = $content;
                break;
            }
        }

        // if we have found the content block then
        if ($found) {
            $this->page['tips'] = $found;
        }
    }

}
