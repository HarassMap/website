<?php

namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Harassmap\Incidents\Models\Tip
 *
 * @property int $id
 * @property string $tip
 * @property string $featured_from
 * @property int $domain_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $read_more
 * @property string|null $link
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Tip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Tip whereDomainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Tip whereFeaturedFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Tip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Tip whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Tip whereReadMore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Tip whereTip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Tip whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tip extends Model
{
    use Validation;
    use DomainOptions;

    public $table = 'harassmap_incidents_tip';

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = [
        'tip',
        'read_more',
        'link',
    ];

    public $rules = [
        'tip' => 'required',
        'read_more' => 'max:50',
        'link' => 'max:100',
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