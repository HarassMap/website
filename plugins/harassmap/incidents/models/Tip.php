<?php

namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Tip
 *
 */
class Tip extends Model
{
    use Validation;
    use DomainOptions;

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['tip'];

    public $rules = [
        'tip' => 'required'
    ];

    public $belongsTo = [
        'domain' => Domain::class
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'harassmap_domain_tip';

    public function beforeCreate()
    {
        if ($this->featured_from == '') {
            $this->featured_from = date('Y-m-d H:i:00');
        }
    }
}