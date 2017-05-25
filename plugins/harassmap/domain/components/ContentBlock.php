<?php

namespace Harassmap\Domain\Components;

use ApplicationException;
use Cms\Classes\ComponentBase;
use Harassmap\Domain\Models\Content;
use Harassmap\Domain\Models\Domain;

class ContentBlock extends ComponentBase
{

    public $content = '';

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

    public function onRender()
    {
        $content_id = $this->property('id');

        if (empty($content_id)) {
            throw new ApplicationException('You need to set a content id.');
        }

        // get the list of matching domains
        $domains = Domain::getMatchingDomains();
        $found = false;

        foreach ($domains as $domain) {
            $content = Content
                ::where('content_id', '=', $content_id)
                ->where('domain_id', '=', $domain->id)
                ->first();

            if ($content) {
                $found = $content;
                break;
            }
        }

        // if we have found the content block then
        if ($found) {
            $this->page['content_id'] = $content_id;
            $this->content = $found->content;
        }
    }

}
