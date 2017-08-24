<?php namespace Harassmap\Mail\Models;

use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Validation;
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
    ];

    public $belongsTo = [
        'domain' => Domain::class
    ];

    public function getCodeOptions()
    {
        $templates = SystemMailTemplate::listAllTemplates();

        return $templates;
    }
}