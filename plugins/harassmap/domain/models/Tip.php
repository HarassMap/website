<?php namespace Harassmap\Domain\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Tip
 *
 * @property int $id
 * @property int $domain_id
 * @property string $tip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Tip whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Tip whereDomainId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Tip whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Tip whereTip($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Tip whereUpdatedAt($value)
 * @mixin \Eloquent
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