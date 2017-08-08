<?php

namespace Harassmap\Incidents\Components;

use Exception;
use RainLab\User\Components\ResetPassword;

class IncidentResetPassword extends ResetPassword
{

    public function onRestorePassword()
    {
        try {
            parent::onRestorePassword();
        } catch (Exception $e) {

        }
    }

}
