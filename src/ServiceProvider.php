<?php

namespace MityDigital\Iconamic;

use MityDigital\Iconamic\Fieldtypes\Iconamic as IconamicFieldtype;
use MityDigital\Iconamic\Tags\Iconamic as IconamicTag;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        IconamicFieldtype::class
    ];

    protected $scripts = [
        __DIR__.'/../dist/js/iconamic.js'
    ];

    protected $tags = [
        IconamicTag::class
    ];

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/iconamic.php', 'statamic.iconamic');

        $this->publishes([
            __DIR__.'/../config/iconamic.php' => config_path('statamic/iconamic.php')
        ], 'iconamic-config');
    }
}
