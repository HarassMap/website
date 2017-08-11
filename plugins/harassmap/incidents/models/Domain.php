<?php

namespace Harassmap\Incidents\Models;

use Backend\Models\User as BackendUserModel;
use Harassmap\News\Models\Posts;
use Model;
use October\Rain\Database\Traits\Validation;
use Request;

/**
 * Harassmap\Incidents\Models\Domain
 *
 * @property int $id
 * @property string $host
 * @property string $about
 * @property string|null $facebook
 * @property string|null $twitter
 * @property string|null $instagram
 * @property string|null $youtube
 * @property string|null $blogger
 * @property string $lat
 * @property string $lng
 * @property int $zoom
 * @property string $name
 * @property int $incident
 * @property int $intervention
 * @property string|null $facebook_app_id
 * @property string $timezone
 * @property string $nameend
 * @property string $tagline
 * @property string|null $attributable_key
 * @property string|null $ga_key
 * @property string|null $twitter_message
 * @property int $need_approval
 * @property string|null $colours
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereAttributableKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereBlogger($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereColours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereFacebookAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereGaKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereIncident($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereIntervention($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereNameend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereNeedApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereTagline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereTwitterMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereYoutube($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereZoom($value)
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

    protected $jsonable = ['form_data'];

    public $rules = [
        'name' => 'required',
        'about' => 'required',
        'host' => 'required|max:50',
        'lat' => 'required',
        'lng' => 'required',
        'zoom' => 'required',
        'twitter_message' => 'max:100',
        'facebook_app_id' => 'max:32',
        'facebook' => 'max:100',
        'twitter' => 'max:100',
        'instagram' => 'max:100',
        'youtube' => 'max:100',
        'blogger' => 'max:100',
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