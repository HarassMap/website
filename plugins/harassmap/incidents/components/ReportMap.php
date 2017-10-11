<?php

namespace Harassmap\Incidents\Components;

use Cache;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Incident;

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
            $cache = Cache::get(self::cacheKey);

            if ($cache) {
                return unserialize($cache);
            }
        }

        $reports = Incident::withFilters($filters)->toArray();

        if ($cacheable) {
            Cache::put(self::cacheKey, serialize($reports), 10080);
        }

        return $reports;
    }

}
