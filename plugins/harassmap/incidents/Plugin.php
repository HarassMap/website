<?php namespace Harassmap\Incidents;

use Harassmap\Incidents\Components\ReportIncident;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            ReportIncident::class => 'harassmapReportIncident',
        ];
    }

    public function registerSettings()
    {
    }
}
