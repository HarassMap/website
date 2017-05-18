<?php

namespace Harassmap\Domain;

use Harassmap\Domain\Components\ContentBlock;
use Harassmap\Domain\Components\Domain;
use Harassmap\Domain\Models\Domain as DomainModel;
use RainLab\User\Models\User;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function boot()
    {

        // extend the user class to give it a list of domains
        User::extend(function ($model) {
            $model->belongsToMany['domains'] = [DomainModel::class, 'table' => 'harassmap_domain_user'];
        });

    }

    public function registerComponents()
    {
        return [
            ContentBlock::class => 'domainContentBlock',
            Domain::class => 'domain'
        ];
    }

    public function registerSettings()
    {
    }
}
