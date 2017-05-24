<?php

namespace Harassmap\Domain;

use Backend\Controllers\Users as BackendUsersController;
use Backend\Models\User as BackendUserModel;
use Harassmap\Domain\Components\ContentBlock;
use Harassmap\Domain\Components\Domain;
use Harassmap\Domain\Components\Tip;
use Harassmap\Domain\Components\Tips;
use Harassmap\Domain\Models\Domain as DomainModel;
use System\Classes\PluginBase;
use BackendAuth;

class Plugin extends PluginBase
{

    public function boot()
    {

        // extend the user class to give it a list of domains
        BackendUserModel::extend(function ($model) {
            $model->belongsToMany['domains'] = [DomainModel::class, 'table' => 'harassmap_domain_user'];
        });

        // extend the user edit form to allow domain allocation
        BackendUsersController::extendFormFields(function ($form, $model, $context) {

            $user = BackendAuth::getUser();

            if ($user->isSuperUser() || $user->hasPermission(['harassmap.domain.manage_user_domains'])) {
                $form->addTabFields([
                    'domains' => [
                        'label' => 'Domain',
                        'commentAbove' => 'Allow this user to edit certain domains',
                        'type' => 'relation',
                        'select' => 'host',
                        'tab' => 'Domain'
                    ]
                ]);
            }

        });

    }

    public function registerComponents()
    {
        return [
            ContentBlock::class => 'harassmapDomainContentBlock',
            Domain::class => 'harassmapDomain',
            Tip::class => 'harassmapTip',
            Tips::class => 'harassmapTips'
        ];
    }

    public function registerSettings()
    {
    }
}
