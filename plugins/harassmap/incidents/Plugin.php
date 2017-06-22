<?php namespace Harassmap\Incidents;

use Harassmap\Incidents\Components\ReportIncident;
use System\Classes\PluginBase;
use Backend\Controllers\Users as BackendUsersController;
use Backend\Models\User as BackendUserModel;
use BackendAuth;
use Harassmap\Incidents\Models\Domain as DomainModel;

class Plugin extends PluginBase
{

    public function boot()
    {
        // extend the user class to give it a list of domains
        BackendUserModel::extend(function ($model) {
            $model->belongsToMany['domains'] = [DomainModel::class, 'table' => 'harassmap_incidents_domain_user'];
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
            ReportIncident::class => 'harassmapReportIncident',
        ];
    }

    public function registerSettings()
    {
    }
}
