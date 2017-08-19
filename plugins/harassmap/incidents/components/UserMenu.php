<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Models\Notification;
use RainLab\User\Facades\Auth;

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
        $user = Auth::getUser();

        $this->page['notifications'] = Notification
            ::where('user_id', '=', $user->id)
            ->where('read', '=', false)
            ->count();
    }

}
