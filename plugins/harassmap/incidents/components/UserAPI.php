<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use RainLab\User\Facades\Auth;

class UserAPI extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'User API',
            'description' => ''
        ];
    }

    public function onRender()
    {
        $this->page['user'] = Auth::getUser();
    }

}
