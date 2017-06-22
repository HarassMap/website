<?php namespace Harassmap\Incidents\Models;

use Model;
use \October\Rain\Database\Traits\Validation;

/**
 * Model
 */
class Assistance extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_assistance';

    public $rules = [
    ];

    public $belongsToMany = [
        'interventions' => [
            Intervention::class,
            'table' => 'harassmap_incidents_intervention_assistance'
        ]
    ];
}