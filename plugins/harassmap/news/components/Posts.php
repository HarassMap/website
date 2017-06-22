<?php

namespace Harassmap\News\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Domain;
use Harassmap\News\Models\Posts as PostsModel;

class Posts extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Posts',
            'description' => 'Gets a list of the most recent posts.'
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
            'listPage' => [
                'title' => 'harassmap.news::lang.post_list.page_name',
                'description' => 'harassmap.news::lang.post_list.page_help',
                'type' => 'dropdown',
                'group' => 'Links',
            ],
        ];
    }

    public function onRender()
    {
        $domain = Domain::getBestMatchingDomain();

        $this->page['postPage'] = $this->property('postPage');
        $this->page['listPage'] = $this->property('listPage');

        if ($domain) {
            $this->page['posts'] = PostsModel
                ::where('domain_id', '=', $domain->id)
                ->where('published_at', '<', Carbon::now())
                ->orderBy('published_at', 'desc')
                ->limit(4)
                ->get();
        }
    }

    public function getPostPageOptions()
    {
        return $this->getCMSPageList();
    }

    public function getListPageOptions()
    {
        return $this->getCMSPageList();
    }

    protected function getCMSPageList()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

}
