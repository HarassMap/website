<?php

namespace Harassmap\Incidents\Classes\Api;

use Harassmap\Incidents\Models\Incident;
use Illuminate\Http\Request;

class IncidentsController extends BaseController
{

    public function index(Request $request)
    {
        // get the base incident list
        $incidents = Incident
            ::orderBy('date', 'desc')
            ->with('location')->with('intervention')->with('role')->with('categories');

        $this->filterBounds($incidents);

        return $this->apiResponse($incidents);
    }

    protected function filterBounds($query)
    {
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

            Incident::filterBounds($query, $lat, $lng);
        }
    }

    public static function boundsError()
    {
        return self::error('You have sent a malformed bounds parameter');
    }

}