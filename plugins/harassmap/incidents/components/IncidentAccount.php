<?php

namespace Harassmap\Incidents\Components;

use Exception;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Translate\Models\Message;
use RainLab\User\Components\Account;
use RainLab\User\Facades\Auth;
use Redirect;

class IncidentAccount extends Account
{

    public function componentDetails()
    {
        return [
            'name' => 'Incident Account',
            'description' => 'Account component for registering users with incidents'
        ];
    }

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        /*
         * Redirect to HTTPS checker
         */
        if ($redirect = $this->redirectForceSecure()) {
            return $redirect;
        }

        /*
         * Activation code supplied
         */
        $routeParameter = $this->property('paramCode');

        if ($activationCode = $this->param($routeParameter)) {
            $this->onActivate($activationCode);

            return Redirect::to($this->pageUrl('user/dashboard'));
        }

        $this->page['user'] = $this->user();
        $this->page['loginAttribute'] = $this->loginAttribute();
        $this->page['loginAttributeLabel'] = $this->loginAttributeLabel();
    }

    public function onAccountUpdate()
    {

        if (!$user = $this->user()) {
            return;
        }

        // switch the username
        if (array_key_exists('account_username', $_POST)) {
            $_POST['username'] = $_POST['account_username'];
            unset($_POST['account_username']);
        }

        // switch the username
        if (array_key_exists('account_email', $_POST)) {
            $_POST['email'] = $_POST['account_email'];
            unset($_POST['account_email']);
        }

        return $this->onUpdate();
    }

    public function onIncidentRegister()
    {

        // register the user
        $result = $this->onRegister();

        // attach the user to the incident
        $this->attachIncidentToUser();

        // return the actual result
        return $result;
    }

    public function onIncidentSignin()
    {

        // register the user
        $result = $this->onSignin();

        // attach the user to the incident
        $this->attachIncidentToUser();

        // return the actual result
        return $result;
    }

    protected function attachIncidentToUser()
    {
        // attach the user to the incident
        $id = $this->param('id');
        $user = Auth::getUser();

        // get the incident and save the user to it
        $incident = Incident::wherePublicId($id)->first();
        $incident->user_id = $user->id;
        $incident->save();
    }

}
