<?php

namespace Harassmap\Comments\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use October\Rain\Database\Traits\SoftDelete;
use RainLab\User\Models\User;

/**
 * Harassmap\Comments\Models\Comment
 *
 * @property int $id
 * @property string $topic_id
 * @property int $user_id
 * @property string $content
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereUserId($value)
 * @mixin \Eloquent
 */
class Comment extends Model
{
    use Validation;
    use SoftDelete;

    public $table = 'harassmap_comments_comments';

    protected $dates = ['deleted_at'];

    public $rules = [
        'content' => 'required|max:1000'
    ];

    public $belongsTo = [
        'user' => User::class,
        'topic' => Topic::class
    ];
}