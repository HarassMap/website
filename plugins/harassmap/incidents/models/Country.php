<?php

namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 *
 * @property int $id
 * @property string $name
 * @property string $iso
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Country whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Country whereIso($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Country whereName($value)
 * @mixin \Eloquent
 */
class Country extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_country';

    public $timestamps = false;

    public $rules = [
    ];

    public $hasMany = [
        'cities' => [City::class, 'delete' => true]
    ];

}