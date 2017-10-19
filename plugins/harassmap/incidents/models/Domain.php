<?php

namespace Harassmap\Incidents\Models;

use Backend\Models\User as BackendUserModel;
use BackendAuth;
use Harassmap\Incidents\Classes\Analytics;
use Harassmap\Mail\Models\MailLayout;
use Harassmap\Mail\Models\MailTemplate;
use Harassmap\MenuManager\Models\Menu;
use Harassmap\News\Models\Posts;
use JanVince\SmallContactForm\Models\Message;
use Model;
use October\Rain\Database\Traits\Validation;
use RainLab\Translate\Classes\Translator;
use RainLab\Translate\Models\Locale;
use RainLab\Translate\Models\Message as LocaleMessage;
use Request;

/**
 * Harassmap\Incidents\Models\Domain
 */
class Domain extends Model
{
    use Validation;

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $table = 'harassmap_incidents_domain';

    public $translatable = [
        'name',
        'tagline',
        'twitter_message',
        'meta_description',
    ];

    protected $jsonable = ['colours'];

    public $rules = [
        'name' => 'required',
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
        'email' => 'max:100|email|required'
    ];

    public $hasMany = [
        'logos' => [Logo::class, 'delete' => true],
        'content' => [Content::class, 'delete' => true],
        'tips' => [Tip::class, 'delete' => true],
        'posts' => [Posts::class, 'delete' => true],
        'incidents' => [Incident::class, 'delete' => true],
        'menus' => [Menu::class, 'delete' => true],
        'categories' => [Category::class, 'delete' => true],
        'assistance' => [Assistance::class, 'delete' => true],
        'roles' => [Role::class, 'delete' => true],
        'maillayouts' => [MailLayout::class, 'delete' => true],
        'mailtemplates' => [MailTemplate::class, 'delete' => true],
        'contacts' => [Message::class, 'delete' => true],
        'messages' => [LocaleMessage::class, 'delete' => true],
    ];

    public $belongsToMany = [
        'users' => [BackendUserModel::class, 'table' => 'harassmap_incidents_domain_user'],
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
        'polar_bg' => [
            'selector' => ['.bg--polar' => ['background-color']],
            'label' => 'Polar Background Colour',
            'help' => 'Default: #e6f9f8'
        ],
        'droplet_bg' => [
            'selector' => ['.bg--green' => ['background-color']],
            'label' => 'Droplet Background Colour',
            'help' => 'Default: #00b8b0'
        ]
    ];

    public function getTimezoneOptions()
    {
        $zones = timezone_identifiers_list();

        // make the values the keys
        return array_combine($zones, $zones);
    }

    public function getDefaultLanguageOptions()
    {
        return Locale::isEnabled()->lists('code', 'code');
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

    public function beforeDelete()
    {
        // for some reason the logos wont auto delete
        // but this works
        $this->logos()->delete();
    }

    public function afterDelete()
    {
        Analytics::domainDeleted($this);
    }

    public function getLogo($position)
    {
        $locale = Translator::instance()->getLocale();
        $logo = Logo
            ::where('domain_id', '=', $this->id)
            ->where('language', '=', $locale)
            ->where('position', '=', $position)
            ->first();

        return ($logo && $logo->image) ? $logo->image->path : '';
    }

    public function getDesktopLogo()
    {
        return $this->getLogo('desktop');
    }

    public function getMobileLogo()
    {
        return $this->getLogo('mobile');
    }

    public function getFooterLogo()
    {
        return $this->getLogo('footer');
    }

    public function getEmailLogo()
    {
        return $this->getLogo('email');
    }

    public function getMetaImage()
    {
        return $this->getLogo('meta');
    }

    // cache the domains
    public static $domains = [];

    /**
     *
     */
    static function getMatchingDomains()
    {
        // return the cached domains if we have them
        if (count(self::$domains) > 0) {
            return self::$domains;
        }

        $requestHost = Request::getHost();
        $domains = self::orderBy('created_at', 'desc')->get();
        $matches = [];

        foreach ($domains as $domain) {
            $host = $domain->host;

            if (fnmatch($host, $requestHost)) {
                array_push($matches, $domain);
            }
        }

        // cache the matches
        self::$domains = $matches;

        // return the domains
        return self::$domains;
    }

    /**
     * @return Domain
     */
    static function getBestMatchingDomain()
    {
        return self::getMatchingDomains()[0];
    }

    static function getDomainIdOptions($empty = true)
    {

        $user = BackendAuth::getUser();

        $choices = [];

        // if the user is a super use then return all the domains
        if ($user->isSuperUser() || $user->hasPermission(['harassmap.incidents.domain.manage_domains'])) {
            $domains = self::get();
        } else {
            $domains = $user->domains;
        }

        foreach ($domains as $domain) {
            $choices[$domain->id] = $domain->host;
        }

        if ($empty) {
            $choices = [NULL => 'No Domain'] + $choices;
        }

        return $choices;
    }

    public function getLanguages()
    {
        return Locale::isEnabled()->lists('code', 'code');
    }
}