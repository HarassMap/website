<?php namespace Harassmap\Domain\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 */
class Content extends Model
{
    use Validation;

    const CONTENT_IDS = [
        'homepage' => 'homepage'
    ];

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['content'];

    /*
     * Validation
     */
    public $rules = [
    ];

    /*
     * A content block can belong to a domain
     */
    public $belongsTo = [
        'domain' => Domain::class
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'harassmap_domain_content';

    public function getContentIdOptions() {
        return self::CONTENT_IDS;
    }
}