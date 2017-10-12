<?php

namespace Harassmap\Translate\Components;

use RainLab\Translate\Components\LocalePicker;
use Harassmap\Incidents\Models\Domain;
use RainLab\Translate\Classes\Translator;
use RainLab\Translate\Models\Locale as LocaleModel;

class ExtendedLocalePicker extends LocalePicker
{
    public function componentDetails()
    {
        return [
            'name'        => 'Extended Locale Picker',
        ];
    }

    public function init()
    {
        $this->translator = Translator::instance();
    }

    public function onRun()
    {
        $this->page['activeLocale'] = $this->activeLocale = $this->translator->getLocale();
        $this->page['locales'] = $this->locales = LocaleModel::listEnabled();
        $this->page['localeLinks'] = $this->makeLinks($this->locales);

        $domain = Domain::getBestMatchingDomain();
        $languages = explode(',', $domain->languages);

        $this->page['locales'] = array_filter($this->page['locales'], function ($key) use ($languages) {
            return in_array($key, $languages);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function makeLinks($locales)
    {
        $links = [];
        foreach ($locales as $key => $locale) {
            $links[$key] = $this->makeLocaleUrlFromPage($key);
        }

        return $links;
    }
}