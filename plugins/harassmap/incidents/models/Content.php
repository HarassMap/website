<?php

namespace Harassmap\Incidents\Models;

use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Content
 *
 * @property int $id
 * @property int $domain_id
 * @property string $content_id
 * @property string $content
 * @property string $link
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Content whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Content whereContentId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Content whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Content whereDomainId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Content whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Content whereLink($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Content whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Content extends Model
{
    use Validation;
    use DomainOptions;

    public $table = 'harassmap_incidents_content';

    const CONTENT_IDS = [
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

    public function getContentIdOptions()
    {
        return self::CONTENT_IDS;
    }
}