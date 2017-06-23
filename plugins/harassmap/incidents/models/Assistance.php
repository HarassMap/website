<?php namespace Harassmap\Incidents\Models;

use Model;
use \October\Rain\Database\Traits\Validation;

/**
 * Model
 *
 * @property int $id
 * @property string $title
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Assistance whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Assistance whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Assistance whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Assistance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Assistance extends Model
{
    use Validation;

    public $table = 'harassmap_incidents_assistance';

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['title'];

    public $rules = [
        'title' => 'required',
    ];

    public $belongsToMany = [
        'interventions' => [Intervention::class, 'table' => 'harassmap_incidents_intervention_assistance']
    ];
}