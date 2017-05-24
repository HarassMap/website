<?php

namespace Harassmap\News\Components;

use Cms\Classes\ComponentBase;

class Posts extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Posts',
            'description' => 'Gets a list of the most recent posts.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {

    }

}
