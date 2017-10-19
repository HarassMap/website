<?php

namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

/**
 * Harassmap\Incidents\Models\Category
 */
class Category extends Model
{
    use Validation;
    use Sortable;
    use DomainOptions;

    public $table = 'harassmap_incidents_category';

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['title', 'description'];

    public $belongsToMany = [
        'incidents' => [Incident::class, 'table' => 'harassmap_incidents_incident_category'],
    ];

    public $belongsTo = [
        'domain' => Domain::class,
    ];

    public $rules = [
        'title' => 'required',
        'description' => 'required',
        'color' => 'required',
    ];

    protected $fillable = ['title', 'description', 'color', 'domain_id'];

    public $hidden = ['id', 'color', 'sort_order', 'pivot', 'created_at', 'updated_at', 'domain_id'];

    public function beforeSave()
    {

        // adding a default value for the sort order
        if (!$this->sort_order) {
            $this->sort_order = 0;
        }

        if ($this->domain_id === '') {
            $this->domain_id = NULL;
        }
    }
}