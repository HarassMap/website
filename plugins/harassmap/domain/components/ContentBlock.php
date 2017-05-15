<?php

namespace Harassmap\Domain\Components;

use Cms\Classes\ComponentBase;

class ContentBlock extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Content Block',
            'description' => 'Render a content block.'
        ];
    }

    public function defineProperties()
    {
        return [
            'id' => [
                'description' => 'The ID of the content block to display',
                'title' => 'Content ID',
                'default' => '',
                'type' => 'string'
            ]
        ];
    }

}
