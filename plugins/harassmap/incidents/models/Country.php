<?php

namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 */
class Country extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_country';

    public $timestamps = false;

    public $rules = [
    ];

    public $hasMany = [
        'cities' => [City::class, 'delete' => true],
        'domains' => Domain::class
    ];

}