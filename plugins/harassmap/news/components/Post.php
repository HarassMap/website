<?php

namespace Harassmap\News\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\News\Models\Posts as PostsModel;

class Post extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Post',
            'description' => 'Displays one post in its entirety.'
        ];
    }

    public function defineProperties()
    {
        return [
            'listPage' => [
                'title' => 'harassmap.news::lang.post_list.page_name',
                'description' => 'harassmap.news::lang.post_list.page_help',
                'type' => 'dropdown',
                'group' => 'Links',
            ],
        ];
    }

    public function getListPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRender()
    {

        // get the slug from the url
        $slug = $this->param('slug');

        $post = PostsModel::whereSlug($slug)->first();

        $this->page['post'] = $post;
        $this->page['listPage'] = $this->property('listPage');
    }

}
