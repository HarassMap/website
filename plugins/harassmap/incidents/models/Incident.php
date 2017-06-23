<?php namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Models\User;

/**
 * Model
 *
 * @property int $id
 * @property string $public_id
 * @property string $description
 * @property string $date
 * @property int $user_id
 * @property int $location_id
 * @property int $domain_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereDomainId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereLocationId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident wherePublicId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereUserId($value)
 * @mixin \Eloquent
 * @property int $role_id
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereRoleId($value)
 */
class Incident extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_incident';

    public $rules = [
        'public_id' => 'required|string',
        'description' => 'required|string',
        'date' => 'required|date',
        'domain_id' => 'required',
        'categories' => 'required|array',
    ];

    public $belongsTo = [
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