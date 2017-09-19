<?php namespace Harassmap\News\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Incidents\Traits\FilterDomain;
use Harassmap\News\Models\Posts as PostModel;

class Posts extends Controller
{
    use FilterDomain;

    public $implement = ['Backend\Behaviors\ListController','Backend\Behaviors\FormController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.News', 'harassmap.news');
    }

    protected $domain_id = 'domain_id';

    protected function findDomain($id)
    {
        $post = PostModel::find($id);

        return $post->domain;
    }
}