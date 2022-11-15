<?php

namespace MityDigital\Iconamic\Support;

use MityDigital\Iconamic\Exceptions\IconamicException;

class Iconamic
{
    /**
     * Clean an SVG for rendering
     *
     * SVGs can use ID, which means you can easily get multiple IDs that are the same in your markup. That's not cool.
     * So this trues to replace common patterns with an index (i.e. fills) to make IDs unique in each instance of the
     * SVG.
     *
     * @param  string  $svg
     * @param  int  $index
     * @param  array  $attributes
     *
     * @return string
     */
    public function cleanSvg(string $svg, int $index, array $attributes = []): string
    {
        // make "#id" and id="" unique
        $svg = str_replace(' id="', ' id="iconamic-'.$index.'-', $svg);
        $svg = str_replace(' xlink:href="#', ' xlink:href="#iconamic-'.$index.'-', $svg);
        $svg = str_replace('url(#', 'url(#iconamic-'.$index.'-', $svg);

        // replace attributes
        $dom = new \DOMDocument();
        $dom->loadXML($svg, LIBXML_NOERROR);

        // if we have classReplace, do this first because it may be appended to by "class"
        if (array_key_exists('classReplace', $attributes))
        {
            // set the class from classReplace
            $dom->documentElement->setAttribute('class', $attributes['classReplace']);

            unset($attributes['classReplace']);
        }

        foreach($attributes as $attribute => $value)
        {
            if ($attribute === 'class')
            {
                if ($dom->documentElement->hasAttribute('class'))
                {
                    // append to the class
                    $existing = $dom->documentElement->getAttribute('class');
                    $dom->documentElement->setAttribute($attribute, $existing.' '.$value);
                }
                else {
                    // add the class
                    $dom->documentElement->setAttribute($attribute, $value);
                }
            }
            else {
                // overwrite attribute - simply add it
                $dom->documentElement->setAttribute($attribute, $value);
            }
        }

        return $dom->saveXML($dom->documentElement);
    }

    /**
     * Get the path for an icon
     *
     * Will use the $path within the path helper to create a full path as a string.
     *
     * If you provide a $filename, that will be tacked on the end of the path too. Voilà!
     *
     * @param  string  $path  The path to use
     * @param  string  $pathHelper  The Laravel path helper to use
     * @param  null|string  $filename  The SVG filename to use, if you want
     *
     * @return string
     * @throws IconamicException
     */
    public function getPath(string $path, string $pathHelper, null|string $filename = null): string
    {
        // if the pathHelper is the default, get it from the config
        if ($pathHelper === 'default') {
            $pathHelper = config('iconamic.path_helper');
        }

        // build the directory based off of the configured path and path helper
        switch ($pathHelper) {
            case 'app_path':
                $directory = app_path($path);
                break;
            case 'base_path':
                $directory = base_path($path);
                break;
            case 'public_path':
                $directory = public_path($path);
                break;
            case 'resource_path':
                $directory = resource_path($path);
                break;
            case 'storage_path':
                $directory = storage_path($path);
                break;
            case null:
            case '':
                throw IconamicException::missingPathHelper();
            default:
                throw IconamicException::unknownPathHelper($pathHelper);
        }

        $path = explode('/', $filename);
        $stack = array();
        foreach ($path as $seg) {
            if ($seg == '..') {
                // Ignore this segment, remove last segment from stack
                array_pop($stack);
                continue;
            }

            if ($seg == '.') {
                // Ignore this segment
                continue;
            }

            $stack[] = $seg;
        }

        $cleanFilename = implode('/', $stack);

        if ($cleanFilename) {
            // return the full path with the filename
            return $directory.DIRECTORY_SEPARATOR.$cleanFilename;
        } else {
            // return the full path with no filename
            return $directory;
        }
    }
}
