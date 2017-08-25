<?php namespace Harassmap\Mail\Models;

use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Facades\Auth;
use System\Models\MailTemplate as SystemMailTemplate;

/**
 * Harassmap\Mail\Models\MailTemplate
 *
 * @property int $id
 * @property int $domain_id
 * @property string $code
 * @property string $subject
 * @property string $content_html
 * @property string $content_text
 * @property int|null $layout_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailTemplate whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailTemplate whereContentHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailTemplate whereContentText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailTemplate whereDomainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailTemplate whereLayoutId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailTemplate whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailTemplate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MailTemplate extends Model
{
    use Validation;
    use DomainOptions;

    public $table = 'harassmap_mail_templates';

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = [
        'subject',
        'content_html',
        'content_text'
    ];

    public $rules = [
        'code' => 'required',
        'layout' => 'required',
        'domain' => 'required'
    ];

    public $belongsTo = [
        'domain' => Domain::class,
        'layout' => MailLayout::class
    ];

    public function getCodeOptions()
    {
        $templates = SystemMailTemplate::listAllTemplates();

        return $templates;
    }

    /**
     * Check to see if we have a domain specific mail template
     * if not then just defer to the normal mailer
     * @param $message
     * @param $view
     * @param $data
     */
    public static function addContentToMailer($message, $view, $data)
    {
        // defer to the system mailer?
        $defer = false;

        // if we didn't get sent a user then use the one logged in
        if (array_key_exists('domain', $data)) {
            $domain = $data['domain'];
        } else {
            $domain = Domain::getBestMatchingDomain();
        }

        // if we have a domain then check to see if we have a template
        if ($domain) {
            $template = self
                ::where('code', '=', $view)
                ->where('domain_id', '=', $domain->id);

            if (!$template) {
                $defer = true;
            }
        } else {
            $defer = true;
        }

        // if we didn't get sent a user then use the one logged in
        if (array_key_exists('user', $data)) {
            $user = $data['user'];
        } else {
            $user = Auth::getUser();
        }

        // if there is no user then
        if (!$user) {
            $defer = true;
        }

        // todo: always defer until we complete this functionality
        $defer = true;

        if ($defer) {
            SystemMailTemplate::addContentToMailer($message, $view, $data);
        } else {


        }

    }
}