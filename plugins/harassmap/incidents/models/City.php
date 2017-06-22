<?php

namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Model
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