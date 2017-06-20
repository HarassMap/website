<?php

namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 *
 * @property int $id
 * @property string $name
 * @property string $lat
 * @property string $lng
 * @property int $country_id
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\City whereCountryId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\City whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\City whereLat($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\City whereLng($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\City whereName($value)
 * @mixin \Eloquent
 */
class City extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_city';

    public $timestamps = false;

    public $rules = [
    ];

    public $belongsTo = [
        'country' => Country::class
    ];
}