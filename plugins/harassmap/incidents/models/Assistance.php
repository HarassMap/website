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
 * @property int|null $domain_id
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Assistance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Assistance whereDomainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Assistance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Assistance whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Assistance whereUpdatedAt($value)
 * @mixin \Eloquent
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

    public $hidden = ['id', 'created_at', 'updated_at', 'domain_id', 'pivot'];

    public function beforeSave()
    {
        if($this->domain_id === '') {
            $this->domain_id = NULL;
        }
    }
}