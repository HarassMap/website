<?php

namespace Harassmap\User\Models;

use Event;
use Flash;
use Harassmap\Translate\Models\Message;
use RainLab\User\Models\User as BaseUser;

class User extends BaseUser
{

    /**
     * After login event
     * @return void
     */
    public function afterLogin()
    {
        if ($this->trashed()) {
            Flash::success(Message::trans('This account has been deleted. Please contact us for more information.'));
        } else {
            parent::afterLogin();

            Event::fire('rainlab.user.login', [$this]);
        }

    }
}