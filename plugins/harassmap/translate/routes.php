<?php

use Harassmap\Translate\Models\Message;

/*
 * Save any used messages to the contextual cache.
 */
App::after(function ($request) {
    if (class_exists('Harassmap\Translate\Models\Message')) {
        Message::saveToCache();
    }
});