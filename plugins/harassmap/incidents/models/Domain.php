<?php

namespace Harassmap\Incidents\Models;

use Backend\Models\User as BackendUserModel;
use Harassmap\Incidents\Models\Country;
use Harassmap\News\Models\Posts;
use Model;
use October\Rain\Database\Traits\Validation;
use Request;

/**
 * Domain
 */
class Domain extends Model
{
    use Validation;

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $table = 'harassmap_incidents_domain';

    public $translatable = [
        'name',
        'about',
    ];

    public $rules = [
        'name' => 'required',
        'about' => 'required',
        'host' => 'required',
        'lat' => 'required',
        'lng' => 'required',
        'zoom' => 'required',
    ];

    public $belongsTo = [
        'country' => Country::class
    ];

    public $hasMany = [
        'content' => [Content::class, 'delete' => true],
        'tips' => [Tip::class, 'delete' => true],
        'posts' => [Posts::class, 'delete' => true]
    ];

    public $belongsToMany = [
        'users' => [BackendUserModel::class, 'table' => 'harassmap_domain_user']
    ];

    public $attachOne = [
        'headerLogo' => 'System\Models\File',
        'footerLogo' => 'System\Models\File'
    ];

    /**
     *
     */
    static function getMatchingDomains()
    {
        $requestHost = Request::getHost();
        $domains = self::get();
        $matches = [];

        foreach ($domains as $domain) {
            $host = $domain->host;

            if (fnmatch($host, $requestHost)) {
                array_push($matches, $domain);
            }
        }

        return $matches;
    }

    static function getBestMatchingDomain()
    {
        return self::getMatchingDomains()[0];
    }
}