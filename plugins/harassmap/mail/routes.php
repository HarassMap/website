<?php

use RainLab\Translate\Classes\Translator;
use RainLab\User\Facades\Auth;

App::before(function ($request) {

    if (App::runningInBackend()) {
        return;
    }

    $translator = Translator::instance();
    if (!$translator->isConfigured())
        return;

    $locale = Request::segment(1);
    $user = Auth::getUser();

    // if there is a user
    if ($user && $user->locale !== $locale) {
        $user->locale = $locale;
        $user->save();
    }

});