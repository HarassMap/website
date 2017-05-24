<?php namespace Harassmap\News;

use Harassmap\News\Components\Post;
use Harassmap\News\Components\Posts;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public $require = ['Harassmap.Domain'];

    public function registerComponents()
    {
        return [
            Posts::class => 'harassmapNewsPosts',
            Post::class => 'harassmapNewsPost',
        ];
    }

    public function registerSettings()
    {
    }
}
