<?php

namespace Harassmap\Incidents\Classes\Api;

use Cms\Classes\CmsController;
use October\Rain\Database\Builder;

class BaseController extends CmsController
{

    public function __construct()
    {
        parent::__construct();

        // add middleware
        $this->middleware(CheckKeyMiddleware::class);
    }

    protected function apiResponse(Builder $query)
    {
        // get the per page with maximum value of 100
        $perPage = min(get('perPage', 100), 100);

        $options = null;

        // if the user wants a pretty response
        if (get('pretty', false)) {
            $options = JSON_PRETTY_PRINT;
        }

        // get the paginated results
        $paginate = $query->paginate($perPage);

        // get all the data
        $data = $paginate->all();

        $params = get();
        unset($params['page']);
        unset($params['perPage']);
        unset($params['key']);
        unset($params['pretty']);

        // build up the results
        $results = [
            'query' => [
                'count' => count($data),
                'perPage' => $paginate->perPage(),
                'total' => $paginate->isEmpty() ? 0 : $paginate->total(),
                'page' => $paginate->currentPage(),
                'params' => $params
            ],
            'data' => $data
        ];

        return response()->json($results, 200, array(), $options);
    }

    public static function error($message)
    {
        return response()->json([
            'code' => '404',
            'message' => $message
        ], 404);
    }

}