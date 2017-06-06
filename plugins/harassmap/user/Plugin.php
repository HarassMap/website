<?php namespace Harassmap\User;

use RainLab\User\Models\User;
use System\Classes\PluginBase;

use RainLab\User\Controllers\Users as UsersController;

class Plugin extends PluginBase
{

    public function boot()
    {
        User::extend(function($model) {
            $model->rules['name'] = 'required';
            $model->rules['surname'] = 'required';

            $model->addFillable([
               'terms', 'marketing'
            ]);
        });


        UsersController::extendFormFields(function($widget, $model, $context) {

            if (!$widget->model instanceof \RainLab\User\Models\User) {
                return;
            }

            if ($context != 'update') {
                return;
            }

            $widget->addFields([
                'username' => [
                    'label'   => 'Username',
                    'tab'     => 'rainlab.user::lang.user.account',
                ],
            ], 'primary');

        });

    }

}
