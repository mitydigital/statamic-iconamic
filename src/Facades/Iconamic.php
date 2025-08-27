<?php

namespace MityDigital\Iconamic\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string cleanSvg(string $svg, int $index, array $attributes = [])
 * @method static string getPath(string $path, string $pathHelper, string $filename = null)
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
