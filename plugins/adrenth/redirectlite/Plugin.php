<?php

namespace Adrenth\RedirectLite;

use Adrenth\RedirectLite\Classes\Exceptions\RulesPathNotReadable;
use Adrenth\RedirectLite\Classes\PublishManager;
use Adrenth\RedirectLite\Classes\RedirectManager;
use App;
use Backend;
use Event;
use Exception;
use Request;
use System\Classes\PluginBase;

/**
 * Class Plugin
 *
 * @package Adrenth\RedirectLite
 */
class Plugin extends PluginBase
{
    /**
     * {@inheritdoc}
     */
    public function pluginDetails()
    {
        return [
            'name' => 'adrenth.redirectlite::lang.plugin.name',
            'description' => 'adrenth.redirectlite::lang.plugin.description',
            'author' => 'Alwin Drenth',
            'icon' => 'icon-link',
            'homepage' => 'http://octobercms.com/plugin/adrenth-redirectlite',
        ];
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function boot()
    {
        if (App::runningInBackend()
            && !App::runningInConsole()
            && !App::runningUnitTests()
        ) {
            $this->bootBackend();
        }

        if (!App::runningInBackend()
            && !App::runningUnitTests()
            && !App::runningInConsole()
        ) {
            $this->bootFrontend();
        }
    }

    /**
     * Boot stuff for Frontend
     *
     * @return void
     */
    public function bootFrontend()
    {
        // Only handle specific request methods
        if (!in_array(Request::method(), ['GET', 'POST', 'HEAD'], true)) {
            return;
        }

        // Create the redirect manager if redirect rules are readable.
        try {
            $manager = RedirectManager::createWithDefaultRulesPath();
        } catch (RulesPathNotReadable $e) {
            return;
        }

        $requestUri = str_replace(Request::getBasePath(), '', Request::getRequestUri());

        $rule = $manager->match($requestUri);

        if ($rule) {
            $manager->redirectWithRule($rule);
        }
    }

    /**
     * Boot stuff for Backend
     *
     * @return void
     * @throws Exception
     */
    public function bootBackend()
    {
        Event::listen('redirects.changed', function () {
            PublishManager::instance()->publish();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function registerPermissions()
    {
        return [
            'adrenth.redirectlite.access_redirects' => [
                'label' => 'adrenth.redirectlite::lang.permission.access_redirects.label',
                'tab' => 'adrenth.redirectlite::lang.permission.access_redirects.tab',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerNavigation()
    {
        $navigation = [
            'redirect' => [
                'label' => 'adrenth.redirectlite::lang.navigation.menu_label',
                'iconSvg' => '/plugins/adrenth/redirectlite/assets/images/redirect-icon.svg',
                'icon' => 'icon-link',
                'url' => Backend::url('adrenth/redirectlite/redirects'),
                'order' => 201,
                'permissions' => [
                    'adrenth.redirectlite.access_redirects',
                ],
            ],
        ];

        return $navigation;
    }

    /**
     * {@inheritdoc}
     */
    public function registerListColumnTypes()
    {
        return [
            'redirectlite_status_code' => function ($value) {
                switch ($value) {
                    case 301:
                        return e(trans('adrenth.redirectlite::lang.redirect.permanent'));
                    case 302:
                        return e(trans('adrenth.redirectlite::lang.redirect.temporary'));
                    case 303:
                        return e(trans('adrenth.redirectlite::lang.redirect.see_other'));
                    case 404:
                        return e(trans('adrenth.redirectlite::lang.redirect.not_found'));
                    case 410:
                        return e(trans('adrenth.redirectlite::lang.redirect.gone'));
                    default:
                        return $value;
                }
            },
            'redirectlite_from_url' => function ($value) {
                $maxChars = 40;
                $textLength = strlen($value);
                if ($textLength > $maxChars) {
                    return '<span title="' . e($value) . '">'
                        . substr_replace($value, '...', $maxChars / 2, $textLength - $maxChars)
                        . '</span>';
                }
                return $value;
            },
        ];
    }
}
