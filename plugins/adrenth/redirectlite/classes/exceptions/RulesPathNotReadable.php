<?php

namespace Adrenth\RedirectLite\Classes\Exceptions;

use RuntimeException;

/**
 * Class RulesPathNotReadable
 *
 * @package Adrenth\RedirectLite\Classes\Exceptions
 */
class RulesPathNotReadable extends RuntimeException
{
    /**
     * @param string $path
     * @return RulesPathNotReadable
     */
    public static function withPath($path)
    {
        return new static("Rules path $path is not readable.");
    }
}
