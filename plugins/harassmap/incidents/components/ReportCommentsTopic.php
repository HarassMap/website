<?php

namespace Harassmap\Incidents\Components;

use RainLab\Forum\Components\Topic;

class ReportCommentsTopic extends Topic
{

    public function componentDetails()
    {
        return [
            'name' => 'Incident Comments Topic',
            'description' => ''
        ];
    }

    public function onRun()
    {
        $this->prepareVars();
        $this->page['channel'] = $this->getChannel();
        $this->page['topic']   = $topic = $this->getTopic();
        $this->page['member']  = $member = $this->getMember();
        $this->handleOptOutLinks();

        return $this->preparePostList();
    }

}
