<?php

namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\Translate\Models\Locale;

/**
 * Model
 */
class Logo extends Model
{
    use Validation;
    use DomainOptions;

    public $table = 'harassmap_incidents_domain_logo';

    public $rules = [
    ];

    public $attachOne = [
        'image' => 'System\Models\File'
    ];

    public function getLanguageOptions()
    {
        return Locale::isEnabled()->lists('code', 'code');
    }
}