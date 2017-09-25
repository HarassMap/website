<?php namespace Harassmap\MenuManager\Components;

use App;
use Harassmap\Incidents\Models\Domain;
use Harassmap\MenuManager\Models\Menu as MenuModel;
use Cms\Classes\ComponentBase;
use DB;
use Lang;
use Request;

class Menu extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'harassmap.menumanager::lang.menu.name',
            'description' => 'harassmap.menumanager::lang.menu.description'
        ];
    }

    /**
     * @return array
     * @todo Change start to parentNode to match my naming
     */
    public function defineProperties()
    {
        return [
            'start'            => [
                'description' => 'harassmap.menumanager::lang.component.start.description',
                'title'       => 'harassmap.menumanager::lang.component.start.title',
                'default'     => 1,
                'type'        => 'dropdown'
            ],
            'activeNode'       => [
                'description' => 'harassmap.menumanager::lang.component.activenode.description',
                'title'       => 'harassmap.menumanager::lang.component.activenode.title',
                'default'     => 0,
                'type'        => 'dropdown'
            ],
            'listItemClasses'  => [
                'description' => 'harassmap.menumanager::lang.component.listitemclasses.description',
                'title'       => 'harassmap.menumanager::lang.component.listitemclasses.title',
                'default'     => 'item',
                'type'        => 'string'
            ],
            'primaryClasses'   => [
                'description' => 'harassmap.menumanager::lang.component.primaryclasses.description',
                'title'       => 'harassmap.menumanager::lang.component.primaryclasses.title',
                'default'     => 'nav nav-pills',
                'type'        => 'string'
            ],
            'secondaryClasses' => [
                'description' => 'harassmap.menumanager::lang.component.secondaryclasses.description',
                'title'       => 'harassmap.menumanager::lang.component.secondaryclasses.title',
                'default'     => 'dropdown-menu',
                'type'        => 'string'
            ],
            'tertiaryClasses'  => [
                'description' => 'harassmap.menumanager::lang.component.tertiaryclasses.description',
                'title'       => 'harassmap.menumanager::lang.component.tertiaryclasses.title',
                'default'     => '',
                'type'        => 'string'
            ],
            'numberOfLevels'   => [
                'description' => 'harassmap.menumanager::lang.component.numberoflevels.description',
                'title'       => 'harassmap.menumanager::lang.component.numberoflevels.title',
                'default'     => '2', // This is the array key, not the value itself
                'type'        => 'dropdown',
                'options'     => [
                    1 => '1',
                    2 => '2',
                    3 => '3'
                ]
            ]
        ];
    }

    /**
     * Returns the list of menu items, plus an empty default option
     *
     * @return array
     */
    public function getActiveNodeOptions()
    {
        $options = $this->getStartOptions();
        array_unshift($options, 'default');

        return $options;
    }

    /**
     * Returns the list of menu items I can select
     *
     * @return array
     */
    public function getStartOptions()
    {
        $MenuModel = new MenuModel();

        return $MenuModel->getCodeOptions();
    }

    /**
     * Build all my parameters for the view
     *
     * @todo Pull as much as possible into the model, including the column names
     */
    public function onRender()
    {
        $domain = Domain::getBestMatchingDomain();
        $code = $this->property('start');

        // get the top node based on the code and domain we are on
        $topNode = MenuModel
            ::where('domain_id', '=', $domain->id)
            ->where('code', '=', $code)
            ->first();

        $this->page['parentNode'] = $topNode;

        // What page is active?
        $this->page['activeLeft'] = 0;
        $this->page['activeRight'] = 0;
        $activeNode = $this->getIdFromProperty($this->property('activeNode'));

        if ($activeNode) {

            // It's been set by the user, so use what they've set it as
            $activeNode = MenuModel::find($activeNode);
        } elseif ($topNode) {

            // Go and find the page we're on
            $baseFileName = $this->page->page->getBaseFileName();

            // Get extra URL parameters
            $params = $this->page->controller->getRouter()->getParameters();

            // And make sure the active page is a child of the parentNode
            $activeNode = MenuModel::where('url', $baseFileName)
                ->where('nest_left', '>', $topNode->nest_left)
                ->where('nest_right', '<', $topNode->nest_right);

            $activeNode = $activeNode->first();
        }

        // If I've got a result that is a node
        if ($activeNode && MenuModel::getClassName() === get_class($activeNode)) {
            $this->page['activeLeft'] = (int)$activeNode->nest_left;
            $this->page['activeRight'] = (int)$activeNode->nest_right;
        }

        // How deep do we want to go?
        $this->page['numberOfLevels'] = (int)$this->property('numberOfLevels');

        // Add the classes to the view
        $this->page['primaryClasses'] = $this->property('primaryClasses');
        $this->page['secondaryClasses'] = $this->property('secondaryClasses');
        $this->page['tertiaryClasses'] = $this->property('tertiaryClasses');
        $this->page['listItemClasses'] = $this->property('listItemClasses');
    }

    /**
     * Gets the id from the passed property
     *  Due to the component inspector re-ordering the array on keys, and me using the key as the menu model id,
     *  I've been forced to add a string to the key. This method removes it and returns the raw id.
     *
     * @param $value
     *
     * @return bool|string
     */
    protected function getIdFromProperty($value)
    {
        if (!strlen($value) > 3) {
            return false;
        }

        return substr($value, 3);
    }

}
