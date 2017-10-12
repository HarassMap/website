<?php namespace Harassmap\Incidents\Models;

use Cache;
use Carbon\Carbon;
use Harassmap\Comments\Models\Comment;
use Harassmap\Comments\Models\Topic;
use Harassmap\Incidents\Classes\Analytics;
use Harassmap\Incidents\Classes\Mailer;
use Harassmap\Incidents\Components\ReportMap;
use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Models\User;

/**
 * Harassmap\Incidents\Models\Incident
 *
 * @property int $id
 * @property string $public_id
 * @property string $description
 * @property string $date
 * @property int|null $user_id
 * @property int $domain_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $role_id
 * @property int $verified
 * @property int $support
 * @property int $is_intervention
 * @property int $approved
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident intervention($status)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident whereDomainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident whereIsIntervention($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident whereSupport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Incident whereVerified($value)
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
        'location' => [
            Location::class,
            'delete' => true
        ],
        'intervention' => [
            Intervention::class,
            'delete' => true
        ],
        'topic' => [
            Topic::class,
            'delete' => true,
            'key' => 'code',
            'otherKey' => 'public_id'
        ],
    ];

    public $hasManyThrough = [
        'comments' => [
            Comment::class,
            'through' => Topic::class,
            'key' => 'code',
            'otherKey' => 'public_id'
        ]
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

    public function beforeCreate()
    {
        $this->generatePublicId();
    }

    public function afterCreate()
    {
        // dont clear the cache of we are in the cli
        if (!(php_sapi_name() === 'cli')) {
            Mailer::incidentCreated($this);

            Analytics::reportCreated($this);

            Cache::forget(ReportMap::cacheKey);
        }
    }

    public function afterUpdate()
    {
        // get which attributes have changed
        $changed = $this->getDirty();

        if (array_key_exists('support', $changed)) {
            Analytics::reportSupportAdded($this);
        } else {
            Analytics::reportEdited($this);
        }
    }

    public function afterDelete()
    {
        Analytics::reportDeleted($this);

        Cache::forget(ReportMap::cacheKey);
    }

    public function scopeIntervention($query, $status)
    {
        // if status is 1 then we remove interventions
        // otherwise its only interventions
        if ($status === "1") {
            $query->doesntHave('intervention');
        } else {
            $query->has('intervention');
        }
    }

    /**
     * @param $filters
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function withFilters($filters)
    {
        $domain = Domain::getBestMatchingDomain();

        $reports = self::where('domain_id', $domain->id);

        self::applyFilters($reports, $filters);

        return $reports->with('location')->with('intervention')->get();
    }

    public static function applyFilters($query, $filters)
    {
        if (array_key_exists('type', $filters) && !empty($filters['type'])) {
            if ($filters['type'] === 'incident') {
                $query->doesntHave('intervention');
            } else {
                $query->has('intervention');
            }
        }

        if (array_key_exists('date_from', $filters) && !empty($filters['date_from']) && strtotime($filters['date_from']) !== false) {
            $from = new Carbon($filters['date_from']);
            $query->where('date', '>', $from->toDateString());
        }

        if (array_key_exists('date_to', $filters) && !empty($filters['date_to']) && strtotime($filters['date_to']) !== false) {
            $to = new Carbon($filters['date_to']);
            $query->where('date', '<', $to->toDateString());
        }

        return $query;
    }

    public static function filterBounds($query, $lat, $lng)
    {
        $query->whereHas('location', function ($query) use ($lat, $lng) {
            $query
                ->whereBetween('lat', [floatval($lat[0]), floatval($lat[1])])
                ->whereBetween('lng', [floatval($lng[0]), floatval($lng[1])]);
        });

        return $query;
    }
}