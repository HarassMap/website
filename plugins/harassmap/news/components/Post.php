<?php

namespace Harassmap\News\Components;

use Cms\Classes\ComponentBase;
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

    public function onRender()
    {

        // get the slug from the url
        $slug = $this->param('slug');

        $post = PostsModel::whereSlug($slug)->first();

        $this->page['post'] = $post;
    }

}
