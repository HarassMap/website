<?php

namespace Harassmap\Incidents\Classes\Api;

use Cms\Classes\CmsController;

class BaseController extends CmsController
{

    public function __construct()
    {
        parent::__construct();

        // add middleware
        $this->middleware(CheckKeyMiddleware::class);
    }

}