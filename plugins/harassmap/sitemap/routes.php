<?php

use Harassmap\Sitemap\Classes\Definition;

Route::get('sitemap.xml', function()
{
    return Response::make(Definition::instance()->generateSitemap())
        ->header("Content-Type", "application/xml");
});
