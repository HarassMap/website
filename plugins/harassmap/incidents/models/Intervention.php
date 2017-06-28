<?php namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Models\User;

/**
 * Model
 *
 * @mixin \Eloquent
 */
class Intervention extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_intervention';

    public $throwOnValidation = false;

    public $rules = [
        'assistance' => 'required|array',
    ];

    public $belongsTo = [
        'incident' => Incident::class,
    ];

    public $belongsToMany = [
        'assistance' => [
            Assistance::class,
            'table' => 'harassmap_incidents_intervention_assistance'
        ]
    ];
}