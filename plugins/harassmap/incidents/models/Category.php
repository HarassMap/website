<?php

namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use October\Rain\Database\Traits\Sortable;

/**
 * Category
 */
class Category extends Model
{
    use Validation;
    use Sortable;

    public $table = 'harassmap_incidents_category';

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['title', 'description'];

    public $belongsToMany = [
        'incidents' => [
            Incident::class,
            'table' => 'harassmap_incidents_incident_category'
        ]
    ];

    public $rules = [
    ];

    public function beforeSave()
    {

        // adding a default value for the sort order
        if(!$this->sort_order) {
            $this->sort_order = 0;
        }
    }
}