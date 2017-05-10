<?php namespace Harassmap\Harassmap\Models;

use October\Rain\Database\Model;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 */
class Category extends Model
{
    use Validation;

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['title', 'description'];

    /*
     * Validation
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'harassmap_harassmap_category';
}