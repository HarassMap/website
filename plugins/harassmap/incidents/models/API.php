<?php

namespace Harassmap\Incidents\Models;

use Model;
use RainLab\User\Models\User;
use October\Rain\Database\Traits\Validation;

/**
 * API
 */
class API extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_api';

    public $rules = [
        'user' => 'required',
        'key' => 'required'
    ];

    public $belongsTo = [
        'user' => User::class
    ];
}