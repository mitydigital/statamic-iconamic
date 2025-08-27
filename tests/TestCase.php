<?php

namespace MityDigital\Iconamic\Tests;

use Illuminate\Support\Facades\File;
use MityDigital\Iconamic\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected $shouldFakeVersion = true;

    protected string $addonServiceProvider = ServiceProvider::class;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        config([
            'iconamic' => [
                'path' => 'svg',
                'path_helper' => 'base_path',
            ],
        ]);

        File::copyDirectory(__DIR__.'/__fixtures__/resources/svg', base_path('svg'));

        File::makeDirectory(resource_path('icon-svg'), 0755);
        File::copy(__DIR__.'/__fixtures__/resources/svg/square.svg', resource_path('icon-svg').'/square.svg');
        File::copy(__DIR__.'/__fixtures__/resources/svg/x.svg', resource_path('icon-svg').'/x.svg');
        File::copy(__DIR__.'/__fixtures__/resources/svg/simple.svg', resource_path('icon-svg').'/simple.svg');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('svg'));
        File::deleteDirectory(resource_path('icon-svg'));

        parent::tearDown();
    }
}
