<?php

namespace Harassmap\Incidents\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Models\Tip as TipModel;

class UserMenu extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'User Menu',
            'description' => ''
        ];
    }

    public function onRender()
    {

    }

}
