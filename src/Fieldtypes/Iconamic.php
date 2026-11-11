<?php

namespace MityDigital\Iconamic\Fieldtypes;

use Illuminate\Support\Facades\URL;
use MityDigital\Iconamic\Facades\Iconamic as IconamicFacade;
use Statamic\Facades\Blink;
use Statamic\Fields\Fieldtype;

class Iconamic extends Fieldtype
{
    protected $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="6" d="M19 9h19.84c2.76 0 5 2.24 5 5v24.84c0 2.76-2.24 5-5 5H14c-2.76 0-5-2.24-5-5V19c0-5.52 4.48-10 10-10Zm42.16 0H81c5.52 0 10 4.48 10 10v19.84c0 2.76-2.24 5-5 5H61.16c-2.76 0-5-2.24-5-5V14c0-2.76 2.24-5 5-5ZM14 56.16h24.84c2.76 0 5 2.24 5 5V86c0 2.76-2.24 5-5 5H19c-5.52 0-10-4.48-10-10V61.16c0-2.76 2.24-5 5-5Zm47.16 0H86c2.76 0 5 2.24 5 5V81c0 5.52-4.48 10-10 10H61.16c-2.76 0-5-2.24-5-5V61.16c0-2.76 2.24-5 5-5Z"/></svg>';

    protected $categories = ['media', 'special'];

    /**
     * Pass the fieldtype component a URL for the icon list rather than the list itself
     *
     * Statamic calls preload() once per field instance, so embedding every SVG here sent the whole icon set once per
     * field: an entry with many icon fields (e.g. inside replicator sets) carried dozens of identical copies. The
     * component fetches the list from the URL once per page and shares it between fields. The URL is signed, so the
     * endpoint only lists folders a field is configured for, and deterministic, so every field using the same folder
     * gets the same URL and the browser can cache it.
     *
     * @return array[]
     */
    public function preload(): array
    {
        $source = $this->iconSource();

        return [
            'url' => URL::signedRoute('statamic.cp.iconamic.icons', $source, null, false),
            'selected' => $this->selectedIcons($source),
        ];
    }

    /**
     * The folder settings for this field: path, path helper and recursive mode
     */
    protected function iconSource(): array
    {
        return [
            'path' => $this->config('path', config('iconamic.path')),
            'path_helper' => $this->config('path_helper', 'default'),
            'recursive' => (string) $this->config('recursive', 'default'),
        ];
    }

    /**
     * The SVG for each currently selected icon, so the field can show its value before the list has loaded
     */
    protected function selectedIcons(array $source): array
    {
        $value = $this->field?->value();
        $names = array_filter(is_array($value) ? $value : [$value], fn ($name) => is_string($name) && $name !== '');

        $selected = [];
        foreach ($names as $index => $name) {
            $path = IconamicFacade::getPath($source['path'], $source['path_helper'], $name.'.svg');
            if (is_file($path)) {
                $selected[$name] = IconamicFacade::cleanSvg(file_get_contents($path), $index);
            }
        }

        return $selected;
    }

    public function preProcessIndex($value)
    {
        $path = IconamicFacade::getPath($this->config('path', config('iconamic.path')),
            $this->config('path_helper', 'default'), $value.'.svg');

        try {
            $svg = file_get_contents($path);

            // get index
            $index = Blink::get('iconamic-index-fieldtype', 0);
            $index++;

            // clean the svg markup to prevent duplicate IDs
            $svg = IconamicFacade::cleanSvg($svg, $index);

            // set index
            Blink::put('iconamic-index-fieldtype', $index);

            return $svg;
        } catch (\ErrorException $e) {
            return $value;
        }
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
                        'instructions' => __('iconamic::fieldtype.config.core.path.instructions',
                            ['path' => config('iconamic.path')]),
                        'type' => 'text',
                        'default' => null,
                        'placeholder' => config('iconamic.path'),
                    ],
                    'path_helper' => [
                        'display' => __('iconamic::fieldtype.config.core.path_helper.display'),
                        'instructions' => __('iconamic::fieldtype.config.core.path_helper.instructions',
                            ['path_helper' => config('iconamic.path_helper')]),
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
                        'instructions' => config('iconamic.recursive')
                            ? __('iconamic::fieldtype.config.core.recursive.instructions.1')
                            : __('iconamic::fieldtype.config.core.recursive.instructions.0'),
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
