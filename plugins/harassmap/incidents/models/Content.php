<?php

namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Content
 */
class Content extends Model
{
    use Validation;
    use DomainOptions;

    const CONTENT_IDS = [
        'homepage.basics' => 'Homepage (Learn)',
        'homepage.share' => 'Homepage (Share)',
        'homepage.active' => 'Homepage (Active)',
        'homepage.droplet' => 'Homepage (Droplet)',
        'homepage.bottomLeft' => 'Homepage (Bottom Left)',
        'homepage.bottomCenter' => 'Homepage (Bottom Center)',
        'homepage.bottomRight' => 'Homepage (Bottom Right)',
        'report.incident' => 'Report Incident',
    ];

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['content'];

    public $rules = [
        'content' => 'required'
    ];

    public $belongsTo = [
        'domain' => Domain::class
    ];

    public $attachOne = [
        'image' => 'System\Models\File'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'harassmap_domain_content';

    public function getContentIdOptions()
    {
        return self::CONTENT_IDS;
    }
}