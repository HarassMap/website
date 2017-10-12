<?php

namespace Adrenth\RedirectLite\Controllers;

use Adrenth\RedirectLite\Classes\PublishManager;
use Adrenth\RedirectLite\Models\Redirect;
use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Behaviors\ReorderController;
use Backend\Classes\Controller;
use Backend\Widgets\Form;
use BackendMenu;
use Event;
use Request;

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

/**
 * Class Redirects
 *
 * @package Adrenth\RedirectLite\Controllers
 * @mixin FormController
 * @mixin ListController
 * @mixin ReorderController
 */
class Redirects extends Controller
{
    /**
     * {@inheritdoc}
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ReorderController',
    ];

    /** @var string */
    public $formConfig = 'config_form.yaml';

    /** @var string */
    public $listConfig = [
        'list' => 'config_list.yaml',
    ];

    /** @var string */
    public $reorderConfig = 'config_reorder.yaml';

    /** @var PublishManager */
    public $publishManager;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $sideMenuItemCode = 'reorder' === $this->action
            ? $this->action
            : 'redirects';

        BackendMenu::setContext('Adrenth.RedirectLite', 'redirect', $sideMenuItemCode);

        $this->requiredPermissions = ['adrenth.redirectlite.access_redirects'];

        $this->addCss('/plugins/adrenth/redirectlite/assets/css/redirect.css', 'Adrenth.RedirectLite');
    }

    // @codingStandardsIgnoreStart

    /**
     * Delete selected redirects.
     *
     * @return array
     */
    public function index_onDelete()
    {
        Redirect::destroy($this->getCheckedIds());
        Event::fire('redirects.changed');
        return $this->listRefresh();
    }

    // @codingStandardsIgnoreEnd

    /**
     * Renders status code information partial.
     *
     * @return string
     */
    public function onShowStatusCodeInfo()
    {
        return (string) $this->makePartial('status_code_info', [], false);
    }

    /**
     * Called after the form fields are defined.
     *
     * @param Form $host
     * @param array $fields
     * @return void
     */
    public function formExtendFields(Form $host, array $fields = [])
    {
        if (Request::method() === 'GET') {
            $this->formExtendRefreshFields($host, $fields);
        }
    }

    /**
     * Called when the form is refreshed, giving the opportunity to modify the form fields.
     *
     * @param Form $host The hosting form widget
     * @param array $fields Current form fields
     * @return void
     */
    public function formExtendRefreshFields(Form $host, $fields)
    {
        if ($fields['status_code']->value
            && $fields['status_code']->value[0] === '4'
        ) {
            $host->getField('to_url')->hidden = true;
            return;
        }

        $host->getField('to_url')->hidden = false;
    }

    /**
     * Check checked ID's from POST request.
     *
     * @return array
     */
    private function getCheckedIds()
    {
        if (($checkedIds = post('checked'))
            && is_array($checkedIds)
            && count($checkedIds)
        ) {
            return $checkedIds;
        }

        return [];
    }

    /**
     * Called after the creation or updating form is saved.
     *
     * @param Model
     */
    public function formAfterSave($model)
    {
        Event::fire('redirects.changed');
    }

    /**
     * Called after the form model is deleted.
     *
     * @param Model
     */
    public function formAfterDelete($model)
    {
        Event::fire('redirects.changed');
    }
}
