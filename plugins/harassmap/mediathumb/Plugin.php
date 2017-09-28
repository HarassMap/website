<?php

namespace Harassmap\Mediathumb;

use Harassmap\Mediathumb\Classes\MediaThumb;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'mediathumb_resize' => [$this, 'mediathumb_resize']
            ]
        ];
    }

    public function mediathumb_resize($img, $mode = null, $size = null, $quality = null)
    {
        return MediaThumb::getThumb($img, $mode, $size, $quality);
    }
}
