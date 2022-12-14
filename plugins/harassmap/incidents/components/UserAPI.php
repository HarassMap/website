<?php

namespace Harassmap\Incidents\Components;

use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Models\API;
use Harassmap\Incidents\Models\Settings;
use RainLab\User\Facades\Auth;

class UserAPI extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'User API',
            'description' => 'Allows the user to generate an API key and display their API information.'
        ];
    }

    public function onRender()
    {
        $user = Auth::getUser();

        $this->page['user'] = $user;
        $this->page['api'] = $user->api;
        $this->page['limit'] = Settings::get('api_day_limit');
    }

    public function onGenerateKey()
    {
        // get the current user
        $user = Auth::getUser();

        // generate a key
        $key = bin2hex(random_bytes(16));

        // make sure the user doesn't already have an api key
        if(!$user->api) {

            // create a new API model for this user
            $api = new API();
            $api->key = $key;
            $api->user_id = $user->id;

            // save the api key to the database
            $api->save();
        } else {
            $api = $user->api;
            $api->key = $key;
            $api->save();
        }

        $this->page['user'] = $user;
        $this->page['api'] = $api;
        $this->page['limit'] = Settings::get('api_day_limit');

    }

}
