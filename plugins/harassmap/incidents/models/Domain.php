<?php

namespace Harassmap\Incidents\Models;

use Backend\Models\User as BackendUserModel;
use Harassmap\News\Models\Posts;
use Model;
use October\Rain\Database\Traits\Validation;
use Request;

/**
 * Domain
 *
 * @property int $id
 * @property string $host
 * @property string $about
 * @property string $facebook
 * @property string $twitter
 * @property string $instagram
 * @property string $youtube
 * @property string $blogger
 * @property string $lat
 * @property string $lng
 * @property int $zoom
 * @property string $name
 * @property bool $incident
 * @property bool $intervention
 * @property string $facebook_app_id
 * @property string $attributable_key
 * @property string $ga_key
 * @property string $timezone
 * @property string $nameend
 * @property string $tagline
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereAbout($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereBlogger($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereFacebook($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereHost($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereIncident($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereInstagram($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereIntervention($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereLat($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereLng($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereTwitter($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereYoutube($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereZoom($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereFacebookAppId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereTimezone($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereNameend($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereTagline($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereAttributableKey($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Incidents\Models\Domain whereGaKey($value)
 * @mixin \Eloquent
 */
class Domain extends Model
{
    use Validation;

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $table = 'harassmap_incidents_domain';

    public $translatable = [
        'name',
        'nameend',
        'tagline',
        'about',
        'twitter_message',
    ];

    public $rules = [
        'name' => 'required',
        'about' => 'required',
        'host' => 'required',
        'lat' => 'required',
        'lng' => 'required',
        'zoom' => 'required',
        'twitter_message' => 'max:100'
    ];

    public $hasMany = [
        'content' => [Content::class, 'delete' => true],
        'tips' => [Tip::class, 'delete' => true],
        'posts' => [Posts::class, 'delete' => true],
        'incidents' => Incident::class,
    ];

    public $belongsToMany = [
        'users' => [BackendUserModel::class, 'table' => 'harassmap_incidents_domain_user'],
        'categories' => [Category::class, 'table' => 'harassmap_incidents_domain_category']
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

    /**
     * @return Domain
     */
    static function getBestMatchingDomain()
    {
        return self::getMatchingDomains()[0];
    }

    public function getTimezoneOptions()
    {
        $zones = timezone_identifiers_list();

        // make the values the keys
        return array_combine($zones, $zones);
    }
}