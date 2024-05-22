<?php

return [

    'config' => [
        'core' => [
            'title' => 'Iconamic Configuration',

            'path' => [
                'display' => 'Path Override',
                'instructions' => 'Your default is configured to be ":path".',
            ],

            'path_helper' => [
                'display' => 'Path Helper Override',
                'instructions' => 'Your default is configured to be ":path_helper".',

                'options' => [
                    'default' => 'Use Default',
                    'app_path' => 'App (app_path)',
                    'base_path' => 'Base (base_path)',
                    'public_path' => 'Public (public_path)',
                    'resource_path' => 'Resource (resource_path)',
                    'storage_path' => 'Storage (storage_path)',
                ],
            ],

            'recursive' => [
                'display' => 'Recursively list icons?',
                'instructions' => [
                    '0' => 'Your default is configured to list within the path only',
                    '1' => 'Your default is configured to recursively list icons within the path.',
                ],

                'options' => [
                    'default' => 'Use Default',
                    'false' => 'Recursive mode disabled',
                    'true' => 'Recursive mode enabled',
                ],
            ],
        ],

        'selection' => [
            'title' => 'Selection',

            'placeholder' => [
                'display' => 'Placeholder',
                'instructions' => 'Set placeholder text.',
            ],

            'clearable' => [
                'display' => 'Clearable',
                'instructions' => 'Enable to allow deselecting your icon',
            ],

            'searchable' => [
                'display' => 'Searchable',
                'instructions' => 'Enable searching through possible icons.',
            ],
        ],

        'data' => [
            'title' => 'Data',

            'default' => [
                'display' => 'Default Value',
                'instructions' => 'The icon to be selected whenever this field is empty on all publish forms.',
            ],
        ],
    ],

];
