<?php

namespace Harassmap\MenuManager\Classes;


use Cms\Classes\Page;
use RainLab\Pages\Classes\Page as StaticPage;

class PageLink
{

    public static function url($name, $parameters = [])
    {
        $parts = explode('||', $name);

        if (count($parts) < 2) {
            return $name;
        }

        $value = $parts[1];

        switch ($parts[0]) {
            case '2';
                $value = Page::url($parts[1], $parameters);
                break;
            case '3';
                $value = StaticPage::url($parts[1]);
                break;
        }

        return $value;
    }

}