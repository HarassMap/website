<?php

namespace Harassmap\Incidents\Components;

use Excodus\TranslateExtended\Components\ExtendedLocalePicker as LocalePicker;
use Harassmap\Incidents\Models\Domain;

class ExtendedLocalePicker extends LocalePicker
{

    public function onRun()
    {
        parent::onRun();

        $domain = Domain::getBestMatchingDomain();
        $languages = explode(',', $domain->languages);

        $this->page['locales'] = array_filter($this->page['locales'], function ($key) use ($languages) {
            return in_array($key, $languages);
        }, ARRAY_FILTER_USE_KEY);
    }
}