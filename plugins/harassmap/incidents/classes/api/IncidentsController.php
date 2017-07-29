<?php

namespace Harassmap\Incidents\Classes\Api;

use Harassmap\Incidents\Models\Incident;
use Illuminate\Http\Request;

class IncidentsController extends BaseController
{

    public function index(Request $request)
    {
        // get the per page with maximum value of 100
        $perPage = min(get('perPage', 10), 100);

        // get the base incident list
        $incidents = Incident
            ::orderBy('date', 'desc')
            ->with('location')->with('intervention')->with('role')->with('categories');

        $bounds = get('bounds');

        // if we have a bounds parameter then get incidents inside bounds
        if ($bounds) {
            $bounds = explode(',', $bounds);

            if (count($bounds) !== 2) {
                return self::boundsError();
            }

            $lat = explode('-', $bounds[0]);
            $lng = explode('-', $bounds[1]);

            if (count($lat) !== 2 || count($lng) !== 2) {
                return self::boundsError();
            }

            $incidents->whereHas('location', function ($query) use ($lat, $lng) {
                $query
                    ->whereBetween('lat', [floatval($lat[0]), floatval($lat[1])])
                    ->whereBetween('lng', [floatval($lng[0]), floatval($lng[1])]);
            });
        }

        // get the paginated results
        $results = $incidents
            ->paginate($perPage)
            ->all();

        return response()->json($results);
    }

    public static function boundsError()
    {
        return self::error('You have sent a malformed bounds parameter');
    }

}