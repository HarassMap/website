<?php namespace Harassmap\User;

use Harassmap\Incidents\Classes\Analytics;
use Harassmap\Incidents\Models\Domain;
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

            $model->belongsTo['domain'] = Domain::class;

            $model->addFillable([
                'terms', 'marketing', 'notification_incident'
            ]);

            $model->bindEvent('model.beforeCreate', function () use ($model) {
                $domain = Domain::getBestMatchingDomain();
                $model->domain_id = $domain->id;
            });

            $model->bindEvent('model.afterCreate', function () use ($model) {
                Analytics::userCreated($model);
            });

            $model->bindEvent('model.afterUpdate', function () use ($model) {
                Analytics::userEdited($model);
            });

            $model->bindEvent('model.afterDelete', function () use ($model) {
                Analytics::userDeleted($model);
            });
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
                'domain' => [
                    'label' => 'harassmap.incidents::lang.form.domain',
                    'tab' => 'rainlab.user::lang.user.account',
                    'type' => 'relation',
                    'nameFrom' => 'host'
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
                'domain' => [
                    'label' => 'harassmap.incidents::lang.form.domain',
                    'tab' => 'rainlab.user::lang.user.account',
                    'type' => 'relation',
                    'nameFrom' => 'host'
                ],
                'notification_incident' => [
                    'label' => 'Notifications?',
                    'tab' => 'rainlab.user::lang.user.account',
                    'type' => 'checkbox'
                ],
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
                    'label' => 'rainlab.user::lang.user.username',
                    'searchable' => true
                ]
            ]);

            $widget->addColumns([
                'domain' => [
                    'label' => 'harassmap.incidents::lang.form.domain',
                    'relation' => 'domain',
                    'select' => 'host',
                ]
            ]);
        });

    }

}
