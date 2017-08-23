<?php

namespace Harassmap\News\Models;

use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Traits\DomainOptions;
use Model;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;

/**
 * Harassmap\News\Models\Posts
 *
 * @property int $id
 * @property int $domain_id
 * @property string $title
 * @property string $slug
 * @property string $intro
 * @property string $content
 * @property int $hide_image
 * @property string|null $published_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\News\Models\Posts whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\News\Models\Posts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\News\Models\Posts whereDomainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\News\Models\Posts whereHideImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\News\Models\Posts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\News\Models\Posts whereIntro($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\News\Models\Posts wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\News\Models\Posts whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\News\Models\Posts whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\News\Models\Posts whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Posts extends Model
{
    use Validation;
    use Sluggable;
    use DomainOptions;

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $table = 'harassmap_news_posts';

    public $rules = [
        'title' => 'required',
        'intro' => 'required',
        'content' => 'required',
        'domain' => 'required',
        'slug' => ['required', 'regex:/^[a-z0-9\/\:_\-\*\[\]\+\?\|]*$/i', 'unique:harassmap_news_posts']
    ];

    protected $slugs = [
        'slug' => 'title'
    ];

    public $translatable = [
        'title',
        'intro',
        'content'
    ];

    public $belongsTo = [
        'domain' => Domain::class
    ];

    public $attachOne = [
        'image' => 'System\Models\File'
    ];

    public function beforeCreate()
    {
        if ($this->published_at == '') {
            $this->published_at = date('Y-m-d H:i:00');
        }
    }
}