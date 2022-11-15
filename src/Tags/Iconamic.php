<?php

namespace MityDigital\Iconamic\Tags;

use MityDigital\Iconamic\Exceptions\IconamicException;
use MityDigital\Iconamic\Facades\Iconamic as IconamicFacade;
use Statamic\Facades\Blink;
use Statamic\Tags\Tags;

class Iconamic extends Tags
{
    /**
     * The {{ iconamic }} tag.
     *
     * The simplest usage of the Iconamic tag. Simply suck in an icon and output its markup.
     *
     * Will look for a field called "icon" in your content.
     * You can specify what field to use by passing a "handle" parameter to the tag.
     *
     * {{ iconamic }}
     *   Looks for a field called "icon", and uses that to get the icon
     *
     * {{ iconamic handle="my_field" }}
     *   Looks for a field called "my_field", and uses that to get the icon
     *
     * @return string
     * @throws IconamicException
     */
    public function index(): string
    {
        return $this->_getIcon();
    }

    /**
     * The actual logic for the tag all lives here, given all three access methods use it.
     *
     * It injects an icon from an SVG file in to the page output, and does some rudimentary processing on the SVG to
     * help prevent duplicate IDs in your markup.
     *
     * Basically we use the handle to get the field from the content, and if that all exists as expected, try to
     * locate and find the SVG icon.
     *
     * If found, it will return the icon (when $checkIconExistsOnly) is set to false (the default), or returns true
     * when $checkIconExistsOnly is set to true.
     *
     * Rather than throwing an exception if an icon is not found, it will quietly return an empty string (or false).
     * This is intended so that if an icon is removed, but content is updated, it won't crash the page load on a
     * production site.
     *
     * @param  string|null  $handle  The handle of the field to load. If null, will look for the "handle" param and try to use that instead.
     * @param  bool  $checkIconExistsOnly  When true, will only return a boolean response if found.
     *
     * @return string|bool
     *
     * @throws IconamicException
     */
    protected function _getIcon(string $handle = null, bool $checkIconExistsOnly = false): string|bool
    {
        // if there's no params or context, get out
        if (!$this->params || !$this->context) {
            return '';
        }

        if (is_null($handle)) {
            // by default let's look for a handle, or default to "icon"
            $handle = $this->params->get('handle', 'icon');
        }

        $icon = null;
        if ($this->params->has('icon') && $this->params->has('path')) {
            // do nothing, treat it as a manual
        } elseif (is_array($this->context) && array_key_exists($handle,
                $this->context) || isset($this->context[$handle])) {
            // if the context is NOT a string (if its a string, treat it as a manual one)
            if (!is_string($this->context[$handle])) {
                $icon = $this->context[$handle];
            }
        }

        // manual mode
        // is there an 'icon' and 'path' param?
        if (!$icon && $this->params->has('icon') && $this->params->has('path')) {
            // load the raw file
            $icon = $this->params->get('icon', false);
            $path = $this->params->get('path', '');

            // do we have a path helper?
            $pathHelper = $this->params->has('path_helper') ? $this->params->get('path_helper', 'default') : 'default';

            // if there's still no icon, then it doesn't exist
            if (!$icon) {
                if ($checkIconExistsOnly) {
                    return false;
                } else {
                    return '';
                }
            }
        } else {
            // if there's no icon or there's no fieldtype, quietly fail
            // (FYI, no fieldtype happens if the handle remains in content, but is no longer in the blueprint)
            if (!$icon || !$icon->fieldtype()) {
                if ($checkIconExistsOnly) {
                    return false;
                } else {
                    return '';
                }
            }

            // get the field
            $field = $icon->field();
            $config = $field->config();

            // what path do we want to look at?
            $path = $config['path'] ?? config('iconamic.path');

            // what helper do we want to use?
            $pathHelper = $config['path_helper'] ?? 'default';
        }

        // get the full path for the icon
        $path = IconamicFacade::getPath($path, $pathHelper, $icon.'.svg');

        if (file_exists($path)) {

            // if we are just checking for existence, return true - we found it!
            if ($checkIconExistsOnly) {
                return true;
            }

            // get the icon
            $svg = file_get_contents($path);

            // get index
            $index = Blink::get('iconamic-index', 0);
            $index++;

            // clean the svg markup to prevent duplicate IDs
            $svg = $this->updateSvgMarkup($svg, $index);

            // set index
            Blink::put('iconamic-index', $index);

            return $svg;
        } else {
            if ($checkIconExistsOnly) {
                return false;
            } else {
                return '';
            }
        }
    }

    /**
     * The {{ iconamic:has }} tag.
     *
     * Checks for whether the SVG icon exists or not, and returns a boolean response.
     *
     * Like index, will look for your "icon" field, or the field specified by the "handle" parameter.
     *
     * {{ iconamic:has }}
     *   Looks for a field called "icon", and uses that to check if it exists
     *
     * {{ iconamic:has handle="my_field" }}
     *   Looks for a field called "my_field", and uses that to check if it exists
     *
     * @return bool
     * @throws IconamicException
     */
    public function has(): bool
    {
        return $this->_getIcon(null, true);
    }

    /**
     * Processes the SVG markup to replace common patterns regarding ID usage.
     *
     * This helps keep duplicate IDs out of your finished markup.
     *
     * If you need to perform some different processing, you may need to override this function with your own tag that
     * extends this tag (to keep the other logic).
     *
     * @param  string  $svg  The SVG markup to process
     * @param  int  $index  The index to use to keep IDs unique
     *
     * @return string
     */
    protected function updateSvgMarkup(string $svg, int $index): string
    {
        // exclude these params
        $exclude = [
            'icon', 'handle', 'path_helper', 'path'
        ];

        $attributes = [];
        foreach ($this->params as $param => $value) {
            if (!in_array($param, $exclude)) {
                $attributes[$param] = $value;
            }
        }

        return IconamicFacade::cleanSvg($svg, $index, $attributes);
    }

    /**
     * The {{ iconamic:* }} tag.
     *
     * Who needs to specify a handle when you can use the wildcard approach? Basically this is shorthand of the index.
     *
     * {{ iconamic:my_field }}
     *   Looks for a field called "my_field", and uses that to get the icon
     *
     * @param  string  $handle  The handle to use as the source for the icon
     *
     * @return string
     * @throws IconamicException
     */
    public function wildcard(string $handle): string
    {
        return $this->_getIcon($handle);
    }
}
