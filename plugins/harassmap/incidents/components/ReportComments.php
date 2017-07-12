<?php

namespace Harassmap\Incidents\Components;

use Exception;
use RainLab\Forum\Components\EmbedTopic;
use RainLab\Forum\Models\Channel as ChannelModel;
use RainLab\Forum\Models\Topic as TopicModel;

class ReportComments extends EmbedTopic
{

    public function componentDetails()
    {
        return [
            'name' => 'Incident Comments',
            'description' => ''
        ];
    }

    public function init()
    {
        $mode = $this->property('mode');
        $code = $this->property('embedCode');

        if (!$code) {
            throw new Exception('No code specified for the Forum Embed component');
        }

        $channel = ($channelSlug = $this->property('channelSlug'))
            ? ChannelModel::whereSlug($channelSlug)->first()
            : null;

        if (!$channel) {
            throw new Exception('No channel specified for Forum Embed component');
        }

        $properties = $this->getProperties();

        /*
         * Proxy as topic
         */
        if ($topic = TopicModel::forEmbed($channel, $code)->first()) {
            $properties['slug'] = $topic->slug;
        }

        $component = $this->addComponent(ReportCommentsTopic::class, $this->alias, $properties);

        /*
         * If a topic does not already exist, generate it when the page ends.
         * This can be disabled by the page setting embedMode to FALSE, for example,
         * if the page returns 404 a topic should not be generated.
         */
        if (!$topic) {
            $this->controller->bindEvent('page.end', function () use ($component, $channel, $code) {
                if ($component->embedMode !== false) {
                    $topic = TopicModel::createForEmbed($code, $channel, $this->page->title);
                    $component->setProperty('slug', $topic->slug);
                    $component->onRun();
                }
            });
        }

        /*
         * Set the embedding mode: Single topic
         */
        $component->embedMode = 'single';
    }

}
