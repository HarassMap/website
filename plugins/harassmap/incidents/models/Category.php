<?php

namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use October\Rain\Database\Traits\Sortable;

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

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['title', 'description'];

    /*
     * Validation
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'harassmap_incidents_category';

    public function beforeSave()
    {

        // adding a default value for the sort order
        if(!$this->sort_order) {
            $this->sort_order = 0;
        }
    }
}