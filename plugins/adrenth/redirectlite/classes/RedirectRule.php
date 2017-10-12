<?php

namespace Adrenth\RedirectLite\Classes;

use Adrenth\RedirectLite\Models\Redirect;
use InvalidArgumentException;

/**
 * Class RedirectRule
 *
 * @package Adrenth\RedirectLite\Classes
 */
class RedirectRule
{
    /** @var int */
    private $id;

    /** @var string */
    private $matchType;

    /** @var string */
    private $fromUrl;

    /** @var string */
    private $toUrl;

    /** @var int */
    private $statusCode;

    /**
     * @param array $attributes
     * @throws InvalidArgumentException
     */
    public function __construct(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $property = camel_case($key);

            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    /**
     * @param Redirect $model
     * @return RedirectRule
     * @throws InvalidArgumentException
     */
    public static function createWithModel(Redirect $model)
    {
        $attributes = $model->getAttributes();
        return new self($attributes);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMatchType()
    {
        return $this->matchType;
    }

    /**
     * @return string
     */
    public function getFromUrl()
    {
        return $this->fromUrl;
    }

    /**
     * @return string
     */
    public function getToUrl()
    {
        return $this->toUrl;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return (int) $this->statusCode;
    }
}
