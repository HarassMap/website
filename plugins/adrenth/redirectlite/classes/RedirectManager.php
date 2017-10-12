<?php

namespace Adrenth\RedirectLite\Classes;

use Adrenth\RedirectLite\Classes\Exceptions\RulesPathNotReadable;
use Adrenth\RedirectLite\Models\Redirect;
use Carbon\Carbon;
use Cms;
use DB;
use Exception;
use League\Csv\Reader;
use Log;
use Request;

/**
 * Class RedirectManager
 *
 * @package Adrenth\RedirectLite\Classes
 */
class RedirectManager
{
    /** @var string */
    private $redirectRulesPath;

    /** @var RedirectRule[] */
    private $redirectRules;

    /** @var string */
    private $basePath;

    /**
     * HTTP 1.1 headers
     *
     * @var array
     */
    private static $headers = [
        301 => 'HTTP/1.1 301 Moved Permanently',
        302 => 'HTTP/1.1 302 Found',
        303 => 'HTTP/1.1 303 See Other',
        404 => 'HTTP/1.1 404 Not Found',
        410 => 'HTTP/1.1 410 Gone',
    ];

    /**
     * Constructs a RedirectManager instance.
     */
    protected function __construct()
    {
        $this->basePath = Request::getBasePath();
    }

    /**
     * Creates an instance of the RedirectManager with the default rules path.
     *
     * @return RedirectManager
     * @throws RulesPathNotReadable
     */
    public static function createWithDefaultRulesPath()
    {
        $rulesPath = storage_path('app/redirects-lite.csv');

        if (!file_exists($rulesPath) || !is_readable($rulesPath)) {
            throw RulesPathNotReadable::withPath($rulesPath);
        }

        return RedirectManager::createWithRulesPath($rulesPath);
    }

    /**
     * Create an instance of the RedirectManager with a specific rules path.
     *
     * @param $redirectRulesPath
     * @return RedirectManager
     */
    public static function createWithRulesPath($redirectRulesPath)
    {
        $instance = new self();
        $instance->redirectRulesPath = $redirectRulesPath;
        return $instance;
    }

    /**
     * Create an instance of the RedirectManager with a redirect rule.
     *
     * @param RedirectRule $rule
     * @return RedirectManager
     */
    public static function createWithRule(RedirectRule $rule)
    {
        $instance = new self();
        $instance->redirectRules[] = $rule;
        return $instance;
    }

    /**
     * @param string $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '/');
        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Find a match based on given URL.
     *
     * @param string $requestPath
     * @return RedirectRule|false
     */
    public function match($requestPath)
    {
        $requestPath = urldecode($requestPath);

        $this->loadRedirectRules();

        foreach ($this->redirectRules as $rule) {
            if ($matchedRule = $this->matchesRule($rule, $requestPath)) {
                return $matchedRule;
            }
        }

        return false;
    }

    /**
     * Redirect with specific rule.
     *
     * @param RedirectRule $rule
     * @return void
     */
    public function redirectWithRule(RedirectRule $rule)
    {
        $this->updateStatistics($rule->getId());

        $statusCode = $rule->getStatusCode();

        if ($statusCode === 404 || $statusCode === 410) {
            header(self::$headers[$statusCode], true, $statusCode);
            exit(0);
        }

        $toUrl = $this->getLocation($rule);

        if (!$toUrl || empty($toUrl)) {
            return;
        }

        header(self::$headers[$statusCode], true, $statusCode);
        header('Location: ' . $toUrl, true, $statusCode);

        exit(0);
    }

    /**
     * Get Location URL to redirect to.
     *
     * @param RedirectRule $rule
     * @return bool|string
     */
    public function getLocation(RedirectRule $rule)
    {
        $toUrl = $this->redirectToPathOrUrl($rule);

        if (is_string($toUrl)
            && $toUrl[0] !== '/'
            && substr($toUrl, 0, 7) !== 'http://'
            && substr($toUrl, 0, 8) !== 'https://'
        ) {
            $toUrl = $this->basePath . '/' . $toUrl;
        }

        if ($toUrl[0] === '/') {
            $toUrl = Cms::url($toUrl);
        }

        return $toUrl;
    }

    /**
     * @param RedirectRule $rule
     * @return string
     */
    private function redirectToPathOrUrl(RedirectRule $rule)
    {
        return $rule->getToUrl();
    }

    /**
     * Check if rule matches against request path and scheme.
     *
     * @param RedirectRule $rule
     * @param string $requestPath
     * @return RedirectRule|bool
     */
    private function matchesRule(RedirectRule $rule, $requestPath)
    {
        return $this->matchExact($rule, $requestPath);
    }

    /**
     * Perform an exact URL match.
     *
     * @param RedirectRule $rule
     * @param string $url
     * @return RedirectRule|bool
     */
    private function matchExact(RedirectRule $rule, $url)
    {
        return $url === $rule->getFromUrl() ? $rule : false;
    }

    /**
     * Load definitions into memory.
     *
     * @return void
     */
    private function loadRedirectRules()
    {
        if ($this->redirectRules !== null) {
            return;
        }

        $rules = [];

        try {
            /** @var Reader $reader */
            $reader = Reader::createFromPath($this->redirectRulesPath);

            // WARNING: this is deprecated method in league/csv:8.0, when league/csv is upgraded to version 9 we should
            // follow the instructions on this page: http://csv.thephpleague.com/upgrading/9.0/
            $results = $reader->fetchAssoc(0);

            foreach ($results as $row) {
                $rule = new RedirectRule($row);
                $rules[] = $rule;
            }
        } catch (Exception $e) {
            Log::error($e);
        }

        $this->redirectRules = $rules;
    }

    /**
     * Update database statistics.
     *
     * @param int $redirectId
     */
    private function updateStatistics($redirectId)
    {
        /** @var Redirect $redirect */
        $redirect = Redirect::find($redirectId);

        if ($redirect === null) {
            return;
        }

        $now = Carbon::now();

        $redirect->update([
            'hits' => DB::raw('hits + 1'),
            'last_used_at' => $now,
        ]);
    }
}
