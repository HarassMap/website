<?php
/**
 * Created by PhpStorm.
 * User: r
 * Date: 13.04.16
 * Time: 16:12
 */

use Excodus\TranslateExtended\Models\Settings;
use RainLab\Translate\Classes\Translator;
use RainLab\Translate\Models\Locale;

App::before(function($request) {

    if (App::runningInBackend()) {
        return;
    }

    $translator = Translator::instance();
    if (!$translator->isConfigured())
        return;

    $locale = Request::segment(1);
    $localeSession = Session::get($translator::SESSION_LOCALE);

    /*
     * Behavior when changing locale from the locale picker; post('locale') has priority over $locale,
     * because Request still have old locale in the URL, hence $locale is outdated and User sends new locale in the POST
     * TODO: hook the translate plugin's onSwitchLocale ajax handler instead of checking on post
     */
    if (post('locale') && $locale != post('locale')) {
        $translator->setLocale(post('locale'));
    }

    /*
     * Behavior when there is no locale in the Request URL, first check in session and then try to match with default browser language
     */
    if (!$locale || !Locale::isValid($locale)) {
        if (Settings::get('prefer_user_session',true) && $localeSession) {
            $translator->setLocale($localeSession);
        } else {
            if(Settings::get('browser_language_detection',true) && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                // get the list of browser languages
                $accepted = parseLanguageList($_SERVER['HTTP_ACCEPT_LANGUAGE']);
                $available = Locale::listEnabled();
                // match against languages enabled in Translate plugin
                // TODO: allow october backend users to create their own mappings to the locale short codes
                $matches = findMatches($accepted, $available);
                // get the first match and save if not empty
                if (!empty($matches)) {
                    $match = array_keys($matches)[0];
                    $translator->setLocale($match);
                }
            }
        }
    }

    /*
      * If it was unable to retrieve locale from session, route url or browser matching, just roll back to default locale
    */
    $locale = $translator->getLocale();

    if (!Locale::isValid($locale)) {
        $translator->setLocale($translator->getDefaultLocale());
    }

    if(Settings::get('route_prefixing', true)) {
        Route::group(['prefix' => $locale], function() {
            Route::any('{slug}', 'Cms\Classes\CmsController@run')->where('slug', '(.*)?');
        });

        Route::any($locale, 'Cms\Classes\CmsController@run');

        Event::listen('cms.route', function() use ($locale) {
            Route::group(['prefix' => $locale], function() {
                Route::any('{slug}', 'Cms\Classes\CmsController@run')->where('slug', '(.*)?');
            });
        });

        if(Settings::get('homepage_redirect', true)) {
            Route::get('/', function() use ($locale) {
                return redirect($locale);
            });
        }

    }
});

// browser language parser based on Gumbo's answer
// http://stackoverflow.com/a/3771447/3704886

// parse list of comma separated language tags and sort it by the quality value
function parseLanguageList($languageList) {
    if (is_null($languageList)) {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return array();
        }
        $languageList = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }
    $languages = array();
    $languageRanges = explode(',', trim($languageList));
    foreach ($languageRanges as $languageRange) {
        if (preg_match('/(\*|[a-zA-Z0-9]{1,8}(?:-[a-zA-Z0-9]{1,8})*)(?:\s*;\s*q\s*=\s*(0(?:\.\d{0,3})|1(?:\.0{0,3})))?/', trim($languageRange), $match)) {
            if (!isset($match[2])) {
                $match[2] = '1.0';
            } else {
                $match[2] = (string) floatval($match[2]);
            }
            if (!isset($languages[$match[2]])) {
                $languages[$match[2]] = strtolower($match[1]);
            }
        }
    }
    krsort($languages);
    return $languages;
}

// compare two parsed arrays of language tags and find the matches
function findMatches($accepted, $available) {
    $matches = array();
    $any = false;
    foreach ($available as $availableLocale => $availableName) {
        foreach ($accepted as $acceptedQuality => $acceptedLocale) {
        $acceptedQuality = floatval($acceptedQuality);
        if ($acceptedQuality === 0.0) continue;
            if ($acceptedLocale === '*') {
                $any = true;
            }
            $matchingGrade = matchLanguage($acceptedLocale, $availableLocale);
            if ($matchingGrade > 0) {
                $q = ($acceptedQuality * $matchingGrade);
                if (!array_key_exists($availableLocale, $matches) || $matches[$availableLocale] < $q) {
                    $matches[$availableLocale] = $q;
                }
            }
        }
    }
    if (count($matches) === 0 && $any) {
        $matches = $available;
    }
    arsort($matches);
    return $matches;
}


/**
 * compare two language tags and distinguish the degree of matching 
 * edit: actually matching "en-us" with "en" will always return "1"
 * @param $a [] user-accepted
 * @param $b [] backend-available
 * @return float|int
 */
function matchLanguage($a, $b) {
    // convert 'en-US' to 'en-us'
    $b = strtolower($b);
    $a = explode('-', $a);
    $b = explode('-', $b);
    $perfect_match = false;
    for ($i=0, $n=min(count($a), count($b)); $i<$n; $i++) {
        if ($a[$i] !== $b[$i]) break;
        if (count($a) == count($b) && $i == $n-1) {
            $perfect_match = true;
        }
    }

    $val = $i === 0 ? 0 : (float) $i / count($a);
    return $perfect_match ? 2 : $val;
}
