<?php

namespace Harassmap\Comments\Components;

use ApplicationException;
use Cms\Classes\ComponentBase;
use Harassmap\Incidents\Models\Content;
use Harassmap\Incidents\Models\Domain;

class Topic extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Comment Topic',
            'description' => 'Add comments to a page'
        ];
    }

    public function defineProperties()
    {
        return [
            'id' => [
                'title' => 'The ID to use for the topics ID',
                'description' => '',
                'type' => 'string'
            ],
        ];
    }

}
