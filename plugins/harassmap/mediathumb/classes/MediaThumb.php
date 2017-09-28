<?php

namespace Harassmap\Mediathumb\Classes;

use Cache;

class MediaThumb
{
    public static $cache = [];

    public static function resize($img, $mode = null, $size = null, $quality = null)
    {
        $cacheKey = $img . $mode . $size . $quality;

        if (array_key_exists($cacheKey, self::$cache)) {
            return self::$cache[$cacheKey];
        }

        $cached = Cache::get($cacheKey);

        if (!is_null($cached)) {
            self::$cache[$cacheKey] = $path = $cached;
        } else {
            $path = mediathumbResize($img, $mode, $size, $quality, 'media');

            Cache::put($cacheKey, $path, 1440);
        }

        return $path;
    }

}