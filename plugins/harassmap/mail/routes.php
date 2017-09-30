<?php

use RainLab\Translate\Classes\Translator;
use RainLab\User\Facades\Auth;
use RainLab\Translate\Models\Locale;

App::before(function ($request) {

    if (App::runningInBackend()) {
        return;
    }

    $translator = Translator::instance();
    if (!$translator->isConfigured()) {
        return;
    }

    $locale = Request::segment(1);
    $user = Auth::getUser();

    // if there is a user
    if ($locale && Locale::isValid($locale) && $user && $user->locale !== $locale) {
        $user->locale = $locale;
        $user->save();
    }

});