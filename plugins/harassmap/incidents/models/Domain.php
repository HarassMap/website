<?php

namespace Harassmap\Incidents\Models;

use Backend\Models\User as BackendUserModel;
use Harassmap\Incidents\Classes\Analytics;
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
 * @property string|null $logos
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereLogos($value)
 * @property string|null $email
 * @method static \Illuminate\Database\Eloquent\Builder|\Harassmap\Incidents\Models\Domain whereEmail($value)
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
        'logos'
    ];

    protected $jsonable = ['colours', 'logos'];

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
        'email' => 'max:100|email'
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

    public $colourTypes = [
        'header' => [
            'selector' => ['.domain--header' => ['background-color']],
            'label' => 'Header',
            'help' => 'Default: #00b8b0'
        ],
        'header_text' => [
            'selector' => ['.domain--header a' => ['color']],
            'label' => 'Header Text',
            'help' => 'Default #ffffff'
        ],
        'menu' => [
            'selector' => ['.header .nav > .nav-item > a' => ['color']],
            'label' => 'Menu Text',
            'help' => 'Default #000000'
        ],
        'font_color_dark' => [
            'selector' => ['p' => ['color']],
            'label' => 'Font Colour Dark',
            'help' => 'Default: #000000'
        ],
        'font_color_light' => [
            'selector' => ['.bg--dark p, .bg--dark h2' => ['color']],
            'label' => 'Font Colour Light',
            'help' => 'Default: #ffffff'
        ],
        'horizontal_header' => [
            'selector' => ['.hr__text' => ['color'], '.hr__line' => ['background-color']],
            'label' => 'Line Header',
            'help' => 'Default: #4a4a4a'
        ],
        'green_text' => [
            'selector' => ['h4' => ['color'], '.header .nav > .nav-item:hover a' => ['color']],
            'label' => 'Green Text',
            'help' => 'Default: #00b8b0'
        ],
        'button' => [
            'selector' => ['.btn--default' => ['color', 'border-color']],
            'label' => 'Button Color',
            'help' => 'Default: #00b8b0'
        ],
        'button_red' => [
            'selector' => ['.btn--red' => ['background-color']],
            'label' => 'Red Button Color',
            'help' => 'Default: #ba1d4a'
        ],
        'button_green' => [
            'selector' => ['.btn--green' => ['background-color']],
            'label' => 'Green Button Color',
            'help' => 'Default: #00b8b0'
        ],
    ];

    /**
     *
     */
    static function getMatchingDomains()
    {
        $requestHost = Request::getHost();
        $domains = self::orderBy('created_at', 'desc')->get();
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

    public function beforeSave()
    {
        $colours = [];

        foreach ($this->colourTypes as $colour => $options) {
            $value = $this->{'colours_' . $colour};

            if ($value) {
                $options['value'] = $value;
                $colours[$colour] = $options;
            }

            unset($this->{'colours_' . $colour});
        }

        $this->colours = $colours;

        // reset all the colours
        if ($this->resetColours === "1") {
            $this->colours = [];
        }

        unset($this->resetColours);
    }

    public function afterCreate()
    {
        Analytics::domainCreated($this);
    }

    public function afterUpdate()
    {
        Analytics::domainEdited($this);
    }

    public function afterDelete()
    {
        Analytics::domainDeleted($this);
    }

    public function getHeaderLogo()
    {
        return $this->logos[0]['header'];
    }

    public function getMobileLogo()
    {
        return $this->logos[0]['mobile'];
    }

    public function getFooterLogo()
    {
        return $this->logos[0]['footer'];
    }
}