<?php

namespace Harassmap\Incidents\Models;

use Model;

/**
 * Harassmap\Incidents\Models\Settings
 *
 * @property int $id
 * @property string $item
 * @property string $value
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Settings whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Settings whereItem($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Settings whereValue($value)
 * @mixin \Eloquent
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
}
