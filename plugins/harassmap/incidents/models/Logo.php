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

    const LOGO_IDS = [
        'desktop' => 'Desktop',
        'mobile' => 'Mobile',
        'footer' => 'Footer',
        'email' => 'Email',
        'meta' => 'Meta (Open Graph)',
    ];

    public $rules = [
        'domain' => 'required',
        'language' => 'required',
        'position' => 'required',
        'image' => 'required',
    ];

    public $belongsTo = [
        'domain' => Domain::class
    ];

    public $attachOne = [
        'image' => 'System\Models\File'
    ];

    public function getLanguageOptions()
    {
        return Locale::isEnabled()->lists('code', 'code');
    }

    public function getPositionOptions()
    {
        return self::LOGO_IDS;
    }
}