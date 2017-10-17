<?php

namespace Harassmap\Incidents\Classes;

use Cms\Classes\CmsException;
use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;
use Harassmap\Incidents\Models\Domain;
use Lang;
use October\Rain\Support\Traits\Singleton;
use RainLab\Pages\Classes\Router as PagesRouter;
use Response;
use View;

class Controller
{
    use Singleton;

    /**
     * @var Theme
     */
    protected $theme;

    /**
     * @var Domain
     */
    protected $domain;

    /**
     * Initialize this singleton.
     */
    protected function init()
    {
        $this->theme = Theme::getActiveTheme();

        $this->domain = Domain::getBestMatchingDomain();

        if (!$this->theme) {
            throw new CmsException(Lang::get('cms::lang.theme.active.not_found'));
        }
    }

    /**
     * @param $url
     * @return \Cms\Classes\Page|bool
     */
    public function initCmsPage($url)
    {
        $router = new Router($this->theme, $this->domain);
        $page = $router->findByUrl($url);

        $staticRouter = new PagesRouter($this->theme);
        $staticPage = $staticRouter->findByUrl($url);

        if (!$staticPage && !$page) {
            return null;
        } else if (!$page && $staticPage) {
            return false;
        }

        $viewBag = $page->viewBag;

        $cmsPage = CmsPage::inTheme($this->theme);
        $cmsPage->url = $url;
        $cmsPage->apiBag['staticPage'] = $page;

        /*
         * Transfer specific values from the content view bag to the page settings object.
         */
        $viewBagToSettings = ['title', 'layout', 'meta_title', 'meta_description', 'is_hidden'];

        foreach ($viewBagToSettings as $property) {
            $cmsPage->settings[$property] = array_get($viewBag, $property);
        }

        return $cmsPage;
    }
}