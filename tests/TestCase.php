<?php

namespace MityDigital\Iconamic\Tests;

use Illuminate\Support\Facades\File;
use MityDigital\Iconamic\Facades\Iconamic;
use MityDigital\Iconamic\ServiceProvider as IconamicServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Statamic\Assets\AssetContainer;
use Statamic\Providers\StatamicServiceProvider;
use Statamic\Statamic;

abstract class TestCase extends OrchestraTestCase
{
    /** @var AssetContainer */
    protected AssetContainer $assetContainer;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'iconamic' => [
                'path'        => 'svg',
                'path_helper' => 'base_path'
            ]
        ]);

        File::copyDirectory(__DIR__.'/TestSupport/svg', base_path('svg'));

        File::makeDirectory(resource_path('icon-svg'), 0755);
        File::copy(__DIR__.'/TestSupport/svg/square.svg', resource_path('icon-svg').'/square.svg');
        File::copy(__DIR__.'/TestSupport/svg/x.svg', resource_path('icon-svg').'/x.svg');
    }

    /**
     * Cleanup the test environment
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        File::deleteDirectory(base_path('svg'));
        File::deleteDirectory(resource_path('icon-svg'));
    }

    /**
     * Set up package aliases
     */
    protected function getPackageAliases($app)
    {
        return [
            'Statamic' => Statamic::class,
        ];
    }

    /**
     * Set up package providers
     */
    protected function getPackageProviders($app)
    {
        return [
            StatamicServiceProvider::class,
            IconamicServiceProvider::class,
        ];
    }

    /**
     * Set up package aliases
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->make(\Statamic\Extend\Manifest::class)->manifest = [
            'mitydigital/iconamic' => [
                'id'        => 'mitydigital/iconamic',
                'namespace' => 'MityDigital\\Iconamic\\',
            ],
        ];
    }

    /**
     * Get the SVG source for a given icon and index
     */
    protected function getSvgSource($file, $index)
    {
        return Iconamic::cleanSvg(file_get_contents(__DIR__.'/TestSupport/svg/'.$file.'.svg'), $index);
    }
}
