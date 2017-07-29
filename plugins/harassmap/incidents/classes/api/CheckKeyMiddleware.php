<?php

namespace Harassmap\Incidents\Classes\Api;

use Carbon\Carbon;
use Closure;
use Harassmap\Incidents\Models\API;
use Illuminate\Http\Request;
use Response;

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
            $api->last_call = new Carbon();
            $api->save();

        }


        return $response;
    }

    public static function keyError()
    {
        return Response::json(array(
            'code' => 401,
            'message' => 'You need a valid API Key'
        ), 401);
    }

}