<?php namespace Harassmap\MailChimp;

use Harassmap\Incidents\Controllers\Domain as DomainController;
use Harassmap\MailChimp\Components\Signup;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function boot()
    {
        // extend the user edit form to allow domain allocation
        DomainController::extendFormFields(function ($form, $model, $context) {

            $form->addTabFields([
                'mailchimp_api' => [
                    'label' => 'MailChimp API key',
                    'commentAbove' => 'Get an API Key from http://admin.mailchimp.com/account/api/',
                    'tab' => 'MailChimp',
                    'span' => 'auto',
                ],
                'mailchimp_list' => [
                    'label' => 'MailChimp List ID',
                    'commentAbove' => 'The ID for your mailchimp list',
                    'tab' => 'MailChimp',
                    'span' => 'auto',
                ],
            ]);

        });
    }

    public function registerComponents()
    {
        return [
            Signup::class => 'harassmapMailChimp',
        ];
    }
}
