<?php

namespace Harassmap\Incidents\Models;

use Model;
use RainLab\User\Models\User;
use October\Rain\Database\Traits\Validation;

/**
 * API
 *
 * @property int $id
 * @property string $key
 * @property int $user_id
 * @property int $calls
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereCalls($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\API whereUserId($value)
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
}