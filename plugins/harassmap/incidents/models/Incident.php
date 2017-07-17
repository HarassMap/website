<?php namespace Harassmap\Incidents\Models;

use Illuminate\Support\Facades\DB;
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
 * @property int $role_id
 * @property bool $verified
 * @property int $support
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
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereRoleId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereSupport($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereVerified($value)
 * @mixin \Eloquent
 */
class Incident extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_incident';

    public $throwOnValidation = false;

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['description'];

    public $rules = [
        'public_id' => 'required|string',
        'description' => 'required|string',
        'date' => 'required|date|before:now',
        'domain' => 'required',
        'categories' => 'required|array',
        'role' => 'required',
    ];

    public $belongsTo = [
        'user' => User::class,
        'domain' => Domain::class,
        'role' => Role::class,
    ];

    public $hasOne = [
        'location' => [Location::class, 'delete' => true],
        'intervention' => [Intervention::class, 'delete' => true],
    ];

    public $belongsToMany = [
        'categories' => [Category::class, 'table' => 'harassmap_incidents_incident_category']
    ];

    public $hidden = ['id', 'domain_id'];

    /**
     * @param $bounds
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function whereInsideBounds($bounds)
    {
        $domain = Domain::getBestMatchingDomain();

        $reports = self::where('domain_id', $domain->id)->whereHas('location', function ($query) use ($bounds) {
            $query
                ->whereBetween(DB::raw('CAST(lat as DECIMAL(10,6))'), [floatval($bounds['south']), floatval($bounds['north'])])
                ->whereBetween(DB::raw('CAST(lng as DECIMAL(10,6))'), [floatval($bounds['west']), floatval($bounds['east'])]);
        })->with('location')->with('intervention')->get();

        return $reports;
    }
}