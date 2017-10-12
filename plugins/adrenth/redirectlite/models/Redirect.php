<?php

namespace Adrenth\RedirectLite\Models;

use Eloquent;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Validator;
use October\Rain\Database\Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

/**
 * Class Redirect
 *
 * @package Adrenth\RedirectLite\Models
 * @mixin Eloquent
 */
class Redirect extends Model
{
    use Sortable;
    use Validation {
        makeValidator as traitMakeValidator;
    }

    /** @var array */
    public static $statusCodes = [
        301 => 'permanent',
        302 => 'temporary',
        303 => 'see_other',
        404 => 'not_found',
        410 => 'gone',
    ];

    /**
     * {@inheritdoc}
     */
    public $table = 'adrenth_redirectlite_redirects';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * Validation rules
     *
     * @var array
     */
    public $rules = [
        'from_url' => 'required',
        'status_code' => 'required|in:301,302,303,404,410',
        'sort_order' => 'numeric',
    ];

    /**
     * {@inheritdoc}
     */
    public $jsonable = [
        'requirements',
    ];

    /**
     * Custom attribute names
     *
     * @var array
     */
    public $attributeNames = [
        'to_url' => 'adrenth.redirectlite::lang.redirect.to_url',
        'from_url' => 'adrenth.redirectlite::lang.redirect.from_url',
        'status_code' => 'adrenth.redirectlite::lang.redirect.status_code',
        'sort_order' => 'adrenth.redirectlite::lang.redirect.sort_order',
        'last_used_at' => 'adrenth.redirectlite::lang.redirect.last_used_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $dates = [
        'last_used_at',
    ];

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param array $data
     * @param array $rules
     * @param array $customMessages
     * @param array $attributeNames
     * @return Validator
     */
    protected static function makeValidator(
        array $data,
        array $rules,
        array $customMessages = [],
        array $attributeNames = []
    ) {
        $validator = self::traitMakeValidator($data, $rules, $customMessages, $attributeNames);

        $validator->sometimes('to_url', 'required', function (Fluent $request) {
            return in_array($request->get('status_code'), ['301', '302', '303'], true);
        });

        return $validator;
    }

    /**
     * Mutator for 'from_url' attribute; make sure the value is URL decoded.
     *
     * @param string $value
     */
    public function setFromUrlAttribute($value)
    {
        $this->attributes['from_url'] = urldecode($value);
    }

    /**
     * Mutator for 'sort_order' attribute; make sure the value is an integer.
     *
     * @param mixed $value
     */
    public function setSortOrderAttribute($value)
    {
        $this->attributes['sort_order'] = (int) $value;
    }

    /**
     * @return array
     */
    public function filterStatusCodeOptions()
    {
        $options = [];

        foreach (self::$statusCodes as $value => $message) {
            $options[$value] = trans("adrenth.redirectlite::lang.redirect.$message");
        }

        return $options;
    }
}
