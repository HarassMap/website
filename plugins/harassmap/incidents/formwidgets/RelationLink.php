<?php namespace Harassmap\Incidents\FormWidgets;

use Backend\Classes\FormWidgetBase;

/**
 * RelationLink Form Widget
 */
class RelationLink extends FormWidgetBase
{

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'harassmap_incidents_relation_link';

    /**
     * @inheritDoc
     */
    public function init()
    {
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('relationlink');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $relationName = $this->formField->fieldName;
        $relation = $this->model->{$relationName};

        $this->vars['name'] = $this->formField->getName();
        $this->vars['items'] = $relation;
        $this->vars['model'] = $this->model;
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        $this->addCss('css/relationlink.css', 'harassmap.incidents');
        $this->addJs('js/relationlink.js', 'harassmap.incidents');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return $value;
    }
}
