<?php

namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Models\User;

/**
 * Harassmap\Incidents\Models\API
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

    public function getCallsThisMonth()
    {
        // if we have made no calls
        if ($this->total === 0) {
            return 0;
        }

        return strtotime($this->last_call) > strtotime(date('Y-m-d')) ? $this->calls : 0;
    }
}