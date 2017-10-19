<?php

namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Harassmap\Incidents\Models\Content
 */
class Content extends Model
{
    use Validation;
    use DomainOptions;

    public $table = 'harassmap_incidents_content';

    const CONTENT_IDS = [
        'login' => 'Login',
        'register' => 'Register (Footer)',
        'register.terms' => 'Register (Terms)',
        'homepage.basics' => 'Homepage (Learn)',
        'homepage.share' => 'Homepage (Share)',
        'homepage.active' => 'Homepage (Active)',
        'homepage.droplet' => 'Homepage (Droplet)',
        'homepage.bottomLeft' => 'Homepage (Bottom Left)',
        'homepage.bottomCenter' => 'Homepage (Bottom Center)',
        'homepage.bottomRight' => 'Homepage (Bottom Right)',
        'report.incident' => 'Report (Incident)',
        'report.intervention' => 'Report (Intervention)',
        'report.thanks' => 'Report (Thanks)',
        'report.comments' => 'Report (Comments)',
        'footer.about' => 'Footer (About)',
        'tips' => 'Tips',
        'contact' => 'Contact',
        'chart' => 'Chart Page',
    ];

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['content'];

    public $rules = [
        'content_id' => 'required|max:50',
        'content' => 'required',
        'domain' => 'required',
    ];

    public $belongsTo = [
        'domain' => Domain::class
    ];

    public $attachOne = [
        'image' => 'System\Models\File'
    ];

    public function getContentIdOptions()
    {
        return self::CONTENT_IDS;
    }
}