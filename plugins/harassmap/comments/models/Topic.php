<?php

namespace Harassmap\Comments\Models;

use Harassmap\Incidents\Models\Incident;
use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Harassmap\Comments\Models\Topic
 *
 * @property int $id
 * @property string $code
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Topic whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Topic whereId($value)
 * @mixin \Eloquent
 */
class Topic extends Model
{
    use Validation;

    public $table = 'harassmap_comments_topic';

    public $timestamps = false;

    public $rules = [
    ];

    public $hasMany = [
        'comments' => Comment::class
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