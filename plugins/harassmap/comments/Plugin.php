<?php namespace Harassmap\Comments;

use Harassmap\Comments\Components\Topic;
use Harassmap\Comments\Models\Comment;
use RainLab\User\Models\User;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function boot()
    {
        User::extend(function ($model) {
            $model->hasMany['comments'] = Comment::class;
        });
    }

    public function registerComponents()
    {
        return [
            Topic::class => 'harassmapCommentsTopic',
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'harassmap.comments::mail.admin.flag' => 'Sent to admin when a new report is added'
        ];
    }
}
