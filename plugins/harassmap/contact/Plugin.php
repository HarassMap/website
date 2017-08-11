<?php namespace Harassmap\Contact;

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

        Event::listen('backend.list.extendQuery', function ($query) {

            if (!$query->getController() instanceof Messages) {
                return;
            }


        });
    }

}
