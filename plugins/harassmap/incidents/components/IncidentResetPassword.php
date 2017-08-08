<?php

namespace Harassmap\Incidents\Components;

use Exception;
use RainLab\User\Components\ResetPassword;
use RainLab\User\Models\User;

class IncidentResetPassword extends ResetPassword
{

    public function onRender()
    {
        $code = $this->code();

        if ($code) {
            $user = User::where('reset_password_code', '=', $code)->first();

            // if there is no user then error
            if (is_null($user)) {
                $this->page['error'] = true;
            }
        }
    }

    public function onRestorePassword()
    {
        try {
            parent::onRestorePassword();
        } catch (Exception $e) {

        }
    }

}
