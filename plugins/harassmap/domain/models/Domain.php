<?php namespace Harassmap\Domain\Models;

use Backend\Models\User as BackendUserModel;
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
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereAbout($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereBlogger($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereFacebook($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereHost($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereInstagram($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereTwitter($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereYoutube($value)
 * @mixin \Eloquent
 * @property string $lat
 * @property string $lng
 * @property int $zoom
 * @property string $name
 * @property bool $incident
 * @property bool $intervention
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereIncident($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereIntervention($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereLat($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereLng($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Harassmap\Domain\Models\Domain whereZoom($value)
 */
class Domain extends Model
{
    use Validation;

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

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

    public $hasMany = [
        'content' => [Content::class, 'delete' => true],
        'tips' => [Tip::class, 'delete' => true]
    ];

    /*
     * A domain can have many users associated with it
     */
    public $belongsToMany = [
        'users' => [BackendUserModel::class, 'table' => 'harassmap_domain_user']
    ];

    public $attachOne = [
        'headerLogo' => 'System\Models\File',
        'footerLogo' => 'System\Models\File'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'harassmap_domain_domain';

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