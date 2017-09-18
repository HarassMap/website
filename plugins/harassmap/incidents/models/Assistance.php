<?php

namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use \October\Rain\Database\Traits\Validation;

/**
 * Harassmap\Incidents\Models\Assistance
 *
 * @property int $id
 * @property string $title
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Assistance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Assistance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Assistance whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Assistance whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Assistance domain($status)
 */
class Assistance extends Model
{
    use Validation;
    use DomainOptions;

    public $table = 'harassmap_incidents_assistance';

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['title'];

    public $rules = [
        'title' => 'required',
    ];

    public $belongsToMany = [
        'interventions' => [Intervention::class, 'table' => 'harassmap_incidents_intervention_assistance'],
    ];

    public $belongsTo = [
        'domain' => Domain::class,
    ];

    public function scopeDomain($query, $status)
    {
        $query->whereHas('domains', function ($query) use ($status) {
            $query->whereIn('domain_id', $status);
        });
    }
}