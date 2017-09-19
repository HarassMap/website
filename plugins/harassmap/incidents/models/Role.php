<?php namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Harassmap\Incidents\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property int|null $domain_id
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Role whereDomainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Role whereName($value)
 * @mixin \Eloquent
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

    public $hidden = ['id'];
}