<?php namespace Harassmap\Base;

use Harassmap\Base\Classes\HarassMapPresenter;
use Illuminate\Pagination\LengthAwarePaginator;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function boot()
    {
        LengthAwarePaginator::presenter(function ($paginator) {
            return new HarassMapPresenter($paginator);
        });
    }

    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }
}
