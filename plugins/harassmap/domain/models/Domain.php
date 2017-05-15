<?php namespace Harassmap\Domain\Models;

use Model;

/**
 * Model
 */
class Domain extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /*
     * Validation
     */
    public $rules = [
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