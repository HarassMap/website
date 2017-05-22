<?php namespace Harassmap\Domain\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 */
class Tip extends Model
{
    use Validation;

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['tip'];
    
    /*
     * Validation
     */
    public $rules = [
    ];

    public $belongsTo = [
        'domain' => Domain::class
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'harassmap_domain_tip';
}