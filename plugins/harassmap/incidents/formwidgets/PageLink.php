<?php namespace Harassmap\Incidents\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Cms\Classes\Page;
use Lang;
use RainLab\Pages\Classes\Page as StaticPage;

/**
 * PageLink Form Widget
 */
class PageLink extends FormWidgetBase
{

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'harassmap_incidents_page_link';

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
        return $this->makePartial('pagelink');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['id'] = $this->getId();
        $this->vars['name'] = $this->getFieldName();

        $parts = explode('||', $this->getLoadValue());

        if (count($parts) < 2) {
            array_unshift($parts, 0);
        }

        $this->vars['type'] = $parts[0];
        $this->vars['value'] = $parts[1];

        $this->vars['urls'] = $this->getUrlOptions();
        $this->vars['pages'] = $this->getStaticOptions();
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        $this->addCss('css/pagelink.css', 'harassmap.incidents');
        $this->addJs('js/pagelink.js', 'harassmap.incidents');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        $type = $value[0];
        $value = $value[$type];

        return $type . '||' . $value;
    }

    /**
     * Get a list of all pages. Prepend an empty option to the start
     *
     * @return array
     */
    public function getUrlOptions()
    {
        $allPages = Page::sortBy('baseFileName')->lists('title', 'baseFileName');
        $pages = array(
            '' => Lang::get('harassmap.menumanager::lang.create.nolink')
        );
        foreach ($allPages as $key => $value) {
            $pages[$key] = "{$value} - (File: $key)";
        }

        return $pages;
    }

    public function getStaticOptions()
    {
        $allPages = StaticPage::listInTheme('harassmap')->lists('title', 'baseFileName');

        $pages = array(
            '' => Lang::get('harassmap.menumanager::lang.create.nolink')
        );
        foreach ($allPages as $key => $value) {
            $pages[$key] = "{$value} - (File: $key)";
        }

        return $pages;
    }
}
