<?php

namespace Harassmap\News\Components;

use Cms\Classes\ComponentBase;

class Post extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Post',
            'description' => 'Displays one post in its entirety.'
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
