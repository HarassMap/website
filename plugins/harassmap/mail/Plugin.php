<?php namespace Harassmap\Mail;

use Harassmap\Mail\Models\MailTemplate;
use System\Classes\PluginBase;
use Event;

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
