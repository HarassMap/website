<?php namespace Harassmap\Comments;

use Harassmap\Comments\Models\Comment;
use System\Classes\PluginBase;
use RainLab\User\Models\User;

class Plugin extends PluginBase
{
    public function boot()
    {
        User::extend(function ($model) {
            $model->hasMany['comments'] = Comment::class;
        });
    }
}
