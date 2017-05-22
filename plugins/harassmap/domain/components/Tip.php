<?php

namespace Harassmap\Domain\Components;

use ApplicationException;
use Cms\Classes\ComponentBase;
use Harassmap\Domain\Models\Content;
use Harassmap\Domain\Models\Domain;

class Tip extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Weekly Tip',
            'description' => 'Render the current weekly tip'
        ];
    }

    public function onRender()
    {

    }

}
