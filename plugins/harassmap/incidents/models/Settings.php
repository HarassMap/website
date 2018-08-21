<?php

namespace Harassmap\Incidents\Models;

use Model;
use Log;

/**
 * Harassmap\Incidents\Models\Settings
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'harassmap_settings';

    public $settingsFields = 'fields.yaml';

    public function initSettingsData()
    {
        $this->api_day_limit = 200;
    }

    public function getMapOptions() {

        return [
            'red_green' => 'Red and Green',
            'yellow_blue' => 'Yellow and Blue'
        ];
    }

    public function onRender() {
        $maxItems = Settings::get('map');
        Log::info($maxItems);
    }
}
