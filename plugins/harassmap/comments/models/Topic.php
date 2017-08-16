<?php

namespace Harassmap\Comments\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Harassmap\Comments\Models\Topic
 *
 * @property string $id
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
}