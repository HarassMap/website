<?php namespace Harassmap\DomainContent\Models;

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
     * Attachments
     */
    public $attachOne = [
        'logo' => 'System\Models\File'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'harassmap_domaincontent_domain';
}