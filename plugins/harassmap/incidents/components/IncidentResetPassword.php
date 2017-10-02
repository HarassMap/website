<?php

namespace Harassmap\Incidents\Components;

use App;
use Exception;
use RainLab\User\Components\ResetPassword;
use RainLab\User\Models\User;
use Log;

class IncidentResetPassword extends ResetPassword
{

    public function onRender()
    {
        $code = $this->code();

        if ($code) {
            $parts = explode('!', $code);

            $user = User::whereId($parts[0])->first();

            // if there is no user then error
            if (!$user || !$user->checkResetPasswordCode($parts[1])) {
                $this->page['error'] = true;
            }
        }

    }

    public function onRestorePassword()
    {
        Log::info('Requesting Password Reset');
        try {
            parent::onRestorePassword();
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

}
