<?php

namespace MityDigital\Iconamic;

use MityDigital\Iconamic\Fieldtypes\Iconamic as IconamicFieldtype;
use MityDigital\Iconamic\Tags\Iconamic as IconamicTag;
use MityDigital\Iconamic\UpdateScripts\v1_0_2\MoveConfigFile;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        IconamicFieldtype::class
    ];

    protected $tags = [
        IconamicTag::class
    ];

    protected $updateScripts = [
        // v1.0.2
        MoveConfigFile::class
    ];

    protected $vite = [
        'input' => [
            'resources/js/index.js',
        ],
        'publicDirectory' => 'resources/dist',
    ];
}
