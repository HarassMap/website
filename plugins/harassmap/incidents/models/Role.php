<?php namespace Harassmap\Incidents\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 *
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Role whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Role whereName($value)
 * @mixin \Eloquent
 */
class Role extends Model
{
    use Validation;

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

    public $belongsToMany = [
        'domains' => [Domain::class, 'table' => 'harassmap_incidents_domain_role'],
    ];

    public $hidden = ['id'];

    public function scopeDomain($query, $status)
    {
        $query->whereHas('domains', function ($query) use ($status) {
            $query->whereIn('domain_id', $status);
        });
    }
}