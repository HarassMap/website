<?php namespace Harassmap\Mail\Models;

use Model;

/**
 * Model
 */
class MailTemplate extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /*
     * Validation
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'harassmap_mail_templates';
}