<?php

namespace MityDigital\Iconamic\Tests\Support;

use MityDigital\Iconamic\Exceptions\IconamicException;
use MityDigital\Iconamic\Tests\TestCase;
use MityDigital\Iconamic\Facades\Iconamic;

class IconamicSupportTest extends TestCase
{

    /** @test */
    public function get_path_successful_with_app_path()
    {
        $this->assertEquals(app_path('svg'), Iconamic::getPath('svg', 'app_path'));
    }

    /** @test */
    public function get_path_successful_with_base_path()
    {
        $this->assertEquals(base_path('svg'), Iconamic::getPath('svg', 'base_path'));
    }

    /** @test */
    public function get_path_successful_with_public_path()
    {
        $this->assertEquals(public_path('svg'), Iconamic::getPath('svg', 'public_path'));
    }

    /** @test */
    public function get_path_successful_with_resource_path()
    {
        $this->assertEquals(resource_path('svg'), Iconamic::getPath('svg', 'resource_path'));
    }

    /** @test */
    public function get_path_successful_with_storage_path()
    {
        $this->assertEquals(storage_path('svg'), Iconamic::getPath('svg', 'storage_path'));
    }

    /** @test */
    public function get_path_exception_with_no_path_helper()
    {
        $this->expectException(IconamicException::class);
        Iconamic::getPath('svg', '');
    }

    /** @test */
    public function get_path_exception_with_some_other_path_helper()
    {
        $this->expectException(IconamicException::class);
        Iconamic::getPath('svg', 'some_other');
    }

    /** @test */
    public function clean_svg_correctly_updates_id_and_usages()
    {
        // id
        $this->assertEquals('<svg id="iconamic-2-abc"></svg>', Iconamic::cleanSvg('<svg id="abc"></svg>', 2));

        // xlink:href=#
        $this->assertEquals(
            '<svg id="iconamic-4-abc"><defs><linearGradient id="iconamic-4-b" x1="125.8" x2="125.8" y2="16.02" xlink:href="#iconamic-4-a"/></defs></svg>',
            Iconamic::cleanSvg('<svg id="abc"><defs><linearGradient id="b" x1="125.8" x2="125.8" y2="16.02" xlink:href="#a"/></defs></svg>',
                4));

        // url(#
        $this->assertEquals(
            '<svg id="iconamic-6-abc"><path fill="url(#iconamic-6-c)"></path></svg>',
            Iconamic::cleanSvg('<svg id="abc"><path fill="url(#c)"></path></svg>', 6));
    }
}
