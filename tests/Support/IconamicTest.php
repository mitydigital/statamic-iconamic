<?php

use MityDigital\Iconamic\Exceptions\IconamicException;
use MityDigital\Iconamic\Facades\Iconamic;

it('get path successfully with app_path', function () {
    $this->assertEquals(app_path('svg'), Iconamic::getPath('svg', 'app_path'));
});

it('get path successfully with base_path', function () {
    $this->assertEquals(base_path('svg'), Iconamic::getPath('svg', 'base_path'));
});

it('get path successfully with public_path', function () {
    $this->assertEquals(public_path('svg'), Iconamic::getPath('svg', 'public_path'));
});

it('get path successfully with resource_path', function () {
    $this->assertEquals(resource_path('svg'), Iconamic::getPath('svg', 'resource_path'));
});

it('get path successfully with storage_path', function () {
    $this->assertEquals(storage_path('svg'), Iconamic::getPath('svg', 'storage_path'));
});

it('throws an exception with no path helper', function () {
    Iconamic::getPath('svg', '');
})->throws(IconamicException::class);

it('throws an exception with some other path helper', function () {
    Iconamic::getPath('svg', 'some_other');
})->throws(IconamicException::class);

it('can clean svg correctly updating the id and usages', function () {
    // id
    $this->assertEquals('<svg id="iconamic-2-abc"/>', Iconamic::cleanSvg('<svg id="abc"></svg>', 2));

    // xlink:href=#
    $this->assertEquals(
        '<svg id="iconamic-4-abc"><defs><linearGradient id="iconamic-4-b" x1="125.8" x2="125.8" y2="16.02" xlink:href="#iconamic-4-a"/></defs></svg>',
        Iconamic::cleanSvg('<svg id="abc"><defs><linearGradient id="b" x1="125.8" x2="125.8" y2="16.02" xlink:href="#a"/></defs></svg>',
            4));

    // url(#
    $this->assertEquals(
        '<svg id="iconamic-6-abc"><path fill="url(#iconamic-6-c)"/></svg>',
        Iconamic::cleanSvg('<svg id="abc"><path fill="url(#c)"></path></svg>', 6));
});

it('can clean svg and apply additional attributes', function () {
    // no change with no attributes set
    $this->assertEquals('<svg/>', Iconamic::cleanSvg('<svg></svg>', 2, []));

    // class gets appended to
    // simple add
    $this->assertEquals(
        '<svg class="w-4 h-4"/>',
        Iconamic::cleanSvg('<svg></svg>', 2, [
            'class' => 'w-4 h-4',
        ]));

    // update existing class
    $this->assertEquals(
        '<svg class="w-4 h-4 text-blue-500"/>',
        Iconamic::cleanSvg('<svg class="w-4 h-4"></svg>', 2, [
            'class' => 'text-blue-500',
        ]));

    // replace existing class
    $this->assertEquals(
        '<svg class="w-8 h-8"/>',
        Iconamic::cleanSvg('<svg class="w-4 h-4"></svg>', 2, [
            'classReplace' => 'w-8 h-8',
        ]));

    // replace and append class
    $this->assertEquals(
        '<svg class="w-8 h-8 text-blue-500"/>',
        Iconamic::cleanSvg('<svg class="w-4 h-4"></svg>', 2, [
            'class' => 'text-blue-500',
            'classReplace' => 'w-8 h-8',
        ]));

    // replace and append, order does not matter
    $this->assertEquals(
        '<svg class="w-8 h-8 text-blue-500"/>',
        Iconamic::cleanSvg('<svg class="w-4 h-4"></svg>', 2, [
            'classReplace' => 'w-8 h-8',
            'class' => 'text-blue-500',
        ]));

    // others get added only
    $this->assertEquals(
        '<svg class="w-4 h-4" monkey="tail"/>',
        Iconamic::cleanSvg('<svg class="w-4 h-4"></svg>', 2, [
            'monkey' => 'tail',
        ]));

    // id is updated accordingly by the cleaner, and left as is
    $this->assertEquals(
        '<svg id="iconamic-2-svg" class="w-4 h-4"/>',
        Iconamic::cleanSvg('<svg id="svg" class="w-4 h-4"></svg>', 2, [
            // notice no id is set - let the cleaner do its thing
            // 'id' => 'identification'
        ]));

    // id can be forcably changed
    $this->assertEquals(
        '<svg id="identification" class="w-4 h-4"/>',
        Iconamic::cleanSvg('<svg id="svg" class="w-4 h-4"></svg>', 2, [
            'id' => 'identification',
        ]));

    // class and basic addition
    $this->assertEquals(
        '<svg class="w-6 h-6 text-blue-500" monkey="tail" pizza="crust"/>',
        Iconamic::cleanSvg('<svg class="w-4 h-4"></svg>', 2, [
            'classReplace' => 'w-6 h-6',
            'class' => 'text-blue-500',
            'monkey' => 'tail',
            'pizza' => 'crust',
        ]));
});
