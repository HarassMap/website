<?php namespace Harassmap\Mail\Models;

use Exception;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Traits\DomainOptions;
use Illuminate\Mail\Message;
use Log;
use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\User\Facades\Auth;
use System\Helpers\View as ViewHelper;
use System\Models\MailTemplate as SystemMailTemplate;
use Twig;

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

    public function getLayoutIdOptions()
    {
        $options = Domain::getDomainIdOptions(false);
        $ids = array_keys($options);

        return MailLayout::whereIn('domain_id', $ids)->get()->lists('host', 'id');
    }

    public function getCodeOptions()
    {
        return SystemMailTemplate::listAllTemplates();
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
            $data['domain'] = $domain;
        }

        // if we have a domain then check to see if we have a template
        if ($domain) {

            // if the domain has an email then send it from there
            if ($domain->email) {
                $message->sender($domain->email, $domain->name);
            }

            $template = self
                ::where('code', '=', $view)
                ->where('domain_id', '=', $domain->id)
                ->orderBy('created_at', 'desc')
                ->first();

        } else {
            $defer = true;
        }

        if ($defer || !$template) {
            SystemMailTemplate::addContentToMailer($message, $view, $data);
        } else {
            self::addDomainContent($message, $view, $data, $template, $domain);
        }

    }

    public static function addDomainContent(Message $message, $view, array $data, MailTemplate $template, Domain $domain)
    {
        // if we didn't get sent a user then use the one logged in
        if (array_key_exists('user', $data)) {
            $user = $data['user'];
        } else {
            $user = Auth::getUser();
        }

        if ($user) {
            $locale = $user->locale;

            if ($locale) {
                // set the mail in the users locale
                $template->translateContext($locale);
            }
        }

        $data['domain'] = [
            'name' => $domain->name
        ];

        $logo = $domain->getEmailLogo();

        if ($logo) {
            try {
                $cid = $message->embed($logo);

                // if the image embedded then set the domain logo
                if (!empty($cid)) {
                    $data['domain']['logo'] = "<img src='$cid'>";
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }

        $globalVars = ViewHelper::getGlobalVars();

        if (!empty($globalVars)) {
            $data = (array)$data + $globalVars;
        }

        $customSubject = $message->getSwiftMessage()->getSubject();
        if (empty($customSubject)) {
            $message->subject(Twig::parse($template->subject, $data));
        }
        $html = Twig::parse($template->content_html, $data);
        if ($template->layout) {
            $html = Twig::parse($template->layout->content_html, [
                    'content' => $html,
                    'css' => $template->layout->content_css
                ] + (array)$data);
        }

        $message->setBody($html, 'text/html');

        /*
         * Text contents
         */
        if (strlen($template->content_text)) {
            $text = Twig::parse($template->content_text, $data);
            if ($template->layout) {
                $text = Twig::parse($template->layout->content_text, [
                        'content' => $text
                    ] + (array)$data);
            }

            $message->addPart($text, 'text/plain');
        }
    }
}