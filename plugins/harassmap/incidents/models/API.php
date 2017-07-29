<?php

namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Models\User;

/**
 * API
 *
 * @property int $id
 * @property string $key
 * @property int $user_id
 * @property int $calls
 * @property int $total
 * @property string $last_call
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereCalls($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereLastCall($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereTotal($value)
 * @mixin \Eloquent
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

        return $this->last_call > date('Y-m-01') ? $this->calls : 0;
    }
}