<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use RainLab\User\Facades\Auth;

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

        // get the users incidents
        $this->page['reports'] = $user->incidents()->orderBy('created_at', 'desc')->paginate(10);
        $this->page['viewPage'] = $this->property('viewPage');
    }

}
