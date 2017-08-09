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
        $perPage = min(get('perPage', 10), 100);

        // get the paginated results
        $paginate = $query->paginate($perPage);

        // get all the data
        $data = $paginate->all();

        $params = get();
        unset($params['page']);
        unset($params['perPage']);
        unset($params['key']);

        // build up the results
        $results = [
            'query' => [
                'count' => count($data),
                'per_page' => $paginate->perPage(),
                'total' => $paginate->isEmpty() ? 0 : $paginate->total(),
                'page' => $paginate->currentPage(),
                'params' => $params
            ],
            'data' => $data
        ];

        if ($paginate->hasMorePages()) {
            $results['query']['next_page'] = $paginate->nextPageUrl();
        }


        return response()->json($results);
    }

    public static function error($message)
    {
        return response()->json([
            'code' => '404',
            'message' => $message
        ], 404);
    }

}