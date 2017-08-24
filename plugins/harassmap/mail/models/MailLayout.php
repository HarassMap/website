<?php namespace Harassmap\Mail\Models;

use Model;
use October\Rain\Database\Traits\Validation;


/**
 * Harassmap\Mail\Models\MailLayout
 *
 * @property int $id
 * @property string $name
 * @property string $content_html
 * @property string $content_text
 * @property string $content_css
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailLayout whereContentCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailLayout whereContentHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailLayout whereContentText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailLayout whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailLayout whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailLayout whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Mail\Models\MailLayout whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MailLayout extends Model
{
    use Validation;

    public $table = 'harassmap_mail_layout';

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = [
        'content_html',
        'content_text',
        'content_css',
    ];

    public $rules = [
        'name' => 'required'
    ];
}