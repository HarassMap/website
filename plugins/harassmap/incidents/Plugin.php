<?php namespace Harassmap\Incidents;

use Backend\Controllers\Users as BackendUsersController;
use Backend\Models\User as BackendUserModel;
use BackendAuth;
use Harassmap\Incidents\Components\ContentBlock;
use Harassmap\Incidents\Components\Domain;
use Harassmap\Incidents\Components\ExpressSupport;
use Harassmap\Incidents\Components\IncidentAccount;
use Harassmap\Incidents\Components\Report;
use Harassmap\Incidents\Components\ReportComments;
use Harassmap\Incidents\Components\ReportCommentsTopic;
use Harassmap\Incidents\Components\ReportIncident;
use Harassmap\Incidents\Components\ReportIntervention;
use Harassmap\Incidents\Components\ReportMap;
use Harassmap\Incidents\Components\ReportTable;
use Harassmap\Incidents\Components\ReportView;
use Harassmap\Incidents\Components\Tip;
use Harassmap\Incidents\Components\Tips;
use Harassmap\Incidents\Components\UserAPI;
use Harassmap\Incidents\Components\UserReports;
use Harassmap\Incidents\Console\MigrateCommand;
use Harassmap\Incidents\Models\API;
use Harassmap\Incidents\Models\Domain as DomainModel;
use Harassmap\Incidents\Models\Incident;
use RainLab\User\Models\User as UserModel;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function boot()
    {
        // extend the user class to give it a list of domains
        BackendUserModel::extend(function ($model) {
            $model->belongsToMany['domains'] = [DomainModel::class, 'table' => 'harassmap_incidents_domain_user'];
        });

        UserModel::extend(function ($model) {
            $model->hasMany['incidents'] = Incident::class;
            $model->hasOne['api'] = API::class;
        });

        // extend the user edit form to allow domain allocation
        BackendUsersController::extendFormFields(function ($form, $model, $context) {

            $user = BackendAuth::getUser();

            if ($user->isSuperUser() || $user->hasPermission(['harassmap.incidents.domain.manage_user_domains'])) {
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
            ReportIntervention::class => 'harassmapReportIntervention',
            ContentBlock::class => 'harassmapDomainContentBlock',
            Domain::class => 'harassmapDomain',
            Tip::class => 'harassmapTip',
            Tips::class => 'harassmapTips',
            IncidentAccount::class => 'harassmapIncidentAccount',
            UserReports::class => 'harassmapUserReports',
            ReportView::class => 'harassmapUserReport',
            ExpressSupport::class => 'harassmapExpressSupport',
            ReportComments::class => 'harassmapReportComments',
            ReportCommentsTopic::class => 'harassmapReportCommentsTopic',
            ReportMap::class => 'harassmapReportMap',
            Report::class => 'harassmapReport',
            ReportTable::class => 'harassmapReportTable',
            UserAPI::class => 'harassmapUserAPI',
        ];
    }

    public function register()
    {
        $this->registerConsoleCommand('harassmap.migrate', MigrateCommand::class);
    }
}
