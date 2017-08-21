<?php namespace Harassmap\User;

use RainLab\User\Controllers\Users as UsersController;
use RainLab\User\Models\User;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function boot()
    {
        User::extend(function ($model) {
            $model->rules['name'] = 'required';
            $model->rules['surname'] = 'required';

            $model->addFillable([
                'terms', 'marketing', 'notification_incident'
            ]);
        });

        UsersController::extendFormFields(function ($widget, $model, $context) {

            if (!$widget->model instanceof \RainLab\User\Models\User) {
                return;
            }

            if ($context != 'update') {
                return;
            }

            $widget->removeField('forum_member[username]');
            $widget->removeField('forum_member[is_moderator]');
            $widget->removeField('forum_member[is_banned]');

            $widget->addFields([
                'username' => [
                    'label' => 'Username',
                    'tab' => 'rainlab.user::lang.user.account',
                ],
            ], 'primary');

        });

        UsersController::extendFormFields(function ($widget, $model, $context) {

            if (!$widget->model instanceof \RainLab\User\Models\User) {
                return;
            }

            if ($context != 'preview') {
                return;
            }

            $widget->addFields([
                'username' => [
                    'label' => 'Username',
                    'tab' => 'rainlab.user::lang.user.account',
                ],
            ], 'primary');

            $widget->addFields([
                'notification_incident' => [
                    'label' => 'Notifications?',
                    'tab' => 'rainlab.user::lang.user.account',
                    'type' => 'checkbox'
                ],
            ], 'primary');

            $widget->addFields([
                'incidents' => [
                    'label' => 'Incidents',
                    'type' => 'relation_link',
                    'tab' => 'Reports',
                    'relation' => 'incidents'
                ],
            ], 'primary');

        });

        UsersController::extendListColumns(function ($widget, $model) {
            if (!$model instanceof \RainLab\User\Models\User) {
                return;
            }

            $widget->removeColumn('forum_member_username');

            $widget->addColumns([
                'username' => [
                    'label'      => 'rainlab.user::lang.user.username',
                    'searchable' => true
                ]
            ]);
        });

    }

}
