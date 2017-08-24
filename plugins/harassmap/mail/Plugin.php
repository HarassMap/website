<?php namespace Harassmap\Mail;

use System\Classes\PluginBase;
use Event;
use System\Models\MailTemplate;

class Plugin extends PluginBase
{
    public function register()
    {
        Event::listen('mailer.beforeAddContent', function($mailer, $message, $view, $data) {
            MailTemplate::addContentToMailer($message, $view, $data);
            return false;
        });
    }
}
