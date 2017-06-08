<?php namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Models\User;

/**
 * Model
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