<?php

namespace Harassmap\User\Classes;

use RainLab\User\Classes\AuthManager as BaseAuthManager;

class AuthManager extends BaseAuthManager
{

    protected $userModel = 'Harassmap\User\Models\User';

}