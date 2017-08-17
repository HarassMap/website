<?php

namespace Harassmap\Comments\Components;

use Cms\Classes\ComponentBase;
use Exception;
use Harassmap\Comments\Models\Topic as TopicModel;

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

    public function onRender()
    {
        $id = $this->property('id');

        if (!$id) {
            throw new Exception('No id specified for the comments topic component');
        }

        $topic = TopicModel::whereCode($id)->first();

        // if there is no topic then create it
        if (!$topic) {
            $topic = TopicModel::createWithCode($id);
        }

        $this->page['topic'] = $topic;
    }

}
