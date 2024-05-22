<?php

namespace MityDigital\Iconamic\Fieldtypes;

use DirectoryIterator;
use MityDigital\Iconamic\Facades\Iconamic as IconamicFacade;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Statamic\Fields\Fieldtype;

class Iconamic extends Fieldtype
{
    protected $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="6" d="M19 9h19.84c2.76 0 5 2.24 5 5v24.84c0 2.76-2.24 5-5 5H14c-2.76 0-5-2.24-5-5V19c0-5.52 4.48-10 10-10Zm42.16 0H81c5.52 0 10 4.48 10 10v19.84c0 2.76-2.24 5-5 5H61.16c-2.76 0-5-2.24-5-5V14c0-2.76 2.24-5 5-5ZM14 56.16h24.84c2.76 0 5 2.24 5 5V86c0 2.76-2.24 5-5 5H19c-5.52 0-10-4.48-10-10V61.16c0-2.76 2.24-5 5-5Zm47.16 0H86c2.76 0 5 2.24 5 5V81c0 5.52-4.48 10-10 10H61.16c-2.76 0-5-2.24-5-5V61.16c0-2.76 2.24-5 5-5Z"/></svg>';

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

                if (! $fileinfo->isDot() && $fileinfo->isFile() && $fileinfo->getExtension() == 'svg') {

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
            'icons' => $icons,
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
            [
                'display' => __('iconamic::fieldtype.config.core.title'),
                'fields' => [
                    'path' => [
                        'display' => __('iconamic::fieldtype.config.core.path.display'),
                        'instructions' => __('iconamic::fieldtype.config.core.path.instructions', ['path' => config('iconamic.path')]),
                        'type' => 'text',
                        'default' => null,
                        'placeholder' => config('iconamic.path'),
                    ],
                    'path_helper' => [
                        'display' => __('iconamic::fieldtype.config.core.path_helper.display'),
                        'instructions' => __('iconamic::fieldtype.config.core.path_helper.instructions', ['path_helper' => config('iconamic.path_helper')]),
                        'type' => 'select',
                        'default' => 'default',
                        'options' => [
                            'default' => __('iconamic::fieldtype.config.core.path_helper.options.default'),
                            'app_path' => __('iconamic::fieldtype.config.core.path_helper.options.app_path'),
                            'base_path' => __('iconamic::fieldtype.config.core.path_helper.options.base_path'),
                            'public_path' => __('iconamic::fieldtype.config.core.path_helper.options.public_path'),
                            'resource_path' => __('iconamic::fieldtype.config.core.path_helper.options.resource_path'),
                            'storage_path' => __('iconamic::fieldtype.config.core.path_helper.options.storage_path'),
                        ],
                    ],
                    'recursive' => [
                        'display' => __('iconamic::fieldtype.config.core.recursive.display'),
                        'instructions' => config('iconamic.recursive') ? __('iconamic::fieldtype.config.core.recursive.instructions.1') : __('iconamic::fieldtype.config.core.recursive.instructions.0'),
                        'type' => 'select',
                        'default' => 'default',
                        'options' => [
                            'default' => __('iconamic::fieldtype.config.core.recursive.options.default'),
                            'false' => __('iconamic::fieldtype.config.core.recursive.options.false'),
                            'true' => __('iconamic::fieldtype.config.core.recursive.options.true'),
                        ],
                    ],
                ],
            ],
            [
                'display' => __('iconamic::fieldtype.config.selection.title'),
                'fields' => [
                    'placeholder' => [
                        'display' => __('iconamic::fieldtype.config.selection.placeholder.display'),
                        'instructions' => __('iconamic::fieldtype.config.selection.placeholder.instructions'),
                        'type' => 'text',
                        'default' => '',
                    ],
                    'clearable' => [
                        'display' => __('iconamic::fieldtype.config.selection.clearable.display'),
                        'instructions' => __('iconamic::fieldtype.config.selection.clearable.instructions'),
                        'type' => 'toggle',
                        'default' => false,
                    ],
                    'searchable' => [
                        'display' => __('iconamic::fieldtype.config.selection.searchable.display'),
                        'instructions' => __('iconamic::fieldtype.config.selection.searchable.instructions'),
                        'type' => 'toggle',
                        'default' => true,
                    ],
                ],
            ],
            [
                'display' => __('iconamic::fieldtype.config.data.title'),
                'fields' => [
                    'default' => [
                        'display' => __('iconamic::fieldtype.config.data.default.display'),
                        'instructions' => __('iconamic::fieldtype.config.data.default.instructions'),
                        'type' => 'text',
                    ],
                ],
            ],
        ];
    }
}
