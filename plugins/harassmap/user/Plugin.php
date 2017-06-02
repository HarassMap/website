<?php namespace Harassmap\User;

use RainLab\User\Models\User;
use System\Classes\PluginBase;

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
    }

}
