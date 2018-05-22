<?php namespace Harassmap\Mail;

use Harassmap\Mail\Models\MailTemplate;
use System\Classes\PluginBase;
use Event;
use Harassmap/Mail/Logger;

class Plugin extends PluginBase
{

    public $require = ['Harassmap.Incidents'];

    public function register()
    {
        Event::listen('mailer.beforeAddContent', function($mailer, $message, $view, $data) {
            Logger::info($mailer);
            Logger::info($message);
            Logger::info($view);
            Logger::info($data);
            MailTemplate::addContentToMailer($message, $view, $data);
            return false;
        });
    }
}
