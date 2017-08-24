<?php namespace Harassmap\Mail\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Incidents\Traits\DomainOptions;
use Harassmap\Incidents\Traits\FilterDomain;
use System\Models\MailTemplate;

class MailTemplates extends Controller
{
    use FilterDomain;

    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    protected $domain_id = 'domain_id';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Mail', 'harassmap.mail', 'harassmap.mail.templates');
    }

    public function formExtendRefreshData($host, $saveData)
    {
        $template = MailTemplate::findOrMakeTemplate($saveData['code']);

        $saveData['subject'] = $template->subject;
        $saveData['content_html'] = $template->content_html;
        $saveData['content_text'] = $template->content_text;

        return $saveData;
    }
}