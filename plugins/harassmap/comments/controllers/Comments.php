<?php namespace Harassmap\Comments\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Comments\Models\Comment;
use Harassmap\Incidents\Classes\Analytics;
use BackendAuth;

class Comments extends Controller
{
    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Comments', 'harassmap.comments.menu');
    }

    public function formExtendQuery($query)
    {
        $query->withTrashed();
    }

    public function formAfterUpdate(Comment $comment)
    {
        $user = BackendAuth::getUser();

        Analytics::commentEdited($comment, $user);
    }

    public function formAfterDelete(Comment $comment)
    {
        $user = BackendAuth::getUser();

        Analytics::commentDeleted($comment, $user);
    }
}