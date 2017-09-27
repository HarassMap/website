<?php

namespace Harassmap\Translate\Models;

use Cache;
use Harassmap\Incidents\Models\Domain;
use RainLab\Translate\Models\Message as BaseMessage;

/**
 * Harassmap\Translate\Models\Message
 */
class Message extends BaseMessage
{

    public static $domainId;

    /**
     * Looks up and translates a message by its string.
     * @param  string $messageId
     * @param  array $params
     * @return string
     */
    public static function trans($messageId, $params = [])
    {
        $domain = Domain::getBestMatchingDomain();

        $msg = static::getWithDomain($messageId, $domain->id);

        $params = array_build($params, function ($key, $value) {
            return [':' . $key, $value];
        });

        $msg = strtr($msg, $params);

        return $msg;
    }

    /**
     * Creates or finds an untranslated message string.
     * @param  string $messageId
     * @return string
     */
    public static function getWithDomain($messageId, $domainId)
    {
        if (!self::$locale) {
            return $messageId;
        }

        $messageCode = self::makeMessageCode($messageId);

        /*
         * Found in cache
         */
        if (array_key_exists($messageCode, self::$cache)) {
            return self::$cache[$messageCode];
        }

        /*
         * Uncached item
         */
        $item = static::firstOrNew([
            'code' => $messageCode,
            'domain_id' => $domainId
        ]);

        /*
         * Create a default entry
         */
        if (!$item->exists) {
            $data = [static::DEFAULT_LOCALE => $messageId];
            $item->message_data = $item->message_data ?: $data;
            $item->save();
        }

        /*
         * Schedule new cache and go
         */
        $msg = $item->forLocale(self::$locale, $messageId);
        self::$cache[$messageCode] = $msg;
        self::$hasNew = true;

        return $msg;
    }

    /**
     * Set the caching context, the page url.
     * @param string $locale
     * @param string $url
     */
    public static function setContext($locale, $url = null)
    {
        if (!strlen($url)) {
            $url = '/';
        }

        $domain = Domain::getBestMatchingDomain();

        self::$domainId = $domain->id;
        self::$url = $url;
        self::$locale = $locale;

        if ($cached = Cache::get(self::makeCacheKey())) {
            self::$cache = (array)$cached;
        } else {
            self::$cache = [];
        }
    }

    /**
     * Save context messages to cache.
     * @return void
     */
    public static function saveToCache()
    {
        if (!self::$hasNew || !self::$url || !self::$locale) {
            return;
        }

        Cache::put(self::makeCacheKey(), self::$cache, 1440);
    }

    /**
     * Creates a cache key for storing context messages.
     * @return string
     */
    protected static function makeCacheKey()
    {
        return 'translation.' . self::$domainId . self::$locale . self::$url;
    }

}
