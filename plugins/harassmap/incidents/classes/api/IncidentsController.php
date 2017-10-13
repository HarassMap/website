<?php

namespace Harassmap\Incidents\Classes\Api;

use Carbon\Carbon;
use Exception;
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

        try {
            $this->filterBounds($incidents);
            $this->filterSince($incidents);
            $this->filterBefore($incidents);
        } catch (Exception $e) {
            return self::error($e->getMessage());
        }


        return $this->apiResponse($incidents);
    }

    protected function filterBounds($query)
    {
        $bounds = get('bounds');

        // if we have a bounds parameter then get incidents inside bounds
        if ($bounds) {
            $bounds = explode(',', $bounds);

            if (count($bounds) !== 2) {
                throw self::boundsError();
            }

            $lat = explode('-', $bounds[0]);
            $lng = explode('-', $bounds[1]);

            if (count($lat) !== 2 || count($lng) !== 2) {
                throw self::boundsError();
            }

            Incident::filterBounds($query, $lat, $lng);
        }
    }

    protected function filterSince($query)
    {
        $this->filterDate($query, get('since'), '>');
    }

    protected function filterBefore($query)
    {
        $this->filterDate($query, get('before'), '<');
    }

    protected function filterDate($query, $time, $operator)
    {
        if ($time) {

            // try and create a date if we cant then throw the date error
            try {
                $date = new Carbon($time);
            } catch (Exception $e) {
                throw self::dateError();
            }

            $query->where('date', $operator, $date);
        }
    }

    public static function boundsError()
    {
        return new Exception("You have sent a malformed bounds parameter");
    }

    public static function dateError()
    {
        return new Exception("The date you have sent us is not formed properly.");
    }

}