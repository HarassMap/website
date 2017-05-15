<?php namespace Harassmap\Domain\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 */
class Domain extends Model
{
    use Validation;
    
    /*
     * Validation
     */
    public $rules = [
    ];

    /*
     * A domain can be associated with many content blocks
     */
    public $hasMany = [
        'content' => Content::class
    ];

    /*
     * Attachments
     */
    public $attachOne = [
        'logo' => 'System\Models\File'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'harassmap_domain_domain';
}