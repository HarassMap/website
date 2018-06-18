<?php namespace Harassmap\Incidents\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Incidents\Models\Incident;
use Harassmap\Incidents\Traits\FilterDomain;
use Illuminate\Http\Response;
use League\Csv\Writer;
use Harassmap\Incidents\Models\Domain;
use Log;

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

    protected $domain_id = 'domain_id';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Incidents', 'harassmap.incidents', 'harassmap.incidents.incidents');
    }

    protected function findDomain($id)
    {
        $incident = Incident::find($id);

        return $incident->domain;
    }

    public function download()
    {
        $checked = get('checked');
        $domain = Domain::getBestMatchingDomain();

        $results = Incident::with('location')
            ->with('intervention')
            ->with('categories')
            ->with('domain')
            ->with('intervention.assistance');


        if (!empty($checked)) {
            $results = $results->where('domain_id', $domain->id)->whereIn('id', explode(',', $checked));
        }

        $result = $this->createCsv($results->where('domain_id', $domain->id)->get());

        return new Response($result, 200, array(
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment;filename=incident_data.csv',
            'Content-Length' => strlen($result)
        ));
    }

    protected function createCsv($collection)
    {
        $header = [
            'id',
            'public_id',
            'domain',
            'description',
            'date',
            'address',
            'city',
            'position',
            'role',
            'categories',
            'intervention',
            'form',
            'reported'
        ];

        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->setOutputBOM(Writer::BOM_UTF8);
        $writer->insertOne($header);

        foreach ($collection as $item) {

            $categories = $item->categories->map(function ($category) {
                return $category->title;
            })->implode(', ');

            if ($item->intervention) {
                $intervention = $item->intervention->assistance->map(function ($assistance) {
                    return $assistance->title;
                })->implode(', ');
            } else {
                $intervention = '';
            }

            $writer->insertOne([
                'id' => $item->id,
                'public_id' => $item->public_id,
                'domain' => $item->domain->host,
                'description' => $item->description,
                'date' => $item->date,
                'address' => !empty($item->location->address) ? ($item->location->address) : 'nil',
                'city' => !empty($item->location->city) ? ($item->location->city) : 'nil',
                'position' => (!empty($item->location->lat) && !empty($item->location->lng)) ? ($item->location->lat . ',' . $item->location->lng) : 'nil',
                'role' => $item->role->name,
                'categories' => $categories,
                'intervention' => !empty($intervention) ? ($intervention) : 'nil',
                'form' => $item->is_intervention ? 'intervention' : 'incident',
                'date_reported' => $item->created_at
            ]);
        }

        return (string)$writer;
    }

     public function listExtendQuery($query)
     {
        $domain = Domain::getBestMatchingDomain();

        if ($domain->host != '*') {
            $query->where('domain_id', $domain->id);
        }
     }
}
