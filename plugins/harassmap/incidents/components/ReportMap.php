<?php

namespace Harassmap\Incidents\Components;

use Cache;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Domain;

class ReportMap extends ComponentBase
{

    const cacheKey = 'harassmap.home.map';

    public function componentDetails()
    {
        return [
            'name' => 'Report Map',
            'description' => 'Shows a map with the reports'
        ];
    }

    public function getViewPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getTablePageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onGetReports()
    {
        $filters = post('filters', []);
        $cacheable = empty($filters);

        if ($cacheable) {
            $cache = Cache::get(self::getCacheKey());

            if ($cache) {
                return unserialize($cache);
            }
        }

        $reports = Incident::withFilters($filters)->toArray();

        if ($cacheable) {
            Cache::put(self::getCacheKey(), serialize($reports), 10080);
        }

        return $reports;
    }

    public static function getCacheKey()
    {
        $domain = Domain::getBestMatchingDomain();

        return self::cacheKey . $domain->id;
    }

}
