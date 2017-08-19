<?php

namespace Harassmap\Comments\Models;

use Harassmap\Incidents\Models\Incident;
use Model;
use October\Rain\Database\Traits\SoftDelete;
use October\Rain\Database\Traits\Validation;

/**
 * Harassmap\Comments\Models\Topic
 *
 * @property int $id
 * @property string $code
 * @property Incident $incident
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Topic whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Topic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Topic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Topic whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Topic whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Topic extends Model
{
    use Validation;
    use SoftDelete;

    public $table = 'harassmap_comments_topic';

    protected $dates = ['deleted_at'];

    public $rules = [
    ];

    public $hasMany = [
        'comments' => [Comment::class, 'delete' => true]
    ];

    public $belongsTo = [
        'incident' => [Incident::class, 'key' => 'code', 'otherKey' => 'public_id']
    ];

    /**
     * @param $code
     * @return Topic
     */
    public static function createWithCode($code)
    {
        $topic = new self();
        $topic->code = $code;
        $topic->save();

        return $topic;
    }
}