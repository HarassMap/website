<?php

namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

/**
 * Category
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $color
 * @property int $sort_order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Category whereColor($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Category whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Category whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Category whereSortOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Category whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    use Validation;
    use Sortable;

    public $table = 'harassmap_incidents_category';

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['title', 'description'];

    public $belongsToMany = [
        'incidents' => [Incident::class, 'table' => 'harassmap_incidents_incident_category'],
        'domains' => [Domain::class, 'table' => 'harassmap_incidents_domain_category'],
    ];

    public $rules = [
        'title' => 'required',
        'description' => 'required',
        'color' => 'required',
    ];

    protected $fillable = [
        'title', 'description', 'color'
    ];

    public $hidden = ['id', 'color', 'sort_order', 'pivot', 'created_at', 'updated_at'];

    public function beforeSave()
    {

        // adding a default value for the sort order
        if (!$this->sort_order) {
            $this->sort_order = 0;
        }
    }
}