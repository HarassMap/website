<?php

namespace Harassmap\Domain\Models;

use Harassmap\Domain\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Content
 *
 * @property int $id
 * @property int $domain_id
 * @property string $content_id
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Content whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Content whereContentId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Content whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Content whereDomainId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Content whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Content whereUpdatedAt($value)
 * @mixin \Eloquent
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