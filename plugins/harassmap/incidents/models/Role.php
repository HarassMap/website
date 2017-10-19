<?php

namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Harassmap\Incidents\Models\Role
 */
class Role extends Model
{
    use Validation;
    use DomainOptions;

    public $table = 'harassmap_incidents_role';

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['name'];

    public $timestamps = false;

    public $rules = [
        'name' => 'required|max:100'
    ];

    public $hasMany = [
        'incidents' => Incident::class,
    ];

    public $belongsTo = [
        'domain' => Domain::class,
    ];

    public $hidden = ['id', 'domain_id'];

    public function beforeSave()
    {
        if($this->domain_id === '') {
            $this->domain_id = NULL;
        }
    }
}