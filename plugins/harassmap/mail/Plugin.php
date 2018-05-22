<?php namespace Harassmap\Mail;

use Harassmap\Mail\Models\MailTemplate;
use System\Classes\PluginBase;
use Event;

class Plugin extends PluginBase
{

    public $require = ['Harassmap.Incidents'];

    public function register()
    {
        Event::listen('mailer.beforeAddContent', function($mailer, $message, $view, $data) {
            Log::info($mailer);
            Log::info($message);
            Log::info($view);
            Log::info($data);
            MailTemplate::addContentToMailer($message, $view, $data);
            return false;
        });
    }
}
