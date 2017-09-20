<?php namespace Harassmap\Incidents;

use Backend\Controllers\Users as BackendUsersController;
use Backend\Models\User as BackendUserModel;
use BackendAuth;
use Event;
use Harassmap\Incidents\Classes\Mailer;
use Harassmap\Incidents\Components\ContentBlock;
use Harassmap\Incidents\Components\Domain;
use Harassmap\Incidents\Components\ExpressSupport;
use Harassmap\Incidents\Components\IncidentAccount;
use Harassmap\Incidents\Components\IncidentResetPassword;
use Harassmap\Incidents\Components\Notifications;
use Harassmap\Incidents\Components\Report;
use Harassmap\Incidents\Components\ReportIncident;
use Harassmap\Incidents\Components\ReportIntervention;
use Harassmap\Incidents\Components\ReportMap;
use Harassmap\Incidents\Components\ReportStory;
use Harassmap\Incidents\Components\ReportTable;
use Harassmap\Incidents\Components\ReportView;
use Harassmap\Incidents\Components\Tip;
use Harassmap\Incidents\Components\UserAPI;
use Harassmap\Incidents\Components\UserMenu;
use Harassmap\Incidents\Components\UserReports;
use Harassmap\Incidents\Console\MigrateCommand;
use Harassmap\Incidents\FormWidgets\RelationLink;
use Harassmap\Incidents\Models\API;
use Harassmap\Incidents\Models\Domain as DomainModel;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Models\Notification;
use Harassmap\Incidents\Widgets\PageList;
use RainLab\Pages\Controllers\Index;
use RainLab\User\Models\User as UserModel;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;

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
            $model->hasMany['notifications'] = Notification::class;
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

        // remove the domain filter if the user doesn't have the permission for it
        Event::listen('backend.filter.extendScopes', function ($widget) {
            $user = BackendAuth::getUser();

            if (!($user->isSuperUser() || $user->hasPermission(['harassmap.incidents.domain.manage_user_domains']))) {
                $widget->removeScope('domain');
            }

        });

        Event::listen('backend.form.extendFields', function ($widget) {

            if (!($widget->getController() instanceof Index)) {
                return;
            }

            $widget->addTabFields([
                'viewBag[domain]' => [
                    'label' => 'Domain',
                    'tab' => 'cms::lang.editor.settings',
                    'type' => 'dropdown',
                    'options' => DomainModel::getDomainIdOptions()
                ]
            ]);

        });

        // extend the static page index to create a new PageList widget
        Index::extend(function ($controller) {
            $controller->addViewPath(__DIR__ . '/views/rainlab/pages/index');
            new PageList($controller, 'domainPageList');
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
            IncidentAccount::class => 'harassmapIncidentAccount',
            IncidentResetPassword::class => 'harassmapIncidentResetPassword',
            UserReports::class => 'harassmapUserReports',
            ReportView::class => 'harassmapUserReport',
            ExpressSupport::class => 'harassmapExpressSupport',
            ReportMap::class => 'harassmapReportMap',
            Report::class => 'harassmapReport',
            ReportTable::class => 'harassmapReportTable',
            UserAPI::class => 'harassmapUserAPI',
            ReportStory::class => 'harassmapStory',
            Notifications::class => 'harassmapNotifications',
            UserMenu::class => 'harassmapUserMenu',
        ];
    }

    public function registerPageSnippets()
    {
        return [
            ContentBlock::class => 'harassmapDomainContentBlock',
        ];
    }

    public function register()
    {
        $this->registerConsoleCommand('harassmap.migrate', MigrateCommand::class);
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'HarassMap Settings',
                'description' => '',
                'category' => SettingsManager::CATEGORY_MISC,
                'icon' => 'icon-cog',
                'class' => 'Harassmap\Incidents\Models\Settings',
                'order' => 500,
                'permissions' => ['harassmap.incidents.access_settings']
            ]
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'harassmap.incidents::mail.admin.report' => 'Sent to admin when a new report is added',
            'harassmap.incidents::mail.user.notifications' => 'Tells users they have new notifications',
        ];
    }

    public function registerSchedule($schedule)
    {
        // every day at 9pm send emails to people with support
        $schedule->call(function () {
            Mailer::sendNotificationMail();
        })->dailyAt('20:00');
    }

    public function registerFormWidgets()
    {
        return [
            RelationLink::class => 'relation_link'
        ];
    }
}
