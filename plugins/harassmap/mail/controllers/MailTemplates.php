<?php namespace Harassmap\Mail\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Incidents\Traits\FilterDomain;
use Harassmap\Mail\Models\MailTemplate;
use System\Models\MailTemplate as SystemMailTemplate;

class MailTemplates extends Controller
{
    use FilterDomain;
    protected $domain_id = 'domain_id';

    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';


    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Mail', 'harassmap.mail', 'harassmap.mail.templates');
    }

    protected function findDomain($id)
    {
        $content = MailTemplate::find($id);

        return $content->domain;
    }

    public function formExtendRefreshData($host, $saveData)
    {
        $template = SystemMailTemplate::findOrMakeTemplate($saveData['code']);

        $saveData['subject'] = $template->subject;
        $saveData['content_html'] = $template->content_html;
        $saveData['content_text'] = $template->content_text;

        return $saveData;
    }
}