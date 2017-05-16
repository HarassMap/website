<?php namespace Harassmap\Domain\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 *
 * @property int $id
 * @property string $host
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereHost($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereUpdatedAt($value)
 * @mixin \Eloquent
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