<?php

namespace MityDigital\Iconamic\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string cleanSvg($svg, $index)
 * @method static string getPath($path, $pathHelper, $filename = null)
 *
 * @see MityDigita\Iconamic\Support\Iconamic
 */
class Iconamic extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \MityDigital\Iconamic\Support\Iconamic::class;
    }
}
