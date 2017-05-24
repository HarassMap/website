<?php

namespace Harassmap\News\Models;

use Harassmap\Domain\Models\Domain;
use Model;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 */
class Posts extends Model
{
    use Validation;
    use Sluggable;

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $table = 'harassmap_news_posts';

    public $rules = [
        'title' => 'required',
        'slug' => ['required', 'regex:/^[a-z0-9\/\:_\-\*\[\]\+\?\|]*$/i', 'unique:harassmap_news_posts']
    ];

    protected $slugs = [
        'slug' => 'title'
    ];

    public $translatable = [
        'title',
        ['slug', 'index' => true],
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