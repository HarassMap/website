<?php

namespace Harassmap\Translate\Classes;

use Harassmap\Incidents\Models\Domain;
use Harassmap\Translate\Models\Message;
use October\Rain\Support\Traits\Singleton;
use RainLab\Translate\Classes\Translator;

class EventRegistry
{
    use Singleton;

    /**
     * Set the page context for translation caching.
     */
    public function setMessageContext($page)
    {
        if (!$page) {
            return;
        }

        $translator = Translator::instance();

        Message::setContext($translator->getLocale(), $page->url);
    }

}