<?php namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 */
class Incident extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_incident';

    public $rules = [
    ];

    public $belongsToMany = [
        'categories' => [
            Category::class,
            'table' => 'harassmap_incident_category'
        ]
    ];
}