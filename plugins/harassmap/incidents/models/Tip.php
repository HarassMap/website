<?php

namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Tip
 *
 * @property int $id
 * @property string $tip
 * @property string $featured_from
 * @property int $domain_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Tip whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Tip whereDomainId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Tip whereFeaturedFrom($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Tip whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Tip whereTip($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Tip whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tip extends Model
{
    use Validation;
    use DomainOptions;

    public $table = 'harassmap_incidents_tip';

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['tip'];

    public $rules = [
        'tip' => 'required'
    ];

    public $belongsTo = [
        'domain' => Domain::class
    ];

    public function beforeCreate()
    {
        if ($this->featured_from == '') {
            $this->featured_from = date('Y-m-d H:i:00');
        }
    }
}