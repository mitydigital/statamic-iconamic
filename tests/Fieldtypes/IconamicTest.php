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

it('can get a list of icons using defaults in preload', function () {
    $icons = $this->fieldtype->preload();

    $this->assertArrayHasKey('icons', $icons);
    $this->assertCount(5, $icons['icons']);
});

it('can get a list of icons using field overrides', function () {
    $field = new Field(
        'icon',
        [
            'path' => 'icon-svg',
            'path_helper' => 'resource_path',
        ]
    );

    $this->fieldtype->setField($field);

    $icons = $this->fieldtype->preload();

    $this->assertArrayHasKey('icons', $icons);
    $this->assertCount(3, $icons['icons']);
});
