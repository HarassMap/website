<?php

namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use \October\Rain\Database\Traits\Validation;

/**
 * Harassmap\Incidents\Models\Assistance
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