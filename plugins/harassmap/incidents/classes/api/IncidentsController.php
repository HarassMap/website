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

        $incidents = Incident
            ::orderBy('date', 'desc')
            ->with('location')->with('intervention')->with('role')->with('categories')
            ->paginate($perPage)
            ->all();

        return response()->json($incidents);
    }

}