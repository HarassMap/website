<?php namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Models\User;

/**
 * Model
 */
class Incident extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_incident';

    public $rules = [
    ];

    public $hasOne = [
        'user' => User::class,
        'location' => Location::class,
    ];

    public $belongsToMany = [
        'categories' => [
            Category::class,
            'table' => 'harassmap_incidents_incident_category'
        ]
    ];
}