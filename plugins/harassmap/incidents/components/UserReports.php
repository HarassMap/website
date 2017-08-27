<?php

namespace Harassmap\Incidents\Components;

use App;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Incident;
use RainLab\User\Facades\Auth;
use Harassmap\Incidents\Models\Domain;

class UserReports extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'User Report Table',
            'description' => 'Shows a table of the users reports'
        ];
    }

    public function defineProperties()
    {
        return [
            'viewPage' => [
                'title' => 'harassmap.incidents::lang.tip.list.page_name',
                'description' => 'harassmap.incidents::lang.tip.list.page_help',
                'type' => 'dropdown',
                'group' => 'Links',
            ],
        ];
    }

    public function getViewPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRender()
    {
        $user = Auth::getUser();

        if (!$user) {
            App::abort(404);
        }

        $domain = Domain::getBestMatchingDomain();

        // get the users incidents
        $this->page['reports'] = Incident
            ::where('domain_id', '=', $domain->id)
            ->where('user_id', '=', $user->id)
            ->orderBy('created_at', 'desc')->paginate(10);

        $this->page['viewPage'] = $this->property('viewPage');
    }

}
