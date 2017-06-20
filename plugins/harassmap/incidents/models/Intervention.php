<?php namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Models\User;

/**
 * Model
 *
 * @property int $id
 * @property int $user_id
 * @property int $incident_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Intervention whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Intervention whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Intervention whereIncidentId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Intervention whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Intervention whereUserId($value)
 * @mixin \Eloquent
 */
class Intervention extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_intervention';

    public $rules = [
    ];

    public $hasOne = [
        'user' => User::class,
        'incident' => Incident::class,
    ];

    public $belongsToMany = [
        'assistance' => [
            Assistance::class,
            'table' => 'harassmap_incidents_intervention_assistance'
        ]
    ];
}