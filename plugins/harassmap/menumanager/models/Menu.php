<?php namespace Harassmap\MenuManager\Models;

use Cms\Classes\Controller as BaseController;
use Cms\Classes\Page;
use Harassmap\Incidents\Models\Domain;
use Harassmap\Incidents\Traits\DomainOptions;
use Lang;
use Model;
use RainLab\Pages\Classes\Page as StaticPage;
use Validator;
use October\Rain\Database\Traits\NestedTree;
use October\Rain\Database\Traits\Validation;
use October\Rain\Database\Traits\Purgeable;

/**
 * Menu Model
 */
class Menu extends Model
{
    use NestedTree;
    use Validation;
    use Purgeable;
    use DomainOptions;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'benfreke_menumanager_menus';

    /**
     * @var array Translatable fields
     */
    public $translatable = ['title', 'description'];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required',
        'parameters' => 'json',
        'domain' => 'required',
    ];

    public $belongsTo = [
        'domain' => Domain::class,
    ];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['title', 'description', 'parent_id'];

    /**
     * @var array List of purgeable values.
     */
    protected $purgeable = ['internal_url', 'external_url', 'static_url'];

    /**
     * @var \Cms\Classes\Controller A reference to the CMS controller.
     */
    private $controller;

    /**
     * Add translation support to this model, if available.
     *
     * @return void
     */
    public static function boot()
    {
        Validator::extend(
            'json',
            function ($attribute, $value, $parameters) {
                json_decode($value);

                return json_last_error() == JSON_ERROR_NONE;
            }
        );

        // Call default functionality (required)
        parent::boot();

        // Check the translate plugin is installed
        if (!class_exists('RainLab\Translate\Behaviors\TranslatableModel')) {
            return;
        }

        // Extend the constructor of the model
        self::extend(
            function ($model) {

                // Implement the translatable behavior
                $model->implement[] = 'RainLab.Translate.Behaviors.TranslatableModel';
            }
        );
    }

    /**
     * Returns the list of menu items, where the key is the id and the value is the title, indented with '-' for depth
     *
     * @return array
     */
    public function getSelectList()
    {
        $items = $this->getAll();
        $output = array();
        foreach ($items as $item) {
            $depthIndicator = $this->getDepthIndicators($item->nest_depth);
            $output["id-$item->id"] = $depthIndicator . ' ' . $item->title;
        }

        return $output;
    }

    /**
     * Recursively adds depth indicators, a '-', to a string
     *
     * @param int $depth
     * @param string $indicators
     *
     * @return string
     */
    protected function getDepthIndicators($depth = 0, $indicators = '')
    {
        if ($depth < 1) {
            return $indicators;
        }

        return $this->getDepthIndicators(--$depth, $indicators . '-');
    }

    /**
     * Returns the class name so I can compare
     *
     * @return string
     */
    public static function getClassName()
    {
        return get_called_class();
    }

    /**
     * Never trust user input, so let's fix up the query string
     *
     * @param $rawString
     *
     * @return string
     */
    protected function createQueryString($rawString)
    {
        // Remove the first character if it is a ?
        if (substr($rawString, 0, 1) === '?') {
            $rawString = substr($rawString, 1);
        }

        // Convert to the individual params
        parse_str($rawString, $queryParams);

        // Build the query string and return it
        return http_build_query($queryParams);
    }

    /**
     * Sets the target attribute for the link
     *
     * @return string
     */
    public function getLinkTarget()
    {
        return $this->link_target ?: '_self';
    }

    /**
     * Load the item classes here to keep the twig template clean
     *
     * @param int $leftIndex The left value of the active node
     * @param int $rightIndex The right value of the active node
     *
     * @return string
     *
     * @todo Add dropdown class if the depth is right
     */
    public function getListItemClasses($leftIndex, $rightIndex)
    {
        $classes = array();

        // Is this item active?
        if ($this->nest_left <= $leftIndex && $this->nest_right >= $rightIndex) {
            $classes[] = 'active';
        }

        // If not active, return an empty string
        return implode(' ', $classes);
    }

    /**
     * Format json
     */
    public function beforeSave()
    {
        if ($this->parameters != '') {
            $this->parameters = json_encode(json_decode($this->parameters));
        }

        if($this->domain_id === '') {
            $this->domain_id = NULL;
        }

        if($this->code === '') {
            $this->code = NULL;
        }
    }

    /**
     * Sets the URL attribute, based on whether it is internal or external
     *
     * @param $value
     */
    public function setUrlAttribute($value)
    {
        $urlValue = null;

        switch ($this->is_external) {
            case 0:
                $urlValue = $this->internal_url;
                break;
            case 1:
                $urlValue = $this->external_url;
                break;
            case 2:
                $urlValue = $this->static_url;
                break;
        }

        // Allow seeding via the 'url' value
        if (empty($urlValue) && !empty($value)) {
            $urlValue = $value;
        }
        $this->attributes['url'] = $urlValue;
    }

    /**
     * Postgres returns these as boolean. The admin screen needs this to be an integer
     *
     * @param int|boolean $attribute The is_external value saved in the database
     * @return int
     */
    public function getIsExternalAttribute($attribute)
    {
        return (int)$attribute;
    }

    public function getCodeOptions()
    {
        return [
            null => 'No Code',
            'main-menu' => 'Main Menu',
            'footer-menu' => 'Footer Menu',
        ];
    }

}
