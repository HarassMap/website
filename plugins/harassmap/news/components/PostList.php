<?php

namespace Harassmap\News\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Domain\Models\Domain;
use Harassmap\News\Models\Posts as PostsModel;

class PostList extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Post List',
            'description' => 'A paginated list of all the news.'
        ];
    }

    public function defineProperties()
    {
        return [
            'postPage' => [
                'title' => 'harassmap.news::lang.post.page_name',
                'description' => 'harassmap.news::lang.post.page_help',
                'type' => 'dropdown',
                'group' => 'Links',
            ],
        ];
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRender()
    {
        $domain = Domain::getBestMatchingDomain();

        $this->page['postPage'] = $this->property('postPage');

        if ($domain) {
            $this->page['posts'] = PostsModel
                ::where('domain_id', '=', $domain->id)
                ->where('published_at', '<', Carbon::now())
                ->orderBy('published_at', 'desc')
                ->paginate(8);
        }
    }

}
