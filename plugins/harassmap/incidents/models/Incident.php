<?php namespace Harassmap\Incidents\Models;

use Carbon\Carbon;
use Harassmap\Incidents\Classes\Mailer;
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
 * @property bool $is_intervention
 * @property bool $approved
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
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereIsIntervention($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Incident whereApproved($value)
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

    public $hidden = ['id', 'domain_id', 'role_id', 'user_id', 'is_intervention', 'created_at', 'updated_at'];

    public function generatePublicId()
    {
        // generate a random public id on creation
        $this->public_id = bin2hex(random_bytes(5));

        // try and find an incident which has this public id already
        $duplicate = Incident::where('public_id', '=', $this->public_id)->limit(1)->count();

        // if it exists then create a new one and try again
        if ($duplicate) {
            $this->generatePublicId();
        }
    }

    public function afterCreate()
    {
        Mailer::incidentCreated($this);
    }

    /**
     * @param $bounds
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function whereInsideBounds($bounds, $filters)
    {
        $domain = Domain::getBestMatchingDomain();

        $reports = self::where('domain_id', $domain->id);

        self::filterBounds($reports, [$bounds['south'], $bounds['north']], [$bounds['west'], $bounds['east']]);

        if (array_key_exists('type', $filters) && !empty($filters['type'])) {
            if ($filters['type'] === 'incident') {
                $reports->doesntHave('intervention');
            } else {
                $reports->has('intervention');
            }
        }

        if (array_key_exists('date_from', $filters) && !empty($filters['date_from']) && strtotime($filters['date_from']) !== false) {
            $from = new Carbon($filters['date_from']);
            $reports->where('date', '>', $from->toDateString());
        }

        if (array_key_exists('date_to', $filters) && !empty($filters['date_to']) && strtotime($filters['date_to']) !== false) {
            $to = new Carbon($filters['date_to']);
            $reports->where('date', '<', $to->toDateString());
        }

        return $reports->with('location')->with('intervention')->get();
    }

    public static function filterBounds($query, $lat, $lng)
    {
        $query->whereHas('location', function ($query) use ($lat, $lng) {
            $query
                ->whereBetween('lat', [floatval($lat[0]), floatval($lat[1])])
                ->whereBetween('lng', [floatval($lng[0]), floatval($lng[1])]);
        });
    }
}