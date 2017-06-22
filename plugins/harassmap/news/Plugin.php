<?php namespace Harassmap\News;

use Harassmap\News\Components\Post;
use Harassmap\News\Components\PostList;
use Harassmap\News\Components\Posts;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public $require = ['Harassmap.Incidents'];

    public function registerComponents()
    {
        return [
            Posts::class => 'harassmapNewsPosts',
            Post::class => 'harassmapNewsPost',
            PostList::class => 'harassmapNewsPostList',
        ];
    }

    public function registerSettings()
    {
    }
}
