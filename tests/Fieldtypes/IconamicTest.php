<?php

use MityDigital\Iconamic\Fieldtypes\Iconamic as IconamicFieldtype;
use Statamic\Fields\Field;

beforeEach(function () {
    $this->fieldtype = new IconamicFieldtype;
});

it('has path and path helper config fields', function () {
    $fields = $this->fieldtype->configFields();

    // have the required fields
    $this->assertEquals(
        [
            'path',
            'path_helper',
            'recursive',
            'placeholder',
            'clearable',
            'searchable',
            'default',
        ],
        $fields->values()->keys()->toArray()
    );

    // path is type text
    $this->assertEquals('text', $fields->get('path')->config()['type']);

    // path helper is a select with the valid options
    $this->assertEquals('select', $fields->get('path_helper')->config()['type']);
    $this->assertEquals([
        'default',
        'app_path',
        'base_path',
        'public_path',
        'resource_path',
        'storage_path',
    ], array_keys($fields->get('path_helper')->config()['options']));
});

it('preloads a signed url for the icon list instead of the icons', function () {
    $meta = $this->fieldtype->preload();

    $this->assertArrayNotHasKey('icons', $meta);
    $this->assertStringStartsWith('/', $meta['url']);
    $this->assertStringContainsString('path=svg', $meta['url']);
    $this->assertStringContainsString('signature=', $meta['url']);
});

it('preloads the field override folder in the icon list url', function () {
    $this->fieldtype->setField(new Field('icon', [
        'path' => 'icon-svg',
        'path_helper' => 'resource_path',
    ]));

    $meta = $this->fieldtype->preload();

    $this->assertStringContainsString('path=icon-svg', $meta['url']);
    $this->assertStringContainsString('path_helper=resource_path', $meta['url']);
});

it('preloads the same url for every field using the same folder', function () {
    $first = (new IconamicFieldtype)->setField(new Field('first', []))->preload();
    $second = (new IconamicFieldtype)->setField(new Field('second', []))->preload();

    $this->assertEquals($first['url'], $second['url']);
});

it('preloads the svg of the selected icon', function () {
    $this->fieldtype->setField((new Field('icon', []))->setValue('square'));

    $meta = $this->fieldtype->preload();

    $this->assertEquals(['square'], array_keys($meta['selected']));
    $this->assertStringStartsWith('<svg', $meta['selected']['square']);
});

it('preloads nothing selected for an empty or missing icon', function ($value) {
    $this->fieldtype->setField((new Field('icon', []))->setValue($value));

    $this->assertEquals([], $this->fieldtype->preload()['selected']);
})->with([null, '', 'not-an-icon']);
