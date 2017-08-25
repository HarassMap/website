<?php namespace Harassmap\Contact;

use BackendAuth;
use Event;
use Harassmap\Contact\Classes\Mailer;
use Harassmap\Incidents\Models\Domain;
use Janvince\SmallContactform\Controllers\Messages;
use JanVince\SmallContactForm\Models\Message;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function boot()
    {
        Message::extend(function ($model) {
            $model->belongsTo['domain'] = Domain::class;

            $model->bindEvent('model.beforeCreate', function () use ($model) {
                $domain = Domain::getBestMatchingDomain();

                $model->domain_id = $domain->id;
            });

            // after a message has been created send an email to domain admins
            $model->bindEvent('model.afterCreate', function () use ($model) {
                Mailer::sendContactEmail($model);
            });
        });

        Messages::extendListColumns(function ($widget, $model) {

            $widget->addColumns([
                'domain' => [
                    'label' => 'Domain',
                    'relation' => 'domain',
                    'select' => 'host'
                ]
            ]);
        });

        Event::listen('backend.list.extendQuery', function ($widget, $query) {

            if (!$widget->getController() instanceof Messages) {
                return;
            }

            $user = BackendAuth::getUser();

            // if the user is a super use then stop here
            if ($user->isSuperUser() || $user->hasPermission(['harassmap.incidents.domain.manage_domains'])) {
                return;
            }

            $domains = $user->domains;

            // if the user has no domains then show nothing
            if ($domains->isEmpty()) {
                $query->where('id', '=', -1);
            }

            $domain_ids = [];

            foreach ($domains as $domain) {
                $domain_ids[] = $domain->id;
            }

            $query->whereIn($this->domain_id, $domain_ids);

        });
    }

}
