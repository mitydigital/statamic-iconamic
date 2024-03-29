<?php

namespace MityDigital\Iconamic\Fieldtypes;

use DirectoryIterator;
use MityDigital\Iconamic\Facades\Iconamic as IconamicFacade;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Statamic\Fields\Fieldtype;

class Iconamic extends Fieldtype
{
    protected $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path d="M19 9h19.84c2.76 0 5 2.24 5 5v24.84c0 2.76-2.24 5-5 5H14c-2.76 0-5-2.24-5-5V19c0-5.52 4.48-10 10-10ZM61.16 9H81c5.52 0 10 4.48 10 10v19.84c0 2.76-2.24 5-5 5H61.16c-2.76 0-5-2.24-5-5V14c0-2.76 2.24-5 5-5ZM14 56.16h24.84c2.76 0 5 2.24 5 5V86c0 2.76-2.24 5-5 5H19c-5.52 0-10-4.48-10-10V61.16c0-2.76 2.24-5 5-5ZM61.16 56.16H86c2.76 0 5 2.24 5 5V81c0 5.52-4.48 10-10 10H61.16c-2.76 0-5-2.24-5-5V61.16c0-2.76 2.24-5 5-5Z" style="fill:none;stroke:#000;stroke-linecap:round;stroke-miterlimit:10;stroke-width:6px"/></svg>';

    protected $categories = ['media', 'special'];

    /**
     * Get the icons as an array to pass to the fieldtype component
     *
     * @return array[]
     */
    public function preload(): array
    {
        // what path do we want to look at?
        $path = $this->config('path', config('iconamic.path'));

        // what helper do we want to use?
        $pathHelper = $this->config('path_helper', 'default');

        $enableRecursiveMode = false;
        switch ($this->config('recursive', 'default')) {
            case 'false':
                $enableRecursiveMode = false;
                break;
            case 'true':
                $enableRecursiveMode = true;
                break;
            default:
                $enableRecursiveMode = config('iconamic.recursive', false);
        }

        // load the icons for the select list
        $icons = [];

        if ($enableRecursiveMode) {
            // recursively list files
            $dir = new DirectoryIterator(IconamicFacade::getPath($path, $pathHelper));
            $path = IconamicFacade::getPath($path, $pathHelper);
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
            $index = 0;
            foreach ($files as $file) {
                if ($file->isFile() && $file->getExtension() == 'svg') {
                    // get the new name
                    $name = str_replace($path.'/', '', $file->getRealPath());

                    $key = $name;
                    if (str_ends_with($name, '.svg')) {
                        $key = substr($key, 0, -4);
                    }

                    $svg = file_get_contents($file->getRealPath());

                    // clean that svg (ick)
                    $icons[$key] = IconamicFacade::cleanSvg($svg, $index);

                    $index++; // increase the index
                }
            }
        } else {
            // the old way of doing it
            $dir = new DirectoryIterator(IconamicFacade::getPath($path, $pathHelper));
            $index = 0;
            foreach ($dir as $fileinfo) {

                if (!$fileinfo->isDot() && $fileinfo->isFile() && $fileinfo->getExtension() == 'svg') {

                    $name = str_replace($path.'/', '', $fileinfo->getBasename());
                    $key = $name;
                    if (str_ends_with($name, '.svg')) {
                        $key = substr($key, 0, -4);
                    }

                    $svg = file_get_contents($fileinfo->getRealPath());

                    // clean that svg (ick)
                    $icons[$key] = IconamicFacade::cleanSvg($svg, $index);

                    $index++; // increase the index
                }
            }
        }

        // sort by key
        ksort($icons);

        return [
            'icons' => $icons
        ];
    }

    /**
     * Each fieldtype will use the default configuration, but can be overridden for each usage.
     *
     * This allows you to set the standard default to be, for example "resources", but also have a different instance
     * that maybe uses a different path or different path helper. Just trying to be flexible for multiple use cases.
     *
     * @return array[]
     */
    protected function configFieldItems(): array
    {
        return [
            'path' => [
                'display' => 'Path Override',
                'instructions' => 'Your default is configured to be "'.config('iconamic.path').'".',
                'type' => 'text',
                'default' => null,
                'width' => 33,
                'placeholder' => config('iconamic.path')
            ],
            'path_helper' => [
                'display' => 'Path Helper Override',
                'instructions' => 'Your default is configured to be "'.config('iconamic.path_helper').'".',
                'type' => 'select',
                'default' => 'default',
                'options' => [
                    'default' => __('Use Default'),
                    'app_path' => __('App (app_path)'),
                    'base_path' => __('Base (base_path)'),
                    'public_path' => __('Public (public_path)'),
                    'resource_path' => __('Resource (resource_path)'),
                    'storage_path' => __('Storage (storage_path)'),
                ],
                'width' => 33
            ],
            'recursive' => [
                'display' => 'Recursively list icons?',
                'instructions' => 'Your default is configured to '.(config('iconamic.recursive') ? 'recursively list icons within the path' : 'list within the path only').'.',
                'type' => 'select',
                'default' => 'default',
                'options' => [
                    'default' => __('Use Default'),
                    'false' => __('Recursive mode disabled'),
                    'true' => __('Recursive mode enabled')
                ],
                'width' => 33
            ],
        ];
    }
}
