<?php

namespace MityDigital\Iconamic\Exceptions;

use Exception;

class IconamicException extends Exception
{
    public static function missingPathHelper()
    {
        return new self('Missing Path Helper - a path helper was not provided.');
    }

    public static function unknownPathHelper(?string $pathHelper)
    {
        return new self("Path Helper \"{$pathHelper}\" could not be translated to a Laravel path helper.");
    }
}
