<?php namespace Harassmap\Incidents\FormWidgets;

use Backend\Classes\FormWidgetBase;
use October\Rain\Database\Collection;

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

    protected function getRelation($name)
    {
        $parts = explode('.', $name);
        $relation = $this->model;

        foreach ($parts as $part) {
            $relation = $relation->{$part};
        }

        return $relation;
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $relationName = $this->config->relation;
        $relation = $this->getRelation($relationName);

        $this->vars['name'] = $this->formField->getName();
        $this->vars['model'] = $this->model;

        if ($relation instanceof Collection) {
            $this->vars['items'] = $relation;
        } else {
            $this->vars['item'] = $relation;
        }
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
