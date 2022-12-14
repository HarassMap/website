<?php namespace Harassmap\Mail\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Exception;
use Flash;
use Harassmap\Incidents\Traits\FilterDomain;
use Harassmap\Mail\Models\MailTemplate;
use Mail;
use RainLab\Translate\Models\Locale;
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

        $this->vars['locales'] = Locale::listEnabled();
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

    public function onTest($recordId)
    {
        try {
            $model = $this->formFindModelObject($recordId);
            $user = $this->user;

            MailTemplate::$testLocale = post('locale');

            Mail::sendTo([$user->email => $user->full_name], $model->code);

            Flash::success(trans('system::lang.mail_templates.test_success'));
        } catch (Exception $ex) {
            Flash::error($ex->getMessage());
        }
    }
}