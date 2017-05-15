<?php

namespace Harassmap\Domain\Components;

use Cms\Classes\ComponentBase;
use Harassmap\Domain\Models\Content;
use ApplicationException;

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
                'type' => 'dropdown',
                'placeholder' => 'Select Content Id',
                'options' => Content::CONTENT_IDS,
                'required' => true
            ]
        ];
    }

    public function onRun()
    {
        if (empty($this->property('id'))) {
            throw new ApplicationException('You need to set a content id.');
        }

        $content = Content::where('content_id', '=', $this->property('id'))->get()->first();

        $this->page['content'] = $content->content;

        // TODO: get the current domain/language etc and then find the content block for this component
    }

}
