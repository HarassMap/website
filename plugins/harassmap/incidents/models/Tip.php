<?php

namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Tip
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