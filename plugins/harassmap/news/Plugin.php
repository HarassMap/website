<?php namespace Harassmap\News;

use Harassmap\News\Components\Login;
use Harassmap\News\Components\PostList;
use Harassmap\News\Components\Posts;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public $require = ['Harassmap.Domain'];

    public function registerComponents()
    {
        return [
            Posts::class => 'harassmapNewsPosts',
            Login::class => 'harassmapNewsPost',
            PostList::class => 'harassmapNewsPostList',
        ];
    }

    public function registerSettings()
    {
    }
}
