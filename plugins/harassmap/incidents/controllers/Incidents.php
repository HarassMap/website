<?php namespace Harassmap\Incidents\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Traits\FilterDomain;
use Illuminate\Http\Response;

class Incidents extends Controller
{
    use FilterDomain;

    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Incidents', 'harassmap.incidents', 'harassmap.incidents.incidents');
    }

    public function download()
    {
        $checked = get('checked');

        $results = Incident::with('location')->with('intervention');

        if (!empty($checked)) {
            $results = $results->whereIn('id', explode(',', $checked));
        }

        $results = $results->get()->toJson();

        return new Response($results, 200, array(
            'Content-type' => 'application/json',
            'Content-Disposition' => 'attachment;filename=incident_data.json',
            'Content-Length' => strlen($results)
        ));
    }
}