<?php

namespace Harassmap\Incidents\Classes\Api;

use Carbon\Carbon;
use Closure;
use Harassmap\Incidents\Models\API;
use Harassmap\Incidents\Models\Settings;
use Illuminate\Http\Request;

class CheckKeyMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        $key = $request->get('key');

        if (!$key) {
            return self::keyError();
        }

        $api = API::whereKey($key)->first();

        if (!$api) {
            return self::keyError();
        }

        // get the day limit
        $day_limit = Settings::get('api_day_limit');



        // get the user from the API
        $user = $api->user;

        // add the user to the request
        $request->attributes->add([
            'user' => $user
        ]);

        // pass the request back to the controller
        $response = $next($request);

        // if the response is ok
        if ($response->isOk()) {

            // increase the total
            $api->total++;
            $api->calls++;

            // if the last call was yesterday then reset the calls
            if (strtotime($api->last_call) < strtotime(date('Y-m-d'))) {
                $api->calls = 1;
            }

            $api->last_call = new Carbon();
            $api->save();

        }

        return $response;
    }

    public static function keyError()
    {
        return response()->json(array(
            'code' => 401,
            'message' => 'You need a valid API Key'
        ), 401);
    }

}