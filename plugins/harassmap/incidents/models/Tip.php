<?php

namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Harassmap\Incidents\Models\Tip
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
        'domain' => 'required',
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