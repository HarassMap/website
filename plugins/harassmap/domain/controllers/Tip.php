<?php namespace Harassmap\Domain\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Tip extends Controller
{
    public $implement = ['Backend\Behaviors\ListController','Backend\Behaviors\FormController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Domain', 'harassmap.domain', 'harassmap.domain.tips');
    }
}