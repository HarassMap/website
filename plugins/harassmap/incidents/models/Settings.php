<?php

namespace Harassmap\Incidents\Models;

use Model;

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

    public function getMapPinsOptions() {

        return [
            'red_green' => 'Red and Green',
            'yellow_blue' => 'Yellow and Blue'
        ];
    }
}
