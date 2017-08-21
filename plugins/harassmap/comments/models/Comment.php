<?php

namespace Harassmap\Comments\Models;

use Harassmap\Incidents\Classes\Analytics;
use Model;
use October\Rain\Database\Traits\SoftDelete;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Facades\Auth;
use RainLab\User\Models\User;


/**
 * Harassmap\Comments\Models\Comment
 *
 * @property int $id
 * @property int $topic_id
 * @property int $user_id
 * @property string $content
 * @property int $flags
 * @property int $approved
 * @property int $user_deleted
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $edited_at
 * @property \Carbon\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment deleted($status)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment incident($status)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereEditedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereFlags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereUserDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Comments\Models\Comment whereUserId($value)
 * @mixin \Eloquent
 */
class Comment extends Model
{
    use Validation;
    use SoftDelete;

    public $table = 'harassmap_comments_comments';

    protected $dates = ['edited_at', 'deleted_at'];

    public $rules = [
        'content' => 'required|max:1000'
    ];

    public $belongsTo = [
        'user' => User::class,
        'topic' => Topic::class
    ];

    public function canEdit()
    {
        $user = Auth::getUser();

        if ($user && $user->id === $this->user_id) {
            return true;
        }

        return false;
    }

    public function beforeSave()
    {
        // remove the flags if the comment is approved
        if ($this->approved) {
            $this->flags = 0;
        }
    }

    public function afterCreate()
    {
        Analytics::commentCreated($this);
    }

    public function afterUpdate()
    {
        Analytics::commentEdited($this);
    }

    public function afterDelete()
    {
        Analytics::commentDeleted($this);
    }

    public function scopeDeleted($query, $status)
    {
        // if status is 1 then we remove interventions
        // otherwise its only interventions
        if($status === "2") {
            $query->onlyTrashed();
        }
    }

    public function scopeIncident($query, $status)
    {
        $query->whereHas('topic', function ($query) use ($status) {
            $query->whereHas('incident', function($query) use ($status) {
                $query->whereIn('id', $status);
            });
        });
    }
}