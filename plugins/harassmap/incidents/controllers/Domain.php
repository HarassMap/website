<?php

namespace Harassmap\Incidents\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Harassmap\Incidents\Models\Domain as DomainModel;
use Harassmap\Incidents\Traits\FilterDomain;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Domain extends Controller
{
    use FilterDomain;

    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Harassmap.Incidents', 'harassmap.incidents.domain', 'harassmap.incidents.domain.domain');
    }

    protected $domain_id = 'id';

    protected function findDomain($id)
    {
        return DomainModel::find($id);
    }

    public function create($context = null)
    {
        // if the user has permission then stop here
        if (!$this->hasPermission()) {
            throw new AccessDeniedHttpException();
        }

        return $this->asExtension('FormController')->create($context);
    }

    public function formExtendFields($form)
    {
        $model = $form->model;
        $colours = $model->colours ? $model->colours : [];

        foreach ($model->colourTypes as $colour => $property) {
            $name = 'colours_' . $colour;
            $label = implode(' ', array_map('ucfirst', explode('_', $colour)));

            // setting the value for this fake field
            $default = '';
            if (array_key_exists($colour, $colours)) {
                $default = $colours[$colour]['value'];
            }
            $model->{$name} = $default;

            $form->addTabFields([
                $name => [
                    'label' => $label,
                    'type' => 'colorpicker',
                    'tab' => 'Colours'
                ]
            ]);
        }

        $form->addTabFields([
            'resetColours' => [
                'label' => 'Reset All Colours?',
                'type' => 'checkbox',
                'tab' => 'Colours'
            ]
        ]);
    }
}